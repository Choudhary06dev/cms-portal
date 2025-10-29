<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpareApprovalPerforma;
use App\Models\SpareApprovalItem;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Spare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of approval performas
     */
    public function index(Request $request)
    {
        $query = SpareApprovalPerforma::with([
            'complaint.client',
            'requestedBy.user',
            'approvedBy.user',
            'items.spare'
        ]);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('complaint', function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('client_name', 'like', "%{$search}%");
                  });
            })->orWhereHas('requestedBy.user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by requested by
        if ($request->has('requested_by') && $request->requested_by) {
            $query->where('requested_by', $request->requested_by);
        }

        // Filter by complaint
        if ($request->has('complaint_id') && $request->complaint_id) {
            $query->where('complaint_id', $request->complaint_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $approvals = $query->orderBy('id', 'desc')->paginate(15);
        
        $complaints = Complaint::pending()->with('client')->get();
        $employees = Employee::whereHas('user', function($q) {
            $q->where('status', 'active');
        })->with('user')->get();

        return view('admin.approvals.index', compact('approvals', 'complaints', 'employees'));
    }

    /**
     * Show the form for creating a new approval performa.
     */
    public function create()
    {
        $complaints = Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])
            ->with(['client', 'assignedEmployee.user'])
            ->get();
        
        $spares = Spare::all(); // Get all spares since status column might not exist
        
        return view('admin.approvals.create', compact('complaints', 'spares'));
    }

    /**
     * Store a newly created approval performa.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'complaint_id' => 'required|exists:complaints,id',
            'items' => 'required|array|min:1',
            'items.*.spare_id' => 'required|exists:spares,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create approval performa
            $approval = SpareApprovalPerforma::create([
                'complaint_id' => $request->complaint_id,
                'requested_by' => auth()->user()->employee->id,
                'status' => 'pending',
                'remarks' => $request->remarks,
            ]);

            // Create approval items
            foreach ($request->items as $item) {
                SpareApprovalItem::create([
                    'performa_id' => $approval->id,
                    'spare_id' => $item['spare_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'reason' => $item['reason'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.approvals.index')
                ->with('success', 'Approval request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create approval request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified approval performa
     */
    public function show(SpareApprovalPerforma $approval)
    {
        $approval->load([
            'complaint.client',
            'requestedBy.user',
            'approvedBy.user',
            'items.spare'
        ]);

        // If no approval items exist, backfill from complaint spare parts so UI can show requested rows
        if ($approval->items->isEmpty() && $approval->complaint) {
            try {
                // Try to use complaint spareParts relationship if present
                $approval->complaint->loadMissing(['spareParts.spare']);
                if ($approval->complaint->relationLoaded('spareParts') && $approval->complaint->spareParts->count() > 0) {
                    foreach ($approval->complaint->spareParts as $sp) {
                        \App\Models\SpareApprovalItem::firstOrCreate(
                            [
                                'performa_id' => $approval->id,
                                'spare_id' => $sp->spare_id,
                            ],
                            [
                                'quantity_requested' => (int)($sp->quantity ?? 1),
                                'reason' => 'Auto-imported from complaint spare usage',
                            ]
                        );
                    }
                    // Reload items after backfill
                    $approval->load(['items.spare']);
                }
            } catch (\Throwable $e) {
                \Log::warning('Failed to backfill approval items in show()', [
                    'approval_id' => $approval->id,
                    'error' => $e->getMessage(),
                ]);
            }
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
                    'client_name' => $approval->complaint->client ? $approval->complaint->client->client_name : 'Deleted Client',
                    'complaint_title' => $approval->complaint->title ?? 'N/A',
                    'requested_by_name' => $approval->requestedBy->user->username ?? 'N/A',
                    'approved_by_name' => $approval->approvedBy->user->username ?? null,
                    'items' => $approval->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'spare_name' => $item->spare->item_name ?? 'N/A',
                            'quantity_requested' => (int)$item->quantity_requested,
                            'quantity_approved' => $item->quantity_approved !== null ? (int)$item->quantity_approved : null,
                            'unit_price' => $item->spare->unit_price ?? 0
                        ];
                    })
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
        $approval->load(['items.spare', 'complaint.client', 'requestedBy.user']);
        
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

        // Check if all items are available (use approved qty if provided, else requested)
        $unavailableItems = [];
        foreach ($approval->items as $item) {
            // Get spare directly to avoid relationship issues
            $spare = \App\Models\Spare::find($item->spare_id);
            
            if (!$spare) {
                \Log::error('Spare not found for item ID: ' . $item->id . ', Spare ID: ' . $item->spare_id);
                $unavailableItems[] = 'Unknown Spare (ID: ' . $item->spare_id . ')';
                continue;
            }
            
            $qtyToUse = $approvedInput->get($item->id) !== null
                ? max(0, (int)$approvedInput->get($item->id))
                : (int)$item->quantity_requested;

            // Check stock availability directly against the intended approval quantity
            if ($spare->stock_quantity < $qtyToUse) {
                $unavailableItems[] = $spare->item_name;
            }
        }

        if (!empty($unavailableItems)) {
            $message = 'Cannot approve: ' . implode(', ', $unavailableItems) . ' are not available in sufficient quantity.';
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
            $employee = auth()->user()->employee ?? Employee::first();
            if (!$employee) {
                throw new \Exception('No employee record found');
            }
            
            $approval->update([
                'status' => 'approved',
                'approved_by' => $employee->id,
                'approved_at' => now(),
                'remarks' => $request->remarks,
            ]);

            // Deduct approved quantities now (no prior deduction at complaint stage)
            foreach ($approval->items as $item) {
                $spare = \App\Models\Spare::find($item->spare_id);
                if ($spare) {
                    $qtyToUse = $approvedInput->get($item->id) !== null
                        ? max(0, (int)$approvedInput->get($item->id))
                        : (int)$item->quantity_requested;

                    // Persist approved quantity on the item for accurate display/reporting
                    $item->update(['quantity_approved' => $qtyToUse]);
                    // Deduct exactly the approved quantity
                    $spare->removeStock(
                        $qtyToUse,
                        "Approved for complaint #{$approval->complaint->getTicketNumberAttribute()}",
                        $approval->complaint_id
                    );
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
     * Reject the specified approval performa
     */
    public function reject(Request $request, SpareApprovalPerforma $approval)
    {
        // Load relationships
        $approval->load(['items.spare', 'complaint.client', 'requestedBy.user']);
        
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
            $employee = auth()->user()->employee ?? Employee::first();
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
                            $employee = auth()->user()->employee ?? Employee::first();
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
                    $employee = auth()->user()->employee ?? Employee::first();
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
            'requestedBy.user',
            'approvedBy.user',
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
                    $approval->requestedBy->user->username ?? 'N/A',
                    ucfirst($approval->status),
                    $approval->items->count(),
                    'PKR ' . number_format($approval->getTotalEstimatedCostAttribute(), 2),
                    $approval->created_at->format('Y-m-d H:i:s'),
                    $approval->approved_at ? $approval->approved_at->format('Y-m-d H:i:s') : 'N/A',
                    $approval->approvedBy->user->username ?? 'N/A',
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
