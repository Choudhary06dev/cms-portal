<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Spare;

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

        $recentComplaints = Complaint::with(['client', 'assignedEmployee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

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
            'employeePerformance',
            'slaPerformance',
            'monthlyTrends'
        ));
    }
}


