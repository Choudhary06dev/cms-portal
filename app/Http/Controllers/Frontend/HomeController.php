<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Spare;
use App\Models\ComplaintCategory;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.home');
    }

    public function about()
    {
        return view('frontend.about');
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function features()
    {
        return view('frontend.features');
    }

    public function dashboard()
    {
        $stats = [
            'total_complaints' => Complaint::count(),
            'new_complaints' => Complaint::where('status', 'new')->count(),
            'pending_complaints' => Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_complaints' => Complaint::whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_complaints' => Complaint::whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'complaints_today' => Complaint::whereDate('created_at', now()->startOfDay())->count(),
            'complaints_this_month' => Complaint::where('created_at', '>=', now()->startOfMonth())->count(),
            'complaints_last_month' => Complaint::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_employees' => Employee::count(),
            'total_clients' => Client::count(),
            'total_spares' => Spare::count(),
            'low_stock_items' => method_exists(Spare::class, 'lowStock') ? Spare::lowStock()->count() : 0,
            'out_of_stock_items' => method_exists(Spare::class, 'outOfStock') ? Spare::outOfStock()->count() : 0,
            'total_spare_value' => Spare::query()->selectRaw('SUM(stock_quantity * unit_price) as total')->value('total') ?? 0,
            'pending_approvals' => class_exists(\App\Models\SpareApprovalPerforma::class) ? \App\Models\SpareApprovalPerforma::where('status', 'pending')->count() : 0,
            'approved_this_month' => class_exists(\App\Models\SpareApprovalPerforma::class) ? \App\Models\SpareApprovalPerforma::where('status', 'approved')->where('created_at', '>=', now()->startOfMonth())->count() : 0,
            'total_approvals' => class_exists(\App\Models\SpareApprovalPerforma::class) ? \App\Models\SpareApprovalPerforma::count() : 0,
            'rejected_approvals' => class_exists(\App\Models\SpareApprovalPerforma::class) ? \App\Models\SpareApprovalPerforma::where('status', 'rejected')->count() : 0,
        ];

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

        $complaintsByStatus = Complaint::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $complaintsByType = Complaint::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Get complaints by category - using ComplaintCategory model
        $categories = ComplaintCategory::all();
        $complaintsByCategory = [];
        
        foreach ($categories as $category) {
            $count = Complaint::where('category', $category->name)->count();
            $complaintsByCategory[$category->id] = [
                'name' => $category->name,
                'count' => $count,
                'description' => $category->description,
            ];
        }

        // Calculate percentage changes
        $lastMonthComplaints = Complaint::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->count();
        $thisMonthComplaints = Complaint::where('created_at', '>=', now()->startOfMonth())->count();
        $totalComplaintsChange = $lastMonthComplaints > 0 ? round((($thisMonthComplaints - $lastMonthComplaints) / $lastMonthComplaints) * 100) : 0;
        
        $lastMonthInProgress = Complaint::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])
            ->whereIn('status', ['new', 'assigned', 'in_progress'])->count();
        $thisMonthInProgress = Complaint::where('created_at', '>=', now()->startOfMonth())
            ->whereIn('status', ['new', 'assigned', 'in_progress'])->count();
        $inProgressChange = $lastMonthInProgress > 0 ? round((($thisMonthInProgress - $lastMonthInProgress) / $lastMonthInProgress) * 100) : 0;

        // Get weekly data for bar chart (last 7 days)
        $weeklyData = [];
        $lastWeekData = [];
        $weekDayLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyData[] = Complaint::whereDate('created_at', $date->toDateString())->count();
            $lastWeekDate = $date->copy()->subWeek();
            $lastWeekData[] = Complaint::whereDate('created_at', $lastWeekDate->toDateString())->count();
            // Get actual day name (Mon, Tue, etc.)
            $weekDayLabels[] = $date->format('D');
        }
        $maxWeeklyValue = max(max($weeklyData ?: [0]), max($lastWeekData ?: [0]), 1);

        // Calculate average for gauge
        $totalResolved = Complaint::whereIn('status', ['resolved', 'closed'])->count();
        $totalComplaints = Complaint::count();
        $averagePercentage = $totalComplaints > 0 ? round(($totalResolved / $totalComplaints) * 100, 2) : 0;

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

        $months = [];
        $complaintsTrend = [];
        $resolutionsTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $complaintsTrend[] = Complaint::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $resolutionsTrend[] = Complaint::whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
        }
        $monthlyTrends = [
            'months' => $months,
            'complaints' => $complaintsTrend,
            'resolutions' => $resolutionsTrend,
        ];

        return view('frontend.dashboard', compact(
            'stats',
            'recentComplaints',
            'pendingApprovals',
            'lowStockItems',
            'overdueComplaints',
            'complaintsByStatus',
            'complaintsByType',
            'complaintsByCategory',
            'categories',
            'totalComplaintsChange',
            'inProgressChange',
            'weeklyData',
            'lastWeekData',
            'weekDayLabels',
            'maxWeeklyValue',
            'averagePercentage',
            'employeePerformance',
            'slaPerformance',
            'monthlyTrends'
        ));
    }
}


