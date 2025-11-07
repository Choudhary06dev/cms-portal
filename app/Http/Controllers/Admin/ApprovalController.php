<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpareApprovalPerforma;
use App\Models\SpareApprovalItem;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Spare;
use App\Models\ComplaintCategory;
use App\Models\StockApprovalData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Traits\LocationFilterTrait;

class ApprovalController extends Controller
{
    use LocationFilterTrait;
    
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of approval performas
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Automatically create missing approval performas for complaints that don't have them
            // This ensures all complaints appear in the approval modal
            // Only create if approval doesn't already exist to avoid duplicates
            $complaintsWithoutApprovalsQuery = Complaint::whereDoesntHave('spareApprovals');
            $this->filterComplaintsByLocation($complaintsWithoutApprovalsQuery, $user);
            $complaintsWithoutApprovals = $complaintsWithoutApprovalsQuery->get();
            if ($complaintsWithoutApprovals->count() > 0) {
                $defaultEmployee = Employee::first();
                if ($defaultEmployee) {
                    foreach ($complaintsWithoutApprovals as $complaint) {
                        try {
                            // Double check if approval doesn't exist (race condition prevention)
                            $existingApproval = SpareApprovalPerforma::where('complaint_id', $complaint->id)->first();
                            if ($existingApproval) {
                                continue; // Skip if approval already exists
                            }
                            
                            $requestedByEmployee = $complaint->assigned_employee_id 
                                ? Employee::find($complaint->assigned_employee_id)
                                : $defaultEmployee;
                            
                            if (!$requestedByEmployee) {
                                $requestedByEmployee = $defaultEmployee;
                            }
                            
                            SpareApprovalPerforma::create([
                                'complaint_id' => $complaint->id,
                                'requested_by' => $requestedByEmployee->id,
                                'status' => 'pending',
                                'remarks' => 'Auto-created for existing complaint',
                            ]);
                        } catch (\Exception $e) {
                            \Log::warning('Failed to create approval performa for complaint: ' . $complaint->id, [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            // Start with base query and join complaints table for filtering/ordering
            // Use distinct to avoid duplicates from joins
            $query = SpareApprovalPerforma::query()
                ->join('complaints', 'spare_approval_performa.complaint_id', '=', 'complaints.id')
                ->join('clients', 'complaints.client_id', '=', 'clients.id')
                ->select('spare_approval_performa.*')
                ->distinct();
            
            // Apply location-based filtering through complaint relationship
            if (!$this->canViewAllData($user)) {
                $query->whereHas('complaint', function($q) use ($user) {
                    $this->filterComplaintsByLocation($q, $user);
                });
            }

            // Search functionality - by Complaint ID, Address, or Cell No (phone)
            if ($request->has('search') && $request->search) {
                $search = trim($request->search);
                if (!empty($search)) {
                    $query->where(function($q) use ($search) {
                        $q->where('complaints.id', 'like', "%{$search}%")
                          ->orWhere('clients.client_name', 'like', "%{$search}%")
                          ->orWhere('clients.address', 'like', "%{$search}%")
                          ->orWhere('clients.phone', 'like', "%{$search}%")
                          ->orWhere('complaints.title', 'like', "%{$search}%");
                    });
                }
            }

            // Filter by Complaint Registration Date (From Date)
            if ($request->has('complaint_date') && $request->complaint_date) {
                $query->whereDate('complaints.created_at', '>=', $request->complaint_date);
            }

            // Filter by End Date (To Date) - works even when category is not selected
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('complaints.created_at', '<=', $request->date_to);
            }

            // Filter by Nature (category)
            if ($request->has('category') && $request->category) {
                $query->where('complaints.category', $request->category);
            }

            // Filter by complaint status (same as status column dropdown)
            if ($request->has('status') && $request->status) {
                $query->where('complaints.status', $request->status);
            }

            // Filter by requester or by complaint's assigned employee (using the same requested_by param)
            if ($request->has('requested_by') && $request->requested_by) {
                $employeeId = $request->requested_by;
                $query->where(function($q) use ($employeeId) {
                    $q->where('spare_approval_performa.requested_by', $employeeId)
                      ->orWhere('complaints.assigned_employee_id', $employeeId);
                });
            }

            // Filter by complaint
            if ($request->has('complaint_id') && $request->complaint_id) {
                $query->where('spare_approval_performa.complaint_id', $request->complaint_id);
            }

            // Filter by date range (for approval creation date) - only if not using complaint date filters
            if ($request->has('date_from') && $request->date_from && !$request->has('complaint_date') && !$request->has('date_to')) {
                $query->whereDate('spare_approval_performa.created_at', '>=', $request->date_from);
            }

            // Order by approval ID (descending) - newest first
            $query->orderBy('spare_approval_performa.id', 'desc');
            
            $approvals = $query->paginate(15);
            
            // Reload relationships after join (join may have affected eager loading)
            $approvals->load([
                'complaint.client',
                'complaint.assignedEmployee',
                'complaint.spareParts.spare',
                'requestedBy',
                'approvedBy',
                'items.spare'
            ]);
            
            // Get complaints with location filtering
            $complaintsQuery = Complaint::pending()->with('client');
            $this->filterComplaintsByLocation($complaintsQuery, $user);
            $complaints = $complaintsQuery->get();
            
            // Get employees with location filtering
            $employeesQuery = Employee::where('status', 'active');
            $this->filterEmployeesByLocation($employeesQuery, $user);
            $employees = $employeesQuery->get();
            
            // Get categories for Nature filter - get from ComplaintCategory table if exists
            if (Schema::hasTable('complaint_categories')) {
                // Get categories from ComplaintCategory table
                $categories = ComplaintCategory::orderBy('name')->pluck('name');
            } else {
                // Fallback: Get categories from complaints that have approvals with location filtering
                $categoriesQuery = Complaint::join('spare_approval_performa', 'complaints.id', '=', 'spare_approval_performa.complaint_id')
                    ->join('clients', 'complaints.client_id', '=', 'clients.id')
                    ->select('complaints.category')
                    ->distinct()
                    ->whereNotNull('complaints.category');
                
                // Apply location filtering through client table
                if (!$this->canViewAllData($user)) {
                    $roleName = strtolower($user->role->role_name ?? '');
                    if ($roleName === 'garrison_engineer' && $user->city_id && $user->city) {
                        $categoriesQuery->where('clients.city', $user->city->name);
                    } elseif (in_array($roleName, ['complaint_center', 'department_staff']) && $user->sector_id && $user->sector) {
                        $categoriesQuery->where('clients.sector', $user->sector->name);
                    }
                }
                
                $categories = $categoriesQuery->pluck('category')
                    ->unique()
                    ->values();
                
                // If still empty, get from all complaints with location filtering
                if ($categories->isEmpty()) {
                    $categoriesQuery = Complaint::select('category')
                        ->distinct()
                        ->whereNotNull('category');
                    
                    // Apply location filtering
                    $this->filterComplaintsByLocation($categoriesQuery, $user);
                    
                    $categories = $categoriesQuery->pluck('category')
                        ->unique()
                        ->values();
                }
            }

            // Get unique complaint statuses from database (only existing ones) - same as status column dropdown
            $statuses = Complaint::select('status')
                ->distinct()
                ->whereNotNull('status')
                ->pluck('status')
                ->unique()
                ->values()
                ->mapWithKeys(function($status) {
                    $statusLabels = [
                        'assigned' => 'Assigned',
                        'in_progress' => 'In-Process',
                        'resolved' => 'Addressed',
                        'work_performa' => 'Work Performa',
                        'maint_performa' => 'Maint Performa',
                        'priced_performa' => 'Maint/Work Priced',
                        'product_na' => 'Product N/A',
                    ];
                    return [$status => $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))];
                });

            // Handle AJAX requests - return only table and pagination
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                try {
                    $html = view('admin.approvals.index', compact('approvals', 'complaints', 'employees', 'categories', 'statuses'))->render();
                    return response()->json([
                        'success' => true,
                        'html' => $html
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error rendering approvals view for AJAX', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'Error loading approvals: ' . $e->getMessage()
                    ], 500);
                }
            }

            return view('admin.approvals.index', compact('approvals', 'complaints', 'employees', 'categories', 'statuses'));
            
        } catch (\Exception $e) {
            \Log::error('Error in ApprovalController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            // Return error response for AJAX
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'error' => 'Error loading approvals: ' . $e->getMessage()
                ], 500);
            }
            
