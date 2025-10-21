<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ComplaintLog;
use App\Models\ComplaintAttachment;
use App\Models\SpareApprovalPerforma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $query = Complaint::with(['client', 'assignedEmployee.user', 'attachments']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('complaint_type') && $request->complaint_type) {
            $query->where('complaint_type', $request->complaint_type);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned employee
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by client
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

        $complaints = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $clients = Client::orderBy('client_name')->get();
        $employees = Employee::whereHas('user', function($q) {
            $q->where('status', 'active');
        })->with('user')->get();

        return view('admin.complaints.index', compact('complaints', 'clients', 'employees'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        $clients = Client::orderBy('client_name')->get();
        $employees = Employee::whereHas('user', function($q) {
            $q->where('status', 'active');
        })->with('user')->get();

        return view('admin.complaints.create', compact('clients', 'employees'));
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'complaint_type' => 'required|in:electric,sanitary,kitchen,general',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:employees,id',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $complaint = Complaint::create([
            'client_id' => $request->client_id,
            'complaint_type' => $request->complaint_type,
            'description' => $request->description,
            'location' => $request->location,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'status' => $request->assigned_to ? 'assigned' : 'new',
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
        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'status' => $complaint->status,
            'notes' => 'Complaint created',
            'created_by' => auth()->user()->employee->id ?? null,
        ]);

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint created successfully.');
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        $complaint->load([
            'client',
            'assignedEmployee.user',
            'attachments',
            'logs.user',
            'spareParts.spare',
            'spareApprovals.items.spare'
        ]);

        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the complaint
     */
    public function edit(Complaint $complaint)
    {
        $clients = Client::orderBy('client_name')->get();
        $employees = Employee::whereHas('user', function($q) {
            $q->where('status', 'active');
        })->with('user')->get();

        return view('admin.complaints.edit', compact('complaint', 'clients', 'employees'));
    }

    /**
     * Update the specified complaint
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'complaint_type' => 'required|in:electric,sanitary,kitchen,general',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:employees,id',
            'status' => 'required|in:new,assigned,in_progress,resolved,closed',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldStatus = $complaint->status;
        $oldAssignedTo = $complaint->assigned_to;

        $complaint->update([
            'client_id' => $request->client_id,
            'complaint_type' => $request->complaint_type,
            'description' => $request->description,
            'location' => $request->location,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
            'closed_at' => $request->status === 'closed' ? now() : null,
        ]);

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
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'status' => $request->status,
                'notes' => "Status changed from {$oldStatus} to {$request->status}",
                'created_by' => auth()->user()->employee->id ?? null,
            ]);
        }

        // Log assignment changes
        if ($oldAssignedTo !== $request->assigned_to) {
            $assignedEmployee = $request->assigned_to ? Employee::find($request->assigned_to) : null;
            $assignmentNote = $assignedEmployee 
                ? "Assigned to {$assignedEmployee->user->full_name}"
                : "Unassigned";
            
            ComplaintLog::create([
                'complaint_id' => $complaint->id,
                'status' => $complaint->status,
                'notes' => $assignmentNote,
                'created_by' => auth()->user()->employee->id ?? null,
            ]);
        }

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint updated successfully.');
    }

    /**
     * Remove the specified complaint
     */
    public function destroy(Complaint $complaint)
    {
        // Check if complaint has any related records
        if ($complaint->spareParts()->count() > 0 || $complaint->spareApprovals()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete complaint with existing spare parts or approval records.');
        }

        // Delete attachments
        foreach ($complaint->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $complaint->delete();

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint deleted successfully.');
    }

    /**
     * Assign complaint to employee
     */
    public function assign(Request $request, Complaint $complaint)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $employee = Employee::find($request->assigned_to);

        $complaint->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'assigned',
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'status' => 'assigned',
            'notes' => "Assigned to {$employee->user->full_name}. " . ($request->notes ?? ''),
            'created_by' => auth()->user()->employee->id ?? null,
        ]);

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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $oldStatus = $complaint->status;

        $complaint->update([
            'status' => $request->status,
            'closed_at' => $request->status === 'closed' ? now() : null,
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'status' => $request->status,
            'notes' => "Status changed from {$oldStatus} to {$request->status}. " . ($request->notes ?? ''),
            'created_by' => auth()->user()->employee->id ?? null,
        ]);

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

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'status' => $complaint->status,
            'notes' => $request->notes,
            'created_by' => auth()->user()->employee->id ?? null,
        ]);

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
            ->selectRaw('complaint_type, COUNT(*) as count')
            ->groupBy('complaint_type')
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
            ->with(['client', 'assignedEmployee.user'])
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
            ->whereNotNull('assigned_to')
            ->selectRaw('assigned_to, COUNT(*) as total_complaints, 
                SUM(CASE WHEN status = "resolved" OR status = "closed" THEN 1 ELSE 0 END) as resolved_complaints,
                AVG(CASE WHEN status = "resolved" OR status = "closed" THEN TIMESTAMPDIFF(HOUR, created_at, updated_at) ELSE NULL END) as avg_resolution_time')
            ->groupBy('assigned_to')
            ->with('assignedEmployee.user')
            ->get();

        return response()->json($performance);
    }

    /**
     * Print complaint slip
     */
    public function printSlip(Complaint $complaint)
    {
        $complaint->load(['client', 'assignedEmployee.user', 'attachments']);
        
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
                    'assigned_to' => 'required|exists:employees,id',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Complaint::whereIn('id', $complaintIds)->update([
                    'assigned_to' => $request->assigned_to,
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
                
                Complaint::whereIn('id', $complaintIds)->update([
                    'status' => $request->status,
                    'closed_at' => $request->status === 'closed' ? now() : null,
                ]);
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
     * Export complaints data
     */
    public function export(Request $request)
    {
        $query = Complaint::with(['client', 'assignedEmployee.user']);

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
            $query->where('complaint_type', $request->complaint_type);
        }

        $complaints = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}
