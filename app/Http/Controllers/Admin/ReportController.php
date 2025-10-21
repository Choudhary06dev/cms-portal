<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Spare;
use App\Models\SpareApprovalPerforma;
use App\Models\ReportsSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display the reports dashboard
     */
    public function index()
    {
        $reportTypes = ReportsSummary::getReportTypes();
        $recentReports = ReportsSummary::recent(7)->get();
        
        return view('admin.reports.index', compact('reportTypes', 'recentReports'));
    }

    /**
     * Generate complaint reports
     */
    public function complaints(Request $request)
    {
        $validator = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'nullable|in:status,type,priority,employee,client',
            'format' => 'nullable|in:html,pdf,excel',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $groupBy = $request->group_by ?? 'status';
        $format = $request->format ?? 'html';

        $query = Complaint::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['client', 'assignedEmployee.user']);

        // Generate report data based on group_by
        switch ($groupBy) {
            case 'status':
                $data = $query->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get();
                break;

            case 'type':
                $data = $query->selectRaw('complaint_type, COUNT(*) as count')
                    ->groupBy('complaint_type')
                    ->get();
                break;

            case 'priority':
                $data = $query->selectRaw('priority, COUNT(*) as count')
                    ->groupBy('priority')
                    ->get();
                break;

            case 'employee':
                $data = $query->selectRaw('assigned_to, COUNT(*) as count')
                    ->whereNotNull('assigned_to')
                    ->groupBy('assigned_to')
                    ->with('assignedEmployee.user')
                    ->get();
                break;

            case 'client':
                $data = $query->selectRaw('client_id, COUNT(*) as count')
                    ->groupBy('client_id')
                    ->with('client')
                    ->get();
                break;

            default:
                $data = $query->get();
        }

        $summary = [
            'total_complaints' => $query->count(),
            'resolved_complaints' => $query->whereIn('status', ['resolved', 'closed'])->count(),
            'pending_complaints' => $query->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'avg_resolution_time' => $query->whereIn('status', ['resolved', 'closed'])
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
                ->value('avg_hours') ?? 0,
        ];

        if ($format === 'html') {
            return view('admin.reports.complaints', compact('data', 'summary', 'dateFrom', 'dateTo', 'groupBy'));
        } else {
            return $this->exportReport('complaints', $data, $summary, $format);
        }
    }

    /**
     * Generate employee performance reports
     */
    public function employees(Request $request)
    {
        $validator = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'department' => 'nullable|string',
            'format' => 'nullable|in:html,pdf,excel',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $department = $request->department;
        $format = $request->format ?? 'html';

        $query = Employee::with(['user', 'assignedComplaints' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('created_at', [$dateFrom, $dateTo]);
        }]);

        if ($department) {
            $query->where('department', $department);
        }

        $employees = $query->get()->map(function($employee) {
            $complaints = $employee->assignedComplaints;
            $resolved = $complaints->whereIn('status', ['resolved', 'closed']);
            
            return [
                'employee' => $employee,
                'total_complaints' => $complaints->count(),
                'resolved_complaints' => $resolved->count(),
                'resolution_rate' => $complaints->count() > 0 ? round(($resolved->count() / $complaints->count()) * 100, 2) : 0,
                'avg_resolution_time' => $resolved->avg(function($complaint) {
                    return $complaint->created_at->diffInHours($complaint->updated_at);
                }) ?? 0,
            ];
        });

        $summary = [
            'total_employees' => $employees->count(),
            'avg_resolution_rate' => $employees->avg('resolution_rate'),
            'top_performer' => $employees->sortByDesc('resolution_rate')->first(),
        ];

        if ($format === 'html') {
            return view('admin.reports.employees', compact('employees', 'summary', 'dateFrom', 'dateTo', 'department'));
        } else {
            return $this->exportReport('employees', $employees, $summary, $format);
        }
    }

    /**
     * Generate spare parts reports
     */
    public function spares(Request $request)
    {
        $validator = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'category' => 'nullable|string',
            'format' => 'nullable|in:html,pdf,excel',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $category = $request->category;
        $format = $request->format ?? 'html';

        $query = Spare::query();

        if ($category) {
            $query->where('category', $category);
        }

        $spares = $query->with(['complaintSpares' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('used_at', [$dateFrom, $dateTo]);
        }])->get()->map(function($spare) {
            $usage = $spare->complaintSpares;
            $totalUsed = $usage->sum('quantity');
            $totalCost = $usage->sum(function($item) {
                return $item->quantity * $spare->unit_price;
            });

            return [
                'spare' => $spare,
                'total_used' => $totalUsed,
                'total_cost' => $totalCost,
                'usage_count' => $usage->count(),
                'current_stock' => $spare->stock_quantity,
                'stock_status' => $spare->getStockStatusAttribute(),
            ];
        });

        $summary = [
            'total_spares' => $spares->count(),
            'total_consumption' => $spares->sum('total_cost'),
            'low_stock_items' => $spares->where('stock_status', 'low_stock')->count(),
            'out_of_stock_items' => $spares->where('stock_status', 'out_of_stock')->count(),
        ];

        if ($format === 'html') {
            return view('admin.reports.spares', compact('spares', 'summary', 'dateFrom', 'dateTo', 'category'));
        } else {
            return $this->exportReport('spares', $spares, $summary, $format);
        }
    }

    /**
     * Generate financial reports
     */
    public function financial(Request $request)
    {
        $validator = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'nullable|in:html,pdf,excel',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $format = $request->format ?? 'html';

        // Spare parts costs
        $spareCosts = DB::table('complaint_spares')
            ->join('spares', 'complaint_spares.spare_id', '=', 'spares.id')
            ->whereBetween('complaint_spares.used_at', [$dateFrom, $dateTo])
            ->selectRaw('spares.category, SUM(complaint_spares.quantity * spares.unit_price) as total_cost')
            ->groupBy('spares.category')
            ->get();

        // Approval costs
        $approvalCosts = SpareApprovalPerforma::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'approved')
            ->with(['items.spare'])
            ->get()
            ->groupBy(function($approval) {
                return $approval->created_at->format('Y-m');
            })
            ->map(function($approvals) {
                return $approvals->sum('getTotalEstimatedCostAttribute');
            });

        $summary = [
            'total_spare_costs' => $spareCosts->sum('total_cost'),
            'total_approvals' => SpareApprovalPerforma::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'approved_approvals' => SpareApprovalPerforma::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'approved')->count(),
            'category_breakdown' => $spareCosts,
            'monthly_approvals' => $approvalCosts,
        ];

        if ($format === 'html') {
            return view('admin.reports.financial', compact('summary', 'dateFrom', 'dateTo'));
        } else {
            return $this->exportReport('financial', $summary, $summary, $format);
        }
    }

    /**
     * Generate SLA reports
     */
    public function sla(Request $request)
    {
        $validator = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'nullable|in:html,pdf,excel',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $format = $request->format ?? 'html';

        $complaints = Complaint::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['client', 'assignedEmployee.user'])
            ->get()
            ->map(function($complaint) {
                $ageInHours = $complaint->created_at->diffInHours(now());
                $isOverdue = $ageInHours > 24; // Default SLA of 24 hours
                
                return [
                    'complaint' => $complaint,
                    'age_hours' => $ageInHours,
                    'is_overdue' => $isOverdue,
                    'sla_status' => $isOverdue ? 'breached' : 'within_sla',
                ];
            });

        $summary = [
            'total_complaints' => $complaints->count(),
            'within_sla' => $complaints->where('sla_status', 'within_sla')->count(),
            'breached_sla' => $complaints->where('sla_status', 'breached')->count(),
            'sla_compliance_rate' => $complaints->count() > 0 ? 
                round(($complaints->where('sla_status', 'within_sla')->count() / $complaints->count()) * 100, 2) : 0,
        ];

        if ($format === 'html') {
            return view('admin.reports.sla', compact('complaints', 'summary', 'dateFrom', 'dateTo'));
        } else {
            return $this->exportReport('sla', $complaints, $summary, $format);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $stats = [
            'total_complaints' => Complaint::count(),
            'resolved_complaints' => Complaint::whereIn('status', ['resolved', 'closed'])->count(),
            'pending_complaints' => Complaint::pending()->count(),
            'overdue_complaints' => Complaint::overdue()->count(),
            'total_employees' => Employee::whereHas('user', function($q) {
                $q->where('status', 'active');
            })->count(),
            'total_clients' => Client::count(),
            'total_spares' => Spare::count(),
            'low_stock_items' => Spare::lowStock()->count(),
            'pending_approvals' => SpareApprovalPerforma::pending()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'complaints');
        $period = $request->get('period', '30'); // days

        switch ($type) {
            case 'complaints':
                $data = Complaint::where('created_at', '>=', now()->subDays($period))
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'spares':
                $data = DB::table('complaint_spares')
                    ->join('spares', 'complaint_spares.spare_id', '=', 'spares.id')
                    ->where('complaint_spares.used_at', '>=', now()->subDays($period))
                    ->selectRaw('DATE(complaint_spares.used_at) as date, SUM(complaint_spares.quantity * spares.unit_price) as total_cost')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'employees':
                $data = Employee::whereHas('user', function($q) {
                    $q->where('status', 'active');
                })
                ->withCount(['assignedComplaints' => function($q) use ($period) {
                    $q->where('created_at', '>=', now()->subDays($period))
                      ->whereIn('status', ['resolved', 'closed']);
                }])
                ->get()
                ->map(function($employee) {
                    return [
                        'name' => $employee->getFullNameAttribute(),
                        'resolved' => $employee->assigned_complaints_count,
                    ];
                });
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }

    /**
     * Export report
     */
    private function exportReport($type, $data, $summary, $format)
    {
        // Implementation for export (PDF, Excel, etc.)
        return response()->json([
            'message' => "Export functionality for {$type} report in {$format} format not implemented yet",
            'data' => $data,
            'summary' => $summary
        ]);
    }

    /**
     * Save report to cache
     */
    public function saveReport(Request $request)
    {
        $validator = $request->validate([
            'report_type' => 'required|in:complaints,spares,employees',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'data' => 'required|array',
        ]);

        $report = ReportsSummary::getOrCreate(
            $request->report_type,
            $request->period_start,
            $request->period_end
        );

        $report->updateData($request->data);

        return response()->json(['success' => true, 'report_id' => $report->id]);
    }

    /**
     * Get cached report
     */
    public function getCachedReport(ReportsSummary $report)
    {
        return response()->json([
            'report' => $report,
            'data' => $report->data_json,
            'summary' => $report->getSummaryAttribute()
        ]);
    }
}