            // Return error view for regular requests
            return redirect()->route('admin.approvals.index')
                ->with('error', 'Error loading approvals: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified approval performa
     */
    public function show(SpareApprovalPerforma $approval)
    {
        $approval->load([
            'complaint.client',
            'complaint.spareParts.spare',
            'complaint.stockLogs.spare',
            'requestedBy',
            'approvedBy',
            'items.spare'
        ]);

        // Always sync approval items from complaint spare parts
        if ($approval->complaint) {
            try {
                // Load complaint spare parts with spare relationship
                $approval->complaint->loadMissing(['spareParts.spare']);
                
                if ($approval->complaint->spareParts->count() > 0) {
                    // Get existing approval items by spare_id
                    $existingItemsBySpareId = $approval->items->keyBy('spare_id');
                    
                    foreach ($approval->complaint->spareParts as $sp) {
                        if (!$sp->spare_id) continue;
                        
                        // Check if approval item already exists for this spare
                        $existingItem = $existingItemsBySpareId->get($sp->spare_id);
                        
                        if ($existingItem) {
                            // Update existing item if quantity changed (only if status is pending)
                            if ($approval->status === 'pending' && $existingItem->quantity_requested != $sp->quantity) {
                                $existingItem->update([
                                    'quantity_requested' => (int)($sp->quantity ?? 1),
                                    'reason' => 'Updated from complaint spare usage',
                                ]);
                            }
                        } else {
                            // Create new approval item if it doesn't exist
                            \App\Models\SpareApprovalItem::create([
                                'performa_id' => $approval->id,
                                'spare_id' => $sp->spare_id,
                                'quantity_requested' => (int)($sp->quantity ?? 1),
                                'quantity_approved' => null,
                                'reason' => 'Auto-imported from complaint spare usage',
                            ]);
                        }
                    }
                    
                    // Remove approval items that are no longer in complaint spare parts (only if status is pending)
                    if ($approval->status === 'pending') {
                        $complaintSpareIds = $approval->complaint->spareParts->pluck('spare_id')->filter()->toArray();
                        $approval->items()->whereNotIn('spare_id', $complaintSpareIds)->delete();
                    }
                    
                    // Reload items after sync
                    $approval->load(['items.spare']);
                }
            } catch (\Throwable $e) {
                \Log::warning('Failed to sync approval items in show()', [
                    'approval_id' => $approval->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Refresh approval to get latest remarks from database
        $approval->refresh();

        // Get previously issued stock from stock logs for this approval
        $issuedStock = [];
        try {
            $stockLogs = \App\Models\SpareStockLog::where('reference_id', $approval->id)
                ->where('change_type', 'out')
                ->with('spare')
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($stockLogs as $log) {
                if ($log->spare) {
                    $issuedStock[] = [
                        'spare_id' => $log->spare_id,
                        'spare_name' => $log->spare->item_name ?? 'N/A',
                        'quantity_issued' => (int)$log->quantity,
                        'issued_at' => $log->created_at ? $log->created_at->format('M d, Y H:i') : null,
                        'remarks' => $log->remarks ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch issued stock logs', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage()
            ]);
        }

        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'approval' => [
                    'id' => $approval->id,
                    'status' => $approval->status,
                    'created_at' => $approval->created_at ? $approval->created_at->format('M d, Y H:i') : null,
                    'approved_at' => $approval->approved_at ? $approval->approved_at->format('M d, Y H:i') : null,
                    'remarks' => $approval->remarks,
                    'complaint_id' => $approval->complaint_id,
                    'complaint' => $approval->complaint ? [
                        'id' => $approval->complaint->id,
                        'category' => $approval->complaint->category ?? null,
                        'title' => $approval->complaint->title ?? 'N/A',
                        'sector' => $approval->complaint->client->sector ?? null,
                        'city' => $approval->complaint->client->city ?? null,
                    ] : null,
                    'client_name' => $approval->complaint->client ? $approval->complaint->client->client_name : 'Deleted Client',
                    'complaint_title' => $approval->complaint->title ?? 'N/A',
                    'requested_by_name' => $approval->requestedBy->name ?? 'N/A',
                    'approved_by_name' => $approval->approvedBy->name ?? null,
                    'items' => $approval->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'spare_id' => $item->spare_id ?? null,
                            'spare_name' => $item->spare->item_name ?? 'N/A',
                            'category' => $item->spare->category ?? 'N/A',
                            'quantity_requested' => (int)$item->quantity_requested,
                            'quantity_approved' => $item->quantity_approved !== null ? (int)$item->quantity_approved : null,
                            'available_stock' => (int)($item->spare->stock_quantity ?? 0),
                            'unit_price' => $item->spare->unit_price ?? 0
                        ];
                    }),
                    'issued_stock' => $issuedStock
                ]
            ]);
        }

        return view('admin.approvals.show', compact('approval'));
    }

