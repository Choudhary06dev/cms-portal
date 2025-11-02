<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Spare;
use App\Models\SpareApprovalPerforma;
use App\Models\SlaRule;
use App\Traits\DatabaseTimeHelpers;
use App\Traits\LocationFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use DatabaseTimeHelpers, LocationFilterTrait;
    public function __construct()
    {
        // Middleware is applied in routes
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($user);
        
        // Get recent complaints with location filtering
        $recentComplaintsQuery = Complaint::with(['client', 'assignedEmployee']);
        $this->filterComplaintsByLocation($recentComplaintsQuery, $user);
        $recentComplaints = $recentComplaintsQuery->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending approvals with location filtering
        $pendingApprovalsQuery = SpareApprovalPerforma::with(['complaint.client', 'requestedBy', 'items.spare'])
            ->where('status', 'pending');
        
        // Apply location filter through complaint relationship
        if (!$this->canViewAllData($user)) {
            $pendingApprovalsQuery->whereHas('complaint', function($q) use ($user) {
                $this->filterComplaintsByLocation($q, $user);
            });
        }
        
        $pendingApprovals = $pendingApprovalsQuery->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get low stock items with location filtering
        $lowStockItemsQuery = Spare::lowStock();
        $this->filterSparesByLocation($lowStockItemsQuery, $user);
        $lowStockItems = $lowStockItemsQuery
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Get overdue complaints with location filtering
        $overdueComplaintsQuery = Complaint::overdue()
            ->with(['client', 'assignedEmployee']);
        $this->filterComplaintsByLocation($overdueComplaintsQuery, $user);
        $overdueComplaints = $overdueComplaintsQuery->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Get complaints by status with location filtering
        $complaintsByStatusQuery = Complaint::query();
        $this->filterComplaintsByLocation($complaintsByStatusQuery, $user);
        $complaintsByStatus = $complaintsByStatusQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get complaints by category with location filtering
        $complaintsByTypeQuery = Complaint::query();
        $this->filterComplaintsByLocation($complaintsByTypeQuery, $user);
        $complaintsByType = $complaintsByTypeQuery->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Get employee performance with location filtering
        $employeePerformanceQuery = Employee::query();
        $this->filterEmployeesByLocation($employeePerformanceQuery, $user);
        $employeePerformance = $employeePerformanceQuery
            ->withCount(['assignedComplaints' => function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])
            ->orderBy('assigned_complaints_count', 'desc')
            ->limit(5)
            ->get();

        // Get SLA performance
        $slaPerformance = $this->getSlaPerformance();

        // Get monthly trends
        $monthlyTrends = $this->getMonthlyTrends();

        return view('admin.dashboard', compact(
            'stats',
            'recentComplaints',
            'pendingApprovals',
            'lowStockItems',
            'overdueComplaints',
            'complaintsByStatus',
            'complaintsByType',
            'employeePerformance',
            'slaPerformance',
            'monthlyTrends'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($user = null)
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Apply location filtering to queries
        $complaintsQuery = Complaint::query();
        $this->filterComplaintsByLocation($complaintsQuery, $user);
        
        $employeesQuery = Employee::query();
        $this->filterEmployeesByLocation($employeesQuery, $user);
        
        $clientsQuery = Client::query();
        $this->filterClientsByLocation($clientsQuery, $user);
        
        $sparesQuery = Spare::query();
        $this->filterSparesByLocation($sparesQuery, $user);

        return [
            // Complaint statistics with location filtering
            'total_complaints' => (clone $complaintsQuery)->count(),
            'new_complaints' => (clone $complaintsQuery)->where('status', 'new')->count(),
            'pending_complaints' => (clone $complaintsQuery)->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_complaints' => (clone $complaintsQuery)->whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_complaints' => (clone $complaintsQuery)->where(function($q) {
                return $q->overdue();
            })->count(),
            'complaints_today' => (clone $complaintsQuery)->whereDate('created_at', $today)->count(),
            'complaints_this_month' => (clone $complaintsQuery)->where('created_at', '>=', $thisMonth)->count(),
            'complaints_last_month' => (clone $complaintsQuery)->whereBetween('created_at', [$lastMonth, $thisMonth])->count(),

            // User statistics (users are not location-based)
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_employees' => (clone $employeesQuery)->count(),
            'total_clients' => (clone $clientsQuery)->count(),

            // Spare parts statistics with location filtering
            'total_spares' => (clone $sparesQuery)->count(),
            'low_stock_items' => (clone $sparesQuery)->lowStock()->count(),
            'out_of_stock_items' => (clone $sparesQuery)->outOfStock()->count(),
            'total_spare_value' => (clone $sparesQuery)->sum(DB::raw('stock_quantity * unit_price')),

            // Approval statistics
            'pending_approvals' => SpareApprovalPerforma::where('status', 'pending')->count(),
            'approved_this_month' => SpareApprovalPerforma::where('status', 'approved')
                ->where('created_at', '>=', $thisMonth)->count(),
            'total_approvals' => SpareApprovalPerforma::count(),
            'rejected_approvals' => SpareApprovalPerforma::where('status', 'rejected')->count(),

            // SLA statistics
            'active_sla_rules' => SlaRule::where('status', 'active')->count(),
            'sla_breaches' => $this->getSlaBreaches(),
        ];
    }

    /**
     * Get SLA performance metrics
     */
    private function getSlaPerformance()
    {
        $totalComplaints = Complaint::where('created_at', '>=', now()->subDays(30))->count();
        $withinSla = 0;
        $breached = 0;

        if ($totalComplaints > 0) {
            $timeDiff = $this->getTimeDiffInHours('created_at', 'updated_at');
            
            $withinSla = Complaint::where('created_at', '>=', now()->subDays(30))
                ->whereIn('status', ['resolved', 'closed'])
                ->whereRaw("{$timeDiff} <= (SELECT max_resolution_time FROM sla_rules WHERE complaint_type = complaints.category AND status = 'active')")
                ->count();

            $breached = $totalComplaints - $withinSla;
        }

        return [
            'total' => $totalComplaints,
            'within_sla' => $withinSla,
            'breached' => $breached,
            'sla_percentage' => $totalComplaints > 0 ? round(($withinSla / $totalComplaints) * 100, 2) : 0,
        ];
    }

    /**
     * Get monthly trends
     */
    private function getMonthlyTrends()
    {
        $months = [];
        $complaints = [];
        $resolutions = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $complaints[] = Complaint::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $resolutions[] = Complaint::whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
        }

        return [
            'months' => $months,
            'complaints' => $complaints,
            'resolutions' => $resolutions,
        ];
    }

    /**
     * Get SLA breaches
     */
    private function getSlaBreaches()
    {
        $timeDiff = $this->getTimeDiffFromNow('created_at');
        
        return Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])
            ->whereRaw("{$timeDiff} > (SELECT max_response_time FROM sla_rules WHERE complaint_type = complaints.category AND status = 'active')")
            ->count();
    }

    /**
     * Get dashboard chart data
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'complaints');
        $period = $request->get('period', '30');

        switch ($type) {
            case 'complaints':
                return $this->getComplaintsChartData($period);
            case 'spares':
                return $this->getSparesChartData($period);
            case 'employees':
                return $this->getEmployeesChartData($period);
            case 'sla':
                return $this->getSlaChartData($period);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    /**
     * Get complaints chart data
     */
    private function getComplaintsChartData($period)
    {
        $data = Complaint::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    /**
     * Get spares chart data
     */
    private function getSparesChartData($period)
    {
        $data = Spare::selectRaw('category, COUNT(*) as count, SUM(stock_quantity * unit_price) as total_value')
            ->groupBy('category')
            ->get();

        return response()->json($data);
    }

    /**
     * Get employees chart data
     */
    private function getEmployeesChartData($period)
    {
        $data = Employee::withCount(['assignedComplaints' => function($query) use ($period) {
            $query->where('created_at', '>=', now()->subDays($period));
        }])
        ->orderBy('assigned_complaints_count', 'desc')
        ->limit(10)
        ->get();

        return response()->json($data);
    }

    /**
     * Get SLA chart data
     */
    private function getSlaChartData($period)
    {
        $timeDiff = $this->getTimeDiffInHours('created_at', 'updated_at');
        
        $data = Complaint::where('created_at', '>=', now()->subDays($period))
            ->selectRaw("category, 
                COUNT(*) as total,
                SUM(CASE WHEN {$timeDiff} <= (SELECT max_resolution_time FROM sla_rules WHERE complaint_type = complaints.category AND status = 'active') THEN 1 ELSE 0 END) as within_sla,
                SUM(CASE WHEN {$timeDiff} > (SELECT max_resolution_time FROM sla_rules WHERE complaint_type = complaints.category AND status = 'active') THEN 1 ELSE 0 END) as breached")
            ->groupBy('category')
            ->get();

        return response()->json($data);
    }

    /**
     * Get real-time updates
     */
    public function getRealTimeUpdates()
    {
        $user = Auth::user();
        $lowStockQuery = Spare::lowStock();
        $this->filterSparesByLocation($lowStockQuery, $user);
        
        $updates = [
            'new_complaints' => Complaint::where('created_at', '>=', now()->subMinutes(5))->count(),
            'new_approvals' => SpareApprovalPerforma::where('created_at', '>=', now()->subMinutes(5))->count(),
            'low_stock_alerts' => $lowStockQuery->count(),
            'sla_breaches' => $this->getSlaBreaches(),
        ];

        return response()->json($updates);
    }
}
