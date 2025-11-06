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
use App\Models\City;
use App\Models\Sector;
use App\Models\ComplaintCategory;
use App\Traits\DatabaseTimeHelpers;
use App\Traits\LocationFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    use DatabaseTimeHelpers, LocationFilterTrait;
    public function __construct()
    {
        // Middleware is applied in routes
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter values from request
        $cityId = $request->input('city_id');
        $sectorId = $request->input('sector_id');
        $category = $request->input('category');
        $approvalStatus = $request->input('approval_status');
        $complaintStatus = $request->input('complaint_status');
        $dateRange = $request->input('date_range');
        
        // Get cities for filter: Check user table - if city_id is null, user can see all cities
        $cities = collect();
        if (Schema::hasTable('cities')) {
            if (!$user->city_id) {
                // User has no city_id assigned, can see all cities
                $cities = City::where('status', 'active')->orderBy('name')->get();
            } elseif ($user->city_id && $user->city) {
                // User has city_id assigned, sees only their city
                $cities = City::where('id', $user->city_id)->where('status', 'active')->get();
            }
        }
        
        // Get sectors for filter: Check user table - if sector_id is null, user can see sectors
        $sectors = collect();
        if (Schema::hasTable('sectors')) {
            if (!$user->sector_id) {
                // User has no sector_id assigned, can see sectors
                if ($user->city_id) {
                    // If user has city_id, show sectors in their city
                    $sectors = Sector::where('city_id', $user->city_id)->where('status', 'active')->orderBy('name')->get();
                } else {
                    // If user has no city_id, show all sectors or sectors of selected city
                    if ($cityId) {
                        $sectors = Sector::where('city_id', $cityId)->where('status', 'active')->orderBy('name')->get();
                    } else {
                        $sectors = Sector::where('status', 'active')->orderBy('name')->get();
                    }
                }
            } elseif ($user->sector_id && $user->sector) {
                // User has sector_id assigned, sees only their sector
                $sectors = Sector::where('id', $user->sector_id)->where('status', 'active')->get();
            }
        }
        
        // Get categories for filter
        $categories = collect();
        if (Schema::hasTable('complaint_categories')) {
            $categories = ComplaintCategory::orderBy('name')->pluck('name');
        } else {
            // Fallback: Get from complaints
            $categories = Complaint::select('category')
                ->distinct()
                ->whereNotNull('category')
                ->orderBy('category')
                ->pluck('category');
        }
        
        // Get approval statuses for filter (fetch from database)
        $approvalStatuses = collect();
        if (Schema::hasTable('spare_approval_performa')) {
            // Get all unique statuses from database
            $statusesFromDB = SpareApprovalPerforma::select('status')
                ->distinct()
                ->whereNotNull('status')
                ->orderBy('status')
                ->pluck('status');
            
            // Map to status => label format
            $statusLabels = SpareApprovalPerforma::getStatuses();
            $approvalStatuses = $statusesFromDB->mapWithKeys(function($status) use ($statusLabels) {
                return [$status => $statusLabels[$status] ?? ucfirst($status)];
            });
        }
        
        // Get complaint statuses for filter (same as approvals page)
        $complaintStatuses = [
            'assigned' => 'Assigned',
            'in_progress' => 'In-Process',
            'resolved' => 'Addressed',
            'work_performa' => 'Work Performa',
            'maint_performa' => 'Maint Performa',
            'priced_performa' => 'Maint/Work Priced',
            'product_na' => 'Product N/A',
        ];
        
        // Get dashboard statistics with filters
        $stats = $this->getDashboardStats($user, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
        
        // Get recent complaints with location filtering and filters
        $recentComplaintsQuery = Complaint::with(['client', 'assignedEmployee']);
        $this->filterComplaintsByLocation($recentComplaintsQuery, $user);
        $this->applyFilters($recentComplaintsQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
        $recentComplaints = $recentComplaintsQuery->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get approvals with location filtering and filters (all statuses, not just pending)
        $pendingApprovalsQuery = SpareApprovalPerforma::with(['complaint.client', 'requestedBy', 'items.spare']);
        
        // Apply approval status filter directly on approvals (if specified, otherwise show all)
        if ($approvalStatus) {
            $pendingApprovalsQuery->where('status', $approvalStatus);
        }
        // If no approval status filter, show all approvals (pending, approved, rejected)
        
        // Apply location filter through complaint relationship
        if (!$this->canViewAllData($user)) {
            $pendingApprovalsQuery->whereHas('complaint', function($q) use ($user, $cityId, $sectorId, $category, $complaintStatus, $dateRange) {
                $this->filterComplaintsByLocation($q, $user);
                $this->applyFilters($q, $cityId, $sectorId, $category, null, $complaintStatus, $dateRange);
            });
        } else {
            // Director: Apply filters through complaint relationship
            if ($cityId || $sectorId || $category || $complaintStatus || $dateRange) {
                $pendingApprovalsQuery->whereHas('complaint', function($q) use ($cityId, $sectorId, $category, $complaintStatus, $dateRange) {
                    $this->applyFilters($q, $cityId, $sectorId, $category, null, $complaintStatus, $dateRange);
                });
            }
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

        // Get overdue complaints with location filtering and filters
        $overdueComplaintsQuery = Complaint::overdue()
            ->with(['client', 'assignedEmployee']);
        $this->filterComplaintsByLocation($overdueComplaintsQuery, $user);
        $this->applyFilters($overdueComplaintsQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
        $overdueComplaints = $overdueComplaintsQuery->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Get complaints by status with location filtering and filters
        $complaintsByStatusQuery = Complaint::query();
        $this->filterComplaintsByLocation($complaintsByStatusQuery, $user);
        $this->applyFilters($complaintsByStatusQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
        $complaintsByStatus = $complaintsByStatusQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get complaints by category with location filtering and filters
        $complaintsByTypeQuery = Complaint::query();
        $this->filterComplaintsByLocation($complaintsByTypeQuery, $user);
        $this->applyFilters($complaintsByTypeQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
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

        // Get monthly trends with filters
        $monthlyTrends = $this->getMonthlyTrends($user, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);

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
            'monthlyTrends',
            'cities',
            'sectors',
            'categories',
            'approvalStatuses',
            'complaintStatuses',
            'cityId',
            'sectorId',
            'category',
            'approvalStatus',
            'complaintStatus',
            'dateRange'
        ));
    }

    /**
     * Apply filters to complaint query
     */
    private function applyFilters($query, $cityId = null, $sectorId = null, $category = null, $approvalStatus = null, $complaintStatus = null, $dateRange = null)
    {
        // Filter by city
        if ($cityId) {
            $city = City::find($cityId);
            if ($city) {
                $query->whereHas('client', function($q) use ($city) {
                    $q->where('city', $city->name);
                });
            }
        }
        
        // Filter by sector
        if ($sectorId) {
            $sector = Sector::find($sectorId);
            if ($sector) {
                $query->whereHas('client', function($q) use ($sector) {
                    $q->where('sector', $sector->name);
                });
            }
        }
        
        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }
        
        // Filter by approval status (through spareApprovals relationship)
        if ($approvalStatus) {
            $query->whereHas('spareApprovals', function($q) use ($approvalStatus) {
                $q->where('status', $approvalStatus);
            });
        }
        
        // Filter by complaint status
        if ($complaintStatus) {
            // Handle special performa statuses
            if ($complaintStatus === 'work_performa' || $complaintStatus === 'maint_performa' || $complaintStatus === 'priced_performa' || $complaintStatus === 'product_na') {
                // These are shown as in_progress with special badges
                $query->where('status', 'in_progress');
            } else {
                $query->where('status', $complaintStatus);
            }
        }
        
        // Filter by date range
        if ($dateRange) {
            $now = now();
            switch ($dateRange) {
                case 'yesterday':
                    $query->whereDate('created_at', $now->copy()->subDay()->toDateString());
                    break;
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', $now->copy()->subMonth()->month)
                          ->whereYear('created_at', $now->copy()->subMonth()->year);
                    break;
                case 'last_6_months':
                    $query->where('created_at', '>=', $now->copy()->subMonths(6)->startOfDay());
                    break;
            }
        }
        
        return $query;
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($user = null, $cityId = null, $sectorId = null, $category = null, $approvalStatus = null, $complaintStatus = null, $dateRange = null)
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Apply location filtering to queries
        $complaintsQuery = Complaint::query();
        $this->filterComplaintsByLocation($complaintsQuery, $user);
        
        // Apply additional filters
        $this->applyFilters($complaintsQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
        
        $employeesQuery = Employee::query();
        $this->filterEmployeesByLocation($employeesQuery, $user);
        
        // Filter employees by selected filters based on their assigned complaints
        if ($category) {
            $employeesQuery->whereHas('assignedComplaints', function($q) use ($category) {
                $q->where('category', $category);
            });
        }
        if ($cityId) {
            $employeesQuery->where('city_id', $cityId);
        }
        if ($sectorId) {
            $employeesQuery->where('sector_id', $sectorId);
        }
        
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

            // Approval statistics removed

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
    private function getMonthlyTrends($user = null, $cityId = null, $sectorId = null, $category = null, $approvalStatus = null, $complaintStatus = null, $dateRange = null)
    {
        $months = [];
        $complaints = [];
        $resolutions = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            // Get complaints for this month with location and filters
            $complaintsQuery = Complaint::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $this->filterComplaintsByLocation($complaintsQuery, $user);
            $this->applyFilters($complaintsQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
            $complaints[] = $complaintsQuery->count();
            
            // Get resolutions for this month with location and filters
            $resolutionsQuery = Complaint::whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->whereIn('status', ['resolved', 'closed']);
            $this->filterComplaintsByLocation($resolutionsQuery, $user);
            $this->applyFilters($resolutionsQuery, $cityId, $sectorId, $category, $approvalStatus, $complaintStatus, $dateRange);
            $resolutions[] = $resolutionsQuery->count();
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