    /**
     * Approve the specified approval performa
     */
    public function approve(Request $request, SpareApprovalPerforma $approval)
    {
        // Load relationships
        $approval->load(['items.spare', 'complaint.client', 'requestedBy']);
        
        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        // Map of approved quantities from request (if provided per item)
        $approvedInput = collect($request->input('items', []))
            ->mapWithKeys(function($data, $key) {
                $id = (int)$key;
                $qty = isset($data['quantity_approved']) ? (int)$data['quantity_approved'] : null;
                return [$id => $qty];
            });

        // Check stock availability and adjust quantities if insufficient
        $unavailableItems = [];
        $adjustedItems = [];
        
        foreach ($approval->items as $item) {
            // Get spare directly to avoid relationship issues
            $spare = \App\Models\Spare::find($item->spare_id);
            
            if (!$spare) {
                \Log::error('Spare not found for item ID: ' . $item->id . ', Spare ID: ' . $item->spare_id);
                $unavailableItems[] = 'Unknown Spare (ID: ' . $item->spare_id . ')';
                continue;
            }
            
            $requestedQty = (int)$item->quantity_requested;
            $approvedQty = $approvedInput->get($item->id) !== null
                ? max(0, (int)$approvedInput->get($item->id))
                : $requestedQty; // Use requested if not specified
            
            $availableQty = (int)$spare->stock_quantity;
            
            // Adjust quantity to available stock if insufficient
            if ($availableQty < $approvedQty) {
                if ($availableQty > 0) {
                    // Give available quantity (partial approval)
                    $adjustedItems[] = [
                        'item_name' => $spare->item_name,
                        'requested' => $approvedQty,
                        'available' => $availableQty,
                        'item_id' => $item->id
                    ];
                    // Update approved quantity to available
                    $approvedInput[$item->id] = $availableQty;
                } else {
                    // No stock available at all
                    $unavailableItems[] = $spare->item_name . ' (Requested: ' . $approvedQty . ', Available: 0)';
                }
            }
        }
        
        // If items are completely unavailable (zero stock), show error
        if (!empty($unavailableItems)) {
            $message = 'Cannot approve: ' . implode(', ', $unavailableItems) . ' have zero stock available.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        try {
            DB::beginTransaction();

            // Update approval status
            $employee = Employee::first();
            if (!$employee) {
                throw new \Exception('No employee record found');
            }
            
            // Build adjusted items message for remarks
            $adjustmentMessage = '';
            if (!empty($adjustedItems)) {
                $adjustments = [];
                foreach ($adjustedItems as $adj) {
                    $adjustments[] = $adj['item_name'] . ' (Requested: ' . $adj['requested'] . ', Given: ' . $adj['available'] . ')';
                }
                $adjustmentMessage = ' Adjusted quantities: ' . implode(', ', $adjustments) . '.';
            }
            
            // Prepare final remarks - user remarks take priority, remove auto-generated text
            $userRemarks = trim($request->remarks ?? '');
            $finalRemarks = '';
            
            // If user provided remarks, use only user remarks (don't keep auto-generated text)
            if ($userRemarks) {
                $finalRemarks = $userRemarks;
            }
            
            // Add adjustment message if any
            if ($adjustmentMessage) {
                $finalRemarks = ($finalRemarks ? $finalRemarks . ' ' : '') . trim($adjustmentMessage);
            }
            
            // Update approval status and remarks
            $updateData = [
                'status' => 'approved',
                'approved_by' => $employee->id,
                'approved_at' => now(),
            ];
            
            // Update remarks if user provided remarks or if there are adjustments
            // This will replace the auto-generated remarks
            if ($finalRemarks) {
                $updateData['remarks'] = $finalRemarks;
            }
            
            $approval->update($updateData);
            
            // Deduct approved quantities now (no prior deduction at complaint stage)
            foreach ($approval->items as $item) {
                $spare = \App\Models\Spare::find($item->spare_id);
                if ($spare) {
                    $qtyToUse = $approvedInput->get($item->id) !== null
                        ? max(0, (int)$approvedInput->get($item->id))
                        : (int)$item->quantity_requested;
                    
                    $availableQty = (int)$spare->stock_quantity;
                    $requestedQty = (int)$item->quantity_requested;
                    
                    // Update reason if quantity was adjusted
                    $reason = $item->reason;
                    if ($qtyToUse < $requestedQty && $qtyToUse > 0) {
                        $reason = 'Insufficient stock: Requested ' . $requestedQty . ', Approved ' . $qtyToUse . ' (Available: ' . $availableQty . ')';
                    } elseif ($qtyToUse == 0) {
                        $reason = 'Zero stock: Requested ' . $requestedQty . ', Available 0';
                    }

                    // Persist approved quantity and updated reason
                    $item->update([
                        'quantity_approved' => $qtyToUse,
                        'reason' => $reason
                    ]);
                    
                    // Deduct exactly the approved quantity (only if > 0)
                    if ($qtyToUse > 0) {
                        $spare->removeStock(
                            $qtyToUse,
                            "Approved for complaint #{$approval->complaint->getTicketNumberAttribute()}",
                            $approval->complaint_id
                        );
                    }
                }
            }

            DB::commit();

            $message = 'Approval performa approved successfully.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Failed to approve: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * Update reason for in-process status
     */
    public function updateReason(Request $request, SpareApprovalPerforma $approval)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'complaint_id' => 'nullable|exists:complaints,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update approval remarks with the reason
            $approval->update([
                'remarks' => $request->reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reason updated successfully.',
                'reason' => $request->reason
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating reason: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject the specified approval performa
     */
    public function reject(Request $request, SpareApprovalPerforma $approval)
    {
        // Load relationships
        $approval->load(['items.spare', 'complaint.client', 'requestedBy']);
        
        $validator = Validator::make($request->all(), [
            'remarks' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        try {
            // Resolve acting employee safely (same pattern as approve())
            $employee = Employee::first();
            if (!$employee) {
                throw new \Exception('No employee record found');
            }

            $approval->update([
                'status' => 'rejected',
                'approved_by' => $employee->id,
                'approved_at' => now(),
                'remarks' => $request->remarks,
            ]);

            $message = 'Approval performa rejected successfully.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            $message = 'Failed to reject: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * Get approval statistics
     */
    public function getStatistics(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $stats = [
            'total' => SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))->count(),
            'pending' => SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))->where('status', 'pending')->count(),
            'approved' => SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))->where('status', 'approved')->count(),
            'rejected' => SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))->where('status', 'rejected')->count(),
            'overdue' => SpareApprovalPerforma::overdue()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk actions on approvals
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject',
                'approval_ids' => 'required|array|min:1',
                'approval_ids.*' => 'exists:spare_approval_performa,id',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator);
            }

