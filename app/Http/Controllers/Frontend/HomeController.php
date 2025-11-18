<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Spare;
use App\Models\ComplaintCategory;
use App\Models\City;
use App\Models\Sector;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.home');
    }

    public function features()
    {
        return view('frontend.features');
    }

    public function dashboard(Request $request)
    {
        // Get filter parameters
        $cityId = $request->get('city_id');
        $sectorId = $request->get('sector_id');
        $category = $request->get('category');
        $status = $request->get('status');
        $dateRange = $request->get('date_range');
        
        // Build base query with filters
        $complaintsQuery = Complaint::query();
        
        if ($cityId) {
            $complaintsQuery->where('city_id', $cityId);
        }
        
        if ($sectorId) {
            $complaintsQuery->where('sector_id', $sectorId);
        }
        
        if ($category && $category !== 'all') {
            $complaintsQuery->where('category', $category);
        }
        
        if ($status && $status !== 'all') {
            $complaintsQuery->where('status', $status);
        }
        
        // Filter by date range
        if ($dateRange) {
            $now = now();
            switch ($dateRange) {
                case 'yesterday':
                    $complaintsQuery->whereDate('created_at', $now->copy()->subDay()->toDateString());
                    break;
                case 'today':
                    $complaintsQuery->whereDate('created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $complaintsQuery->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    break;
                case 'last_week':
                    $complaintsQuery->whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $complaintsQuery->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'last_month':
                    $complaintsQuery->whereMonth('created_at', $now->copy()->subMonth()->month)
                          ->whereYear('created_at', $now->copy()->subMonth()->year);
                    break;
                case 'last_6_months':
                    $complaintsQuery->where('created_at', '>=', $now->copy()->subMonths(6)->startOfDay());
                    break;
            }
        }
        
        // Get filter options
        $geGroups = City::where(function($q) {
                $q->where('name', 'LIKE', '%GE%')
                  ->orWhere('name', 'LIKE', '%AGE%')
                  ->orWhere('name', 'LIKE', '%ge%')
                  ->orWhere('name', 'LIKE', '%age%');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        $geNodes = Sector::where('status', 'active');
        if ($cityId) {
            $geNodes->where('city_id', $cityId);
        }
        $geNodes = $geNodes->orderBy('name')->get();
        
        $categories = ComplaintCategory::all();
        
        // Get all statuses from database (same as admin side)
        $statuses = [
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'resolved' => 'Addressed',
            'work_performa' => 'Work Performa',
            'maint_performa' => 'Maintenance Performa',
            'work_priced_performa' => 'Work Performa Priced',
            'maint_priced_performa' => 'Maintenance Performa Priced',
            'product_na' => 'Product N/A',
            'un_authorized' => 'Un-Authorized',
            'pertains_to_ge_const_isld' => 'Pertains to GE(N) Const Isld',
        ];
        
        // Calculate stats with filters
        $stats = [
            'total_complaints' => (clone $complaintsQuery)->count(),
            'new_complaints' => (clone $complaintsQuery)->where('status', 'new')->count(),
            'pending_complaints' => (clone $complaintsQuery)->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_complaints' => (clone $complaintsQuery)->whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_complaints' => (clone $complaintsQuery)->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'complaints_today' => (clone $complaintsQuery)->whereDate('created_at', now()->startOfDay())->count(),
            'complaints_this_month' => (clone $complaintsQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
            'complaints_last_month' => (clone $complaintsQuery)->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->count(),
        ];
        
        // Calculate resolution rate
        $totalComplaints = $stats['total_complaints'];
        $resolvedComplaints = $stats['resolved_complaints'];
        $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100) : 0;
        $stats['resolution_rate'] = $resolutionRate;
        
        // Calculate average resolution time
        $resolvedComplaintsWithTime = (clone $complaintsQuery)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('closed_at')
            ->get();
        
        $avgResolutionDays = 0;
        if ($resolvedComplaintsWithTime->count() > 0) {
            $totalDays = $resolvedComplaintsWithTime->sum(function($complaint) {
                return $complaint->created_at->diffInDays($complaint->closed_at);
            });
            $avgResolutionDays = round($totalDays / $resolvedComplaintsWithTime->count());
        }
        $stats['average_resolution_days'] = $avgResolutionDays;
        
        // In Progress count
        $stats['in_progress'] = (clone $complaintsQuery)->where('status', 'in_progress')->count();
        
        // Assigned count
        $stats['assigned'] = (clone $complaintsQuery)->where('status', 'assigned')->count();
        
        // Closed count
        $stats['closed'] = (clone $complaintsQuery)->where('status', 'closed')->count();
        
        // Work Performa count
        $stats['work_performa'] = (clone $complaintsQuery)->where('status', 'work_performa')->count();
        
        // Maintenance Performa count
        $stats['maint_performa'] = (clone $complaintsQuery)->where('status', 'maint_performa')->count();
        
        // Addressed count (resolved status)
        $stats['addressed'] = (clone $complaintsQuery)->where('status', 'resolved')->count();
        
        // Un Authorized count
        $stats['un_authorized'] = (clone $complaintsQuery)->where('status', 'un_authorized')->count();
        
        // Product N/A count
        $stats['product'] = (clone $complaintsQuery)->where('status', 'product_na')->count();
        
        // Pertains to GE/Const/Isld count
        $stats['pertains_to_ge_const_isld'] = (clone $complaintsQuery)->where('status', 'pertains_to_ge_const_isld')->count();

        $page = request()->get('page', 1);
        $perPage = 5;
        $recentComplaints = Complaint::with(['client', 'assignedEmployee'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $pendingApprovals = class_exists(\App\Models\SpareApprovalPerforma::class)
            ? \App\Models\SpareApprovalPerforma::with(['complaint.client', 'requestedBy', 'items.spare'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
            : collect();

        $lowStockItems = method_exists(Spare::class, 'lowStock')
            ? Spare::lowStock()->orderBy('stock_quantity', 'asc')->limit(10)->get()
            : collect();

        $overdueComplaints = Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])
            ->with(['client', 'assignedEmployee'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Get monthly complaints data (current year)
        $monthlyComplaints = [];
        $monthLabels = [];
        for ($i = 0; $i < 12; $i++) { // Jan to Dec
            $date = now()->startOfYear()->addMonths($i);
            $monthLabels[] = $date->format('M');
            $monthQuery = (clone $complaintsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $monthlyComplaints[] = $monthQuery->count();
        }

        // Get complaints by status with filters
        $complaintsByStatusQuery = (clone $complaintsQuery);
        $complaintsByStatus = $complaintsByStatusQuery
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get resolved vs recent ed data (year to date)
        $resolvedVsEdData = [];
        $recentEdData = [];
        $yearTdData = [];
        for ($i = 0; $i < 12; $i++) { // Jan to Dec
            $date = now()->startOfYear()->addMonths($i);
            $monthQuery = (clone $complaintsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            $recentEdData[] = $monthQuery->count();
            $resolvedData = (clone $monthQuery)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
            $resolvedVsEdData[] = $resolvedData;
            
            // Year TD (Year to Date) - cumulative from start of year
            $yearTdQuery = (clone $complaintsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', '<=', $date->month);
            $yearTdData[] = $yearTdQuery->count();
        }

        $employeePerformance = Employee::query()
            ->withCount(['assignedComplaints' => function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])
            ->orderBy('assigned_complaints_count', 'desc')
            ->limit(5)
            ->get();

        $slaPerformance = [
            'total' => 0,
            'within_sla' => 0,
            'breached' => 0,
            'sla_percentage' => 0,
        ];

        // If AJAX request, return JSON data
        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'monthlyComplaints' => $monthlyComplaints,
                'monthLabels' => $monthLabels,
                'complaintsByStatus' => $complaintsByStatus,
                'resolvedVsEdData' => $resolvedVsEdData,
                'recentEdData' => $recentEdData,
            ]);
        }

        return view('frontend.dashboard', compact(
            'stats',
            'geGroups',
            'geNodes',
            'categories',
            'statuses',
            'monthlyComplaints',
            'monthLabels',
            'complaintsByStatus',
            'resolvedVsEdData',
            'recentEdData',
            'yearTdData',
            'cityId',
            'sectorId',
            'category',
            'status',
            'dateRange'
        ));
    }
}


