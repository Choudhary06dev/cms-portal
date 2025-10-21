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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent complaints
        $recentComplaints = Complaint::with(['client', 'assignedEmployee.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending approvals
        $pendingApprovals = SpareApprovalPerforma::with(['complaint.client', 'requestedBy.user', 'items.spare'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get low stock items
        $lowStockItems = Spare::lowStock()
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Get overdue complaints
        $overdueComplaints = Complaint::overdue()
            ->with(['client', 'assignedEmployee.user'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Get complaints by status
        $complaintsByStatus = Complaint::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get complaints by type
        $complaintsByType = Complaint::selectRaw('complaint_type, COUNT(*) as count')
            ->groupBy('complaint_type')
            ->pluck('count', 'complaint_type')
            ->toArray();

        // Get employee performance
        $employeePerformance = Employee::with('user')
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
    private function getDashboardStats()
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            // Complaint statistics
            'total_complaints' => Complaint::count(),
            'new_complaints' => Complaint::where('status', 'new')->count(),
            'pending_complaints' => Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_complaints' => Complaint::whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_complaints' => Complaint::overdue()->count(),
            'complaints_today' => Complaint::whereDate('created_at', $today)->count(),
            'complaints_this_month' => Complaint::where('created_at', '>=', $thisMonth)->count(),
            'complaints_last_month' => Complaint::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),

            // User statistics
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_employees' => Employee::count(),
            'total_clients' => Client::count(),

            // Spare parts statistics
            'total_spares' => Spare::count(),
            'low_stock_items' => Spare::lowStock()->count(),
            'out_of_stock_items' => Spare::outOfStock()->count(),
            'total_spare_value' => Spare::sum(DB::raw('stock_quantity * unit_price')),

            // Approval statistics
            'pending_approvals' => SpareApprovalPerforma::where('status', 'pending')->count(),
            'approved_this_month' => SpareApprovalPerforma::where('status', 'approved')
                ->where('created_at', '>=', $thisMonth)->count(),

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
            $withinSla = Complaint::where('created_at', '>=', now()->subDays(30))
                ->whereIn('status', ['resolved', 'closed'])
                ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, updated_at) <= (SELECT max_resolution_time FROM sla_rules WHERE complaint_type = complaints.complaint_type AND status = "active")')
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
        return Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) > (SELECT max_response_time FROM sla_rules WHERE complaint_type = complaints.complaint_type AND status = "active")')
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
        $data = Complaint::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('complaint_type, 
                COUNT(*) as total,
                SUM(CASE WHEN TIMESTAMPDIFF(HOUR, created_at, updated_at) <= (SELECT max_resolution_time FROM sla_rules WHERE complaint_type = complaints.complaint_type AND status = "active") THEN 1 ELSE 0 END) as within_sla,
                SUM(CASE WHEN TIMESTAMPDIFF(HOUR, created_at, updated_at) > (SELECT max_resolution_time FROM sla_rules WHERE complaint_type = complaints.complaint_type AND status = "active") THEN 1 ELSE 0 END) as breached')
            ->groupBy('complaint_type')
            ->get();

        return response()->json($data);
    }

    /**
     * Get real-time updates
     */
    public function getRealTimeUpdates()
    {
        $updates = [
            'new_complaints' => Complaint::where('created_at', '>=', now()->subMinutes(5))->count(),
            'new_approvals' => SpareApprovalPerforma::where('created_at', '>=', now()->subMinutes(5))->count(),
            'low_stock_alerts' => Spare::lowStock()->count(),
            'sla_breaches' => $this->getSlaBreaches(),
        ];

        return response()->json($updates);
    }
}