            $approvalIds = $request->approval_ids;
            $action = $request->action;

            DB::beginTransaction();

            switch ($action) {
                case 'approve':
                    $approvals = SpareApprovalPerforma::whereIn('id', $approvalIds)
                        ->where('status', 'pending')
                        ->get();

                    foreach ($approvals as $approval) {
                        // Check availability for each item
                        $canApprove = true;
                        foreach ($approval->items as $item) {
                            if (!$item->isSpareAvailable()) {
                                $canApprove = false;
                                break;
                            }
                        }

                        if ($canApprove) {
                            $employee = Employee::first();
                            if (!$employee) {
                                throw new \Exception('No employee record found');
                            }
                            
                            $approval->update([
                                'status' => 'approved',
                                'approved_by' => $employee->id,
                                'approved_at' => now(),
                                'remarks' => $request->remarks,
                            ]);

                            // Deduct requested quantity as approved in bulk
                            foreach ($approval->items as $item) {
                                $approvedQty = (int)$item->quantity_requested;
                                $item->update(['quantity_approved' => $approvedQty]);
                                $item->spare->removeStock(
                                    $approvedQty,
                                    "Bulk approved for complaint #{$approval->complaint->getTicketNumberAttribute()}",
                                    $approval->complaint_id
                                );
                            }
                        }
                    }
                    $message = 'Selected approvals processed successfully.';
                    break;

                case 'reject':
                    $employee = Employee::first();
                    if (!$employee) {
                        throw new \Exception('No employee record found');
                    }
                    
                    $updated = SpareApprovalPerforma::whereIn('id', $approvalIds)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'rejected',
                            'approved_by' => $employee->id,
                            'approved_at' => now(),
                            'remarks' => $request->remarks,
                        ]);
                    
                    $message = 'Selected approvals rejected successfully.';
                    break;
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Export approvals data
     */
    public function export(Request $request)
    {
        $query = SpareApprovalPerforma::with([
            'complaint.client',
            'requestedBy',
            'approvedBy',
            'items.spare'
        ]);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('complaint', function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $approvals = $query->orderBy('created_at', 'desc')->get();

        $format = $request->get('format', 'csv');
        
        if ($format === 'csv') {
            return $this->exportToCsv($approvals);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($approvals);
        } else {
            return response()->json(['message' => 'Unsupported export format']);
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($approvals)
    {
        $filename = 'approvals_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($approvals) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Complaint ID',
                'Client Name',
                'Requested By',
                'Status',
                'Total Items',
                'Total Cost',
                'Created At',
                'Approved At',
                'Approved By',
                'Remarks'
            ]);

            // CSV Data
            foreach ($approvals as $approval) {
                fputcsv($file, [
                    $approval->id,
                    $approval->complaint->getTicketNumberAttribute(),
                    $approval->complaint->client ? $approval->complaint->client->client_name : 'Deleted Client',
                    $approval->requestedBy->name ?? 'N/A',
                    ucfirst($approval->status),
                    $approval->items->count(),
                    'PKR ' . number_format($approval->getTotalEstimatedCostAttribute(), 2),
                    $approval->created_at->format('Y-m-d H:i:s'),
                    $approval->approved_at ? $approval->approved_at->format('Y-m-d H:i:s') : 'N/A',
                    $approval->approvedBy->name ?? 'N/A',
                    $approval->remarks ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel (placeholder)
     */
    private function exportToExcel($approvals)
    {
        // This would require Laravel Excel package
        return response()->json(['message' => 'Excel export requires Laravel Excel package']);
    }

}