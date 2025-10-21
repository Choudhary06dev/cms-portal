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
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
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

        $approvals = $query->orderBy('created_at', 'desc')->paginate(15);
        
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
        
        $spares = Spare::where('status', 'active')->get();
        
        return view('admin.approvals.create', compact('complaints', 'spares'));
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

        return view('admin.approvals.show', compact('approval'));
    }

    /**
     * Approve the specified approval performa
     */
    public function approve(Request $request, SpareApprovalPerforma $approval)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        // Check if all items are available
        $unavailableItems = [];
        foreach ($approval->items as $item) {
            if (!$item->isSpareAvailable()) {
                $unavailableItems[] = $item->spare->item_name;
            }
        }

        if (!empty($unavailableItems)) {
            return redirect()->back()
                ->with('error', 'Cannot approve: ' . implode(', ', $unavailableItems) . ' are not available in sufficient quantity.');
        }

        // Update approval status
        $approval->update([
            'status' => 'approved',
            'approved_by' => auth()->user()->employee->id,
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);

        // Deduct stock for each item
        foreach ($approval->items as $item) {
            $item->spare->removeStock(
                $item->quantity_requested,
                "Approved for complaint #{$approval->complaint->getTicketNumberAttribute()}",
                $approval->complaint_id
            );
        }

        return redirect()->back()
            ->with('success', 'Approval performa approved successfully.');
    }

    /**
     * Reject the specified approval performa
     */
    public function reject(Request $request, SpareApprovalPerforma $approval)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $approval->update([
            'status' => 'rejected',
            'approved_by' => auth()->user()->employee->id,
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()
            ->with('success', 'Approval performa rejected successfully.');
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
     * Get approval chart data
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $data = SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json($data);
    }

    /**
     * Get monthly approval trends
     */
    public function getMonthlyTrends(Request $request)
    {
        $months = $request->get('months', 12);

        $data = SpareApprovalPerforma::where('created_at', '>=', now()->subMonths($months))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }

    /**
     * Get overdue approvals
     */
    public function getOverdueApprovals(Request $request)
    {
        $hours = $request->get('hours', 24);

        $overdue = SpareApprovalPerforma::overdue($hours)
            ->with(['complaint.client', 'requestedBy.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($overdue);
    }

    /**
     * Get approval performance by employee
     */
    public function getEmployeePerformance(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $performance = SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))
            ->whereNotNull('approved_by')
            ->selectRaw('approved_by, COUNT(*) as total_approved, AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_approval_time')
            ->groupBy('approved_by')
            ->with('approvedBy.user')
            ->get();

        return response()->json($performance);
    }

    /**
     * Get approval cost analysis
     */
    public function getCostAnalysis(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $costAnalysis = SpareApprovalPerforma::where('created_at', '>=', now()->subDays($period))
            ->where('status', 'approved')
            ->with(['items.spare'])
            ->get()
            ->map(function($approval) {
                return [
                    'id' => $approval->id,
                    'complaint_id' => $approval->complaint_id,
                    'total_cost' => $approval->getTotalEstimatedCostAttribute(),
                    'items_count' => $approval->getTotalItemsAttribute(),
                    'created_at' => $approval->created_at,
                ];
            })
            ->sortByDesc('total_cost')
            ->values();

        return response()->json($costAnalysis);
    }

    /**
     * Bulk actions on approvals
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'approval_ids' => 'required|array|min:1',
            'approval_ids.*' => 'exists:spare_approval_performa,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $approvalIds = $request->approval_ids;
        $action = $request->action;

        switch ($action) {
            case 'approve':
                $validator = Validator::make($request->all(), [
                    'remarks' => 'nullable|string',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
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
                        $approval->update([
                            'status' => 'approved',
                            'approved_by' => auth()->user()->employee->id,
                            'approved_at' => now(),
                            'remarks' => $request->remarks,
                        ]);

                        // Deduct stock
                        foreach ($approval->items as $item) {
                            $item->spare->removeStock(
                                $item->quantity_requested,
                                "Bulk approved for complaint #{$approval->complaint->getTicketNumberAttribute()}",
                                $approval->complaint_id
                            );
                        }
                    }
                }
                $message = 'Selected approvals processed successfully.';
                break;

            case 'reject':
                $validator = Validator::make($request->all(), [
                    'remarks' => 'required|string',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                SpareApprovalPerforma::whereIn('id', $approvalIds)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->user()->employee->id,
                        'approved_at' => now(),
                        'remarks' => $request->remarks,
                    ]);
                $message = 'Selected approvals rejected successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
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

        $approvals = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}
