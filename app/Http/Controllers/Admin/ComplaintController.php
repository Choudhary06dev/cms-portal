<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ComplaintSpare;
use App\Models\Spare;
use App\Models\ComplaintCategory;
use App\Models\City;
use App\Models\Sector;
use Illuminate\Support\Facades\Schema;
use App\Models\SpareApprovalPerforma;
use App\Models\SpareApprovalItem;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintLog;
use App\Traits\LocationFilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    use LocationFilterTrait;
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::with(['client', 'assignedEmployee', 'attachments', 'spareParts.spare']);

        // Apply location-based filtering
        $this->filterComplaintsByLocation($query, $user);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned employee
        if ($request->has('assigned_employee_id') && $request->assigned_employee_id) {
            $query->where('assigned_employee_id', $request->assigned_employee_id);
        }

        // Filter by client (apply location filter)
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $complaints = $query->orderBy('id', 'desc')->paginate(15);
        
        // Filter employees by location
        $employeesQuery = Employee::where('status', 'active');
        $this->filterEmployeesByLocation($employeesQuery, $user);
        $employees = $employeesQuery->get();
        
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::orderBy('name')->pluck('name')
            : collect();

        return view('admin.complaints.index', compact('complaints', 'employees', 'categories'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        // Clear any old input data to ensure clean form
        request()->session()->forget('_old_input');
        
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::orderBy('name')->pluck('name')
            : collect();
        
        // Get cities and sectors for dropdowns
        $cities = Schema::hasTable('cities')
            ? City::where('status', 'active')->orderBy('name')->get()
            : collect();
        
        $sectors = collect(); // Will be loaded dynamically based on city selection

        // Defaults for Department Staff: preselect their city and sector
        $defaultCityId = null;
        $defaultSectorId = null;
        $authUser = Auth::user();
        if ($authUser && $authUser->role && strtolower($authUser->role->role_name) === 'department_staff') {
            $defaultCityId = $authUser->city_id;
            $defaultSectorId = $authUser->sector_id;
        }

        return view('admin.complaints.create', compact('employees', 'categories', 'cities', 'sectors', 'defaultCityId', 'defaultSectorId'));
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        // Debug: Log the request data
        Log::info('Complaint creation request', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            // Allow any category string (column changed to VARCHAR)
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent,emergency',
            'description' => 'required|string',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            // Status removed from form - will be managed in approvals view, default to 'new'
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
            'spare_parts' => 'nullable|array',
            'spare_parts.*.spare_id' => 'nullable|exists:spares,id',
            'spare_parts.*.quantity' => 'nullable|integer|min:1',
            'city' => 'nullable|string|max:100',
            'sector' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Get city and sector names from IDs if provided
            $cityName = null;
            $sectorName = null;
            
            if ($request->city_id) {
                $city = City::find($request->city_id);
                $cityName = $city ? $city->name : null;
            }
            
            if ($request->sector_id) {
                $sector = Sector::find($request->sector_id);
                $sectorName = $sector ? $sector->name : null;
            }
            
            // Find or create client by name
            $client = Client::firstOrCreate(
                ['client_name' => trim($request->client_name)],
                [
                    'contact_person' => $request->input('contact_person') ?: trim($request->client_name),
                    'email' => $request->input('email', ''),
                    'phone' => $request->input('phone', ''),
                    'city' => $cityName,
                    'sector' => $sectorName,
                    'address' => $request->input('address'),
                    'status' => 'active',
                ]
            );

            $complaint = Complaint::create([
                'title' => $request->title,
                'client_id' => $client->id,
                'city' => $cityName,
                'sector' => $sectorName,
                'category' => $request->category,
                'priority' => $request->priority,
                'description' => $request->description,
                'assigned_employee_id' => $request->assigned_employee_id,
                'status' => 'new', // Default to 'new' - status will be managed in approvals view
            ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('complaint-attachments', $filename, 'public');
                
                ComplaintAttachment::create([
                    'complaint_id' => $complaint->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Log the complaint creation
        // Find the employee associated with the current user
        $currentEmployee = Employee::first();
        
        if ($currentEmployee) {
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $currentEmployee->id,
                'action' => 'created',
                'remarks' => 'Complaint created',
            ]);
        }

        // Handle spare parts (optional)
        $totalCost = 0;
        $usedParts = [];

        // Only process spare parts if provided
        if (!empty($request->spare_parts) && is_array($request->spare_parts)) {
            foreach ($request->spare_parts as $part) {
                if (empty($part['spare_id']) || empty($part['quantity'])) {
                    Log::warning('Skipping empty spare part entry', ['part' => $part]);
                    continue; // Skip empty entries
                }

            $spare = Spare::find($part['spare_id']);
            
            if (!$spare) {
                throw new \Exception("Spare part not found: {$part['spare_id']}");
            }

            // Get employee ID for used_by
            $usedByEmployeeId = $currentEmployee ? $currentEmployee->id : (Employee::first()?->id);
            if (!$usedByEmployeeId) {
                throw new \Exception("No employee found to assign as used_by. Please create an employee first.");
            }

            // Do not enforce stock at request time; stock will be checked at approval

            // Create complaint spare record
            try {
                ComplaintSpare::create([
                    'complaint_id' => $complaint->id,
                    'spare_id' => $spare->id,
                    'quantity' => (int)$part['quantity'],
                    'used_by' => $usedByEmployeeId,
                    'used_at' => now(),
                ]);
                Log::info('ComplaintSpare created', [
                    'complaint_id' => $complaint->id,
                    'spare_id' => $spare->id,
                    'quantity' => $part['quantity']
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create ComplaintSpare', [
                    'complaint_id' => $complaint->id,
                    'spare_id' => $spare->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }

            // No stock deduction at complaint creation; happens on approval

                $totalCost += $spare->unit_price * $part['quantity'];
                $usedParts[] = "{$spare->item_name} (Qty: {$part['quantity']})";
            }
        }

        // Log spare parts usage if any were added
        if (!empty($usedParts) && $currentEmployee) {
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $currentEmployee->id,
                'action' => 'spare_parts_added',
                'remarks' => 'Added spare parts during creation: ' . implode(', ', $usedParts) . '. Total cost: PKR ' . number_format($totalCost, 2),
            ]);
        }

        // Create approval performa for the complaint (only if spare parts are provided)
        // Auto-approve if stock is available
        if (!empty($request->spare_parts) && is_array($request->spare_parts)) {
            try {
                // Get any employee as fallback if current user doesn't have employee record
                $requestedBy = $currentEmployee ? $currentEmployee->id : \App\Models\Employee::first()->id;
                
                if (!$requestedBy) {
                    Log::error('No employee found to assign as requested_by');
                    return redirect()->route('admin.complaints.index')
                        ->with('error', 'Complaint created but approval could not be generated. Please create an employee first.');
                }
                
                // Always create pending approval - never auto-approve
                // Check stock availability to add reason for insufficient stock
                $stockIssues = [];
                foreach ($request->spare_parts as $part) {
                    if (!empty($part['spare_id']) && !empty($part['quantity'])) {
                        $spare = Spare::find($part['spare_id']);
                        if ($spare) {
                            $requestedQty = (int)$part['quantity'];
                            $availableQty = (int)$spare->stock_quantity;
                            if ($availableQty < $requestedQty) {
                                $stockIssues[] = $spare->item_name . ' (Requested: ' . $requestedQty . ', Available: ' . $availableQty . ')';
                            }
                        }
                    }
                }
                
                // Create pending approval - never auto-approve
                $approval = SpareApprovalPerforma::create([
                    'complaint_id' => $complaint->id,
                    'requested_by' => $requestedBy,
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'remarks' => 'Auto-generated approval for complaint: ' . $complaint->title . 
                        (count($stockIssues) > 0 ? ' - Stock insufficient: ' . implode(', ', $stockIssues) : ''),
                ]);
                
                // Create approval items with requested quantities (no auto-approval)
                foreach ($request->spare_parts as $part) {
                    if (!empty($part['spare_id']) && !empty($part['quantity'])) {
                        $spare = Spare::find($part['spare_id']);
                        $requestedQty = (int)$part['quantity'];
                        
                        // Check stock and set reason if insufficient
                        $availableQty = $spare ? (int)$spare->stock_quantity : 0;
                        $reason = 'Requested from complaint creation';
                        if ($availableQty < $requestedQty) {
                            $reason = 'Insufficient stock: Requested ' . $requestedQty . ', Available ' . $availableQty;
                        }
                        
                        $item = SpareApprovalItem::create([
                            'performa_id' => $approval->id,
                            'spare_id' => (int)$part['spare_id'],
                            'quantity_requested' => $requestedQty,
                            'quantity_approved' => null, // Will be set when manually approved
                            'reason' => $reason,
                        ]);
                    }
                }
                
                Log::info('Approval created successfully (pending for manual approval)', [
                    'approval_id' => $approval->id,
                    'complaint_id' => $complaint->id,
                    'requested_by' => $requestedBy,
                    'status' => 'pending',
                    'stock_issues' => count($stockIssues) > 0 ? $stockIssues : null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create approval', [
                    'complaint_id' => $complaint->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Commit the transaction
        DB::commit();

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint created successfully.');
            
        } catch (\Exception $e) {
            // Rollback the transaction on any error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to create complaint: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        try {
            $complaint->load([
                'client',
                'assignedEmployee',
                'attachments',
                'logs.actionBy',
                'spareParts.spare',
                'spareParts.usedBy',
                'spareApprovals.items.spare'
            ]);

            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                // Exclude state from client data in JSON response for modal
                $complaintData = $complaint->toArray();
                if (isset($complaintData['client']) && is_array($complaintData['client']) && isset($complaintData['client']['state'])) {
                    unset($complaintData['client']['state']);
                }
                
                return response()->json([
                    'success' => true,
                    'complaint' => $complaintData
                ]);
            }

            return view('admin.complaints.show', compact('complaint'));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in ComplaintController@show: ' . $e->getMessage(), [
                'complaint_id' => $complaint->id,
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON error for AJAX requests
            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading complaint details: ' . $e->getMessage()
                ], 500);
            }

            // For regular requests, redirect back with error
            return redirect()->back()->with('error', 'Error loading complaint details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the complaint
     */
    public function edit(Complaint $complaint)
    {
        $complaint->load(['spareParts.spare']);
        
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $categories = Schema::hasTable('complaint_categories')
            ? ComplaintCategory::orderBy('name')->pluck('name')
            : collect();

        return view('admin.complaints.edit', compact('complaint', 'employees', 'categories'));
    }

    /**
     * Update the specified complaint
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            // Allow any category string (column changed to VARCHAR)
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent,emergency',
            'description' => 'required|string',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            // Status removed from form - will be managed in approvals view, keep existing status
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            // Product (spare) update requirements: single selection required
            'spare_parts' => 'required|array|min:1',
            'spare_parts.0.spare_id' => 'required|exists:spares,id',
            'spare_parts.0.quantity' => 'required|integer|min:1',
            'city' => 'nullable|string|max:100',
            'sector' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Find or create client by name
        $client = Client::firstOrCreate(
            ['client_name' => trim($request->client_name)],
            [
                'city' => $request->city ?? null,
                'sector' => $request->sector ?? null,
                'status' => 'active',
            ]
        );

        $oldStatus = $complaint->status;
        $oldAssignedTo = $complaint->assigned_employee_id;

        $complaint->update([
            'title' => $request->title,
            'client_id' => $client->id,
            'city' => $request->city,
            'sector' => $request->sector,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'assigned_employee_id' => $request->assigned_employee_id,
            // Status not updated here - will be managed in approvals view
            // Keep existing status and closed_at
        ]);

        // Update product (spare) selection: replace selection only; no stock movement here
        try {
            DB::beginTransaction();

            $currentEmployee = Employee::first();

            // Remove existing complaint spares without stock adjustment
            $existingSpares = $complaint->spareParts()->with('spare')->get();
            foreach ($existingSpares as $existing) {
                $existing->delete();
            }

            // Add the new selection (single box form)
            $part = $request->spare_parts[0];
            $spare = Spare::find($part['spare_id']);
            if (!$spare) {
                throw new \Exception('Selected product not found.');
            }
            // Do not enforce stock at request time; approval will enforce

            // Create record only (no stock deduction here)
            ComplaintSpare::create([
                'complaint_id' => $complaint->id,
                'spare_id' => $spare->id,
                'quantity' => (int)$part['quantity'],
                'used_by' => $currentEmployee?->id ?? Employee::first()->id,
                'used_at' => now(),
            ]);

            // Log change
            if ($currentEmployee) {
                ComplaintLog::create([
                    'complaint_id' => $complaint->id,
                    'action_by' => $currentEmployee->id,
                    'action' => 'spare_parts_updated',
                    'remarks' => "Updated product to {$spare->item_name} (Qty: {$part['quantity']})",
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product/quantity: ' . $e->getMessage())->withInput();
        }

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('complaint-attachments', $filename, 'public');
                
                ComplaintAttachment::create([
                    'complaint_id' => $complaint->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Log status changes
        if ($oldStatus !== $request->status) {
            $currentEmployee = Employee::first();
            if ($currentEmployee) {
                ComplaintLog::create([
                    'complaint_id' => $complaint->id,
                    'action_by' => $currentEmployee->id,
                    'action' => 'status_changed',
                    'remarks' => "Status changed from {$oldStatus} to {$request->status}",
                ]);
            }
        }

        // Log assignment changes
        if ($oldAssignedTo !== $request->assigned_employee_id) {
            $assignedEmployee = $request->assigned_employee_id ? Employee::find($request->assigned_employee_id) : null;
            $assignmentNote = $assignedEmployee 
                ? "Assigned to {$assignedEmployee->name}"
                : "Unassigned";
            
            $currentEmployee = Employee::first();
            if ($currentEmployee) {
                ComplaintLog::create([
                    'complaint_id' => $complaint->id,
                    'action_by' => $currentEmployee->id,
                    'action' => 'assignment_changed',
                    'remarks' => $assignmentNote,
                ]);
            }
        }

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint updated successfully.');
    }

    /**
     * Remove the specified complaint
     */
    public function destroy(Complaint $complaint)
    {
        try {
            // Soft delete - no need to check for related records as soft delete preserves them
            // Also no need to manually delete related records as they will be soft deleted too
            $complaint->delete(); // This will now soft delete due to SoftDeletes trait

            return redirect()->route('admin.complaints.index')
                ->with('success', 'Complaint deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting complaint: ' . $e->getMessage());
        }
    }

    /**
     * Assign complaint to employee
     */
    public function assign(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'assigned_employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $employee = Employee::find($request->assigned_employee_id);

        $complaint->update([
            'assigned_employee_id' => $request->assigned_employee_id,
            'status' => 'assigned',
        ]);

        $currentEmployee = Employee::first();
        if ($currentEmployee) {
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $currentEmployee->id,
                'action' => 'assigned',
                'remarks' => "Assigned to {$employee->name}. " . ($request->notes ?? ''),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Complaint assigned successfully.');
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,assigned,in_progress,resolved,closed',
            'notes' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);


        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first() ?: 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator);
        }

        $oldStatus = $complaint->status;
        
        // Get remarks - prefer remarks field, fallback to notes
        $remarks = $request->input('remarks') ?: $request->input('notes') ?: '';

        // Set closed_at when status becomes 'resolved' or 'closed', but only if it's not already set
        $updateData = [
            'status' => $request->status,
        ];
        
        if (in_array($request->status, ['resolved', 'closed']) && !$complaint->closed_at) {
            $updateData['closed_at'] = now();
        } elseif (!in_array($request->status, ['resolved', 'closed'])) {
            // If status is changed from resolved/closed to something else, clear closed_at
            $updateData['closed_at'] = null;
        }

        $complaint->update($updateData);


        $currentEmployee = Employee::first();
        if ($currentEmployee) {
            // Initialize log remarks with status change message
            $logRemarks = "Status changed from {$oldStatus} to {$request->status}";
            
            if ($remarks) {
                $logRemarks .= ". Remarks: " . $remarks;
            }
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $currentEmployee->id,
                'action' => 'status_changed',
                'remarks' => $logRemarks,
            ]);
        }

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            // Reload the complaint to get the updated closed_at
            $complaint->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Complaint status updated successfully.',
                'complaint' => [
                    'id' => $complaint->id,
                    'status' => $complaint->status,
                    'old_status' => $oldStatus,
                    'closed_at' => $complaint->closed_at ? $complaint->closed_at->format('d-m-Y H:i:s') : null,
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', 'Complaint status updated successfully.');
    }

    /**
     * Add notes to complaint
     */
    public function addNotes(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $currentEmployee = Employee::first();
        if ($currentEmployee) {
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $currentEmployee->id,
                'action' => 'note_added',
                'remarks' => $request->notes,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Notes added successfully.');
    }

    /**
     * Get complaint statistics
     */
    public function getStatistics(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $stats = [
            'total' => Complaint::where('created_at', '>=', now()->subDays($period))->count(),
            'new' => Complaint::where('created_at', '>=', now()->subDays($period))->where('status', 'new')->count(),
            'assigned' => Complaint::where('created_at', '>=', now()->subDays($period))->where('status', 'assigned')->count(),
            'in_progress' => Complaint::where('created_at', '>=', now()->subDays($period))->where('status', 'in_progress')->count(),
            'resolved' => Complaint::where('created_at', '>=', now()->subDays($period))->where('status', 'resolved')->count(),
            'closed' => Complaint::where('created_at', '>=', now()->subDays($period))->where('status', 'closed')->count(),
            'overdue' => Complaint::overdue()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get complaint chart data
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $data = Complaint::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json($data);
    }

    /**
     * Get complaints by type
     */
    public function getByType(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $data = Complaint::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return response()->json($data);
    }

    /**
     * Get overdue complaints
     */
    public function getOverdue(Request $request)
    {
        $days = $request->get('days', 7);

        $overdue = Complaint::overdue($days)
            ->with(['client', 'assignedEmployee'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($overdue);
    }

    /**
     * Get employee performance
     */
    public function getEmployeePerformance(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $performance = Complaint::where('created_at', '>=', now()->subDays($period))
            ->whereNotNull('assigned_employee_id')
            ->selectRaw('assigned_employee_id, COUNT(*) as total_complaints, 
                SUM(CASE WHEN status = "resolved" OR status = "closed" THEN 1 ELSE 0 END) as resolved_complaints,
                AVG(CASE WHEN status = "resolved" OR status = "closed" THEN TIMESTAMPDIFF(HOUR, created_at, updated_at) ELSE NULL END) as avg_resolution_time')
            ->groupBy('assigned_employee_id')
            ->with('assignedEmployee')
            ->get();

        return response()->json($performance);
    }

    /**
     * Print complaint slip
     */
    public function printSlip(Complaint $complaint)
    {
        $complaint->load(['client', 'assignedEmployee', 'attachments']);
        
        return view('admin.complaints.print-slip', compact('complaint'));
    }

    /**
     * Bulk actions on complaints
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:assign,change_status,change_priority,delete',
            'complaint_ids' => 'required|array|min:1',
            'complaint_ids.*' => 'exists:complaints,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $complaintIds = $request->complaint_ids;
        $action = $request->action;

        switch ($action) {
            case 'assign':
                $validator = Validator::make($request->all(), [
                    'assigned_employee_id' => 'required|exists:employees,id',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Complaint::whereIn('id', $complaintIds)->update([
                    'assigned_employee_id' => $request->assigned_employee_id,
                    'status' => 'assigned',
                ]);
                $message = 'Selected complaints assigned successfully.';
                break;

            case 'change_status':
                $validator = Validator::make($request->all(), [
                    'status' => 'required|in:new,assigned,in_progress,resolved,closed',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                // Set closed_at when status becomes 'resolved' or 'closed', but only if not already set
                if (in_array($request->status, ['resolved', 'closed'])) {
                    Complaint::whereIn('id', $complaintIds)
                        ->whereNull('closed_at')
                        ->update([
                            'status' => $request->status,
                            'closed_at' => now(),
                        ]);
                    
                    // Update status for complaints that already have closed_at
                    Complaint::whereIn('id', $complaintIds)
                        ->whereNotNull('closed_at')
                        ->update([
                            'status' => $request->status,
                        ]);
                } else {
                    // If status is changed from resolved/closed to something else, clear closed_at
                    Complaint::whereIn('id', $complaintIds)->update([
                        'status' => $request->status,
                        'closed_at' => null,
                    ]);
                }
                $message = 'Selected complaints status updated successfully.';
                break;

            case 'change_priority':
                $validator = Validator::make($request->all(), [
                    'priority' => 'required|in:low,medium,high',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Complaint::whereIn('id', $complaintIds)->update(['priority' => $request->priority]);
                $message = 'Selected complaints priority updated successfully.';
                break;

            case 'delete':
                // Check for related records
                $complaintsWithRecords = Complaint::whereIn('id', $complaintIds)
                    ->where(function($q) {
                        $q->whereHas('spareParts')
                          ->orWhereHas('spareApprovals');
                    })
                    ->count();

                if ($complaintsWithRecords > 0) {
                    return redirect()->back()
                        ->with('error', 'Some complaints cannot be deleted due to existing related records.');
                }

                Complaint::whereIn('id', $complaintIds)->delete();
                $message = 'Selected complaints deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Add spare parts to complaint with automatic stock deduction
     */
    public function addSpareParts(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'spare_parts' => 'required|array|min:1',
            'spare_parts.*.spare_id' => 'required|exists:spares,id',
            'spare_parts.*.quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $currentEmployee = Employee::first();
            if (!$currentEmployee) {
                throw new \Exception('Employee record not found');
            }

            $totalCost = 0;
            $usedParts = [];

            foreach ($request->spare_parts as $part) {
                $spare = Spare::find($part['spare_id']);
                
                if (!$spare) {
                    throw new \Exception("Spare part not found: {$part['spare_id']}");
                }

                // Create complaint spare record
                $complaintSpare = ComplaintSpare::create([
                    'complaint_id' => $complaint->id,
                    'spare_id' => $spare->id,
                    'quantity' => $part['quantity'],
                    'used_by' => $currentEmployee->id,
                    'used_at' => now(),
                ]);

                // No stock deduction here; happens on approval

                $totalCost += $spare->unit_price * $part['quantity'];
                $usedParts[] = "{$spare->item_name} (Qty: {$part['quantity']})";
            }

            // Log the spare parts usage
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'action_by' => $currentEmployee->id,
                'action' => 'spare_parts_added',
                'remarks' => 'Added spare parts: ' . implode(', ', $usedParts) . '. Total cost: PKR ' . number_format($totalCost, 2) . ($request->remarks ? '. Remarks: ' . $request->remarks : ''),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Spare parts added successfully. Total cost: PKR ' . number_format($totalCost, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to add spare parts: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Export complaints data
     */
    public function export(Request $request)
    {
        $query = Complaint::with(['client', 'assignedEmployee']);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('complaint_type') && $request->complaint_type) {
            $query->where('category', $request->complaint_type);
        }

        $complaints = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }

}
