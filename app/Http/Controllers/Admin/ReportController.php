<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Spare;
use App\Models\SpareApprovalPerforma;
use App\Models\EmployeeLeave;
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
        // Get real-time statistics
        $stats = $this->getRealTimeStats();
        $recentActivity = $this->getRecentActivity();
        
        // Get real data for JavaScript functions
        $realData = $this->getRealDataForJS();
        
        return view('admin.reports.index', compact('stats', 'recentActivity', 'realData'));
    }

    /**
     * Get real data for JavaScript functions
     */
    private function getRealDataForJS()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        
        return [
            'complaints' => [
                'total' => \App\Models\Complaint::count(),
                'resolved' => \App\Models\Complaint::where('status', 'resolved')
                    ->whereBetween('updated_at', [$startOfMonth, $now])
                    ->count(),
                'pending' => \App\Models\Complaint::where('status', '!=', 'resolved')->count(),
                'avg_resolution_time' => $this->getAverageResolutionTime()
            ],
            'employees' => [
                'total' => \App\Models\Employee::count(),
                'active' => \App\Models\Employee::whereHas('user', function($query) {
                    $query->where('status', 'active');
                })->count(),
                'on_leave' => \App\Models\EmployeeLeave::where('status', 'pending')->count(),
                'avg_performance' => $this->getAverageEmployeePerformance()
            ],
            'spares' => [
                'total_items' => \App\Models\Spare::count(),
                'low_stock' => \App\Models\Spare::where('stock_quantity', '<=', \DB::raw('threshold_level'))->count(),
                'out_of_stock' => \App\Models\Spare::where('stock_quantity', 0)->count(),
                'total_value' => \App\Models\Spare::sum(\DB::raw('stock_quantity * unit_price'))
            ],
            'financial' => [
                'total_costs' => $this->getTotalSpareCosts(),
                'approvals' => \App\Models\SpareApprovalPerforma::count(),
                'approved' => \App\Models\SpareApprovalPerforma::where('status', 'approved')->count(),
                'approval_rate' => $this->getApprovalRate()
            ]
        ];
    }

    /**
     * Get average resolution time in hours
     */
    private function getAverageResolutionTime()
    {
        $resolvedComplaints = \App\Models\Complaint::where('status', 'resolved')
            ->whereNotNull('updated_at')
            ->get();
            
        if ($resolvedComplaints->isEmpty()) {
            return 0;
        }
        
        $totalHours = $resolvedComplaints->sum(function($complaint) {
            return $complaint->created_at->diffInHours($complaint->updated_at);
        });
        
        return round($totalHours / $resolvedComplaints->count(), 1);
    }

    /**
     * Get average employee performance
     */
    private function getAverageEmployeePerformance()
    {
        $employees = \App\Models\Employee::with(['assignedComplaints' => function($query) {
            $query->where('status', 'resolved');
        }])->get();
        
        if ($employees->isEmpty()) {
            return 0;
        }
        
        $totalPerformance = $employees->sum(function($employee) {
            $totalComplaints = $employee->assignedComplaints->count();
            if ($totalComplaints === 0) return 0;
            
            $resolvedComplaints = $employee->assignedComplaints->where('status', 'resolved')->count();
            return ($resolvedComplaints / $totalComplaints) * 100;
        });
        
        return round($totalPerformance / $employees->count(), 1);
    }

    /**
     * Get total spare costs
     */
    private function getTotalSpareCosts()
    {
        return \DB::table('complaint_spares')
            ->join('spares', 'complaint_spares.spare_id', '=', 'spares.id')
            ->sum(\DB::raw('complaint_spares.quantity * spares.unit_price'));
    }

    /**
     * Get approval rate percentage
     */
    private function getApprovalRate()
    {
        $totalApprovals = \App\Models\SpareApprovalPerforma::count();
        if ($totalApprovals === 0) return 0;
        
        $approvedApprovals = \App\Models\SpareApprovalPerforma::where('status', 'approved')->count();
        return round(($approvedApprovals / $totalApprovals) * 100, 1);
    }

    /**
     * Get real-time statistics for dashboard
     */
    private function getRealTimeStats()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        
        return [
            'active_complaints' => Complaint::where('status', '!=', 'resolved')->count(),
            'resolved_this_month' => Complaint::where('status', 'resolved')
                ->whereBetween('updated_at', [$startOfMonth, $now])
                ->count(),
            'sla_compliance' => $this->calculateSlaCompliance(),
            'active_employees' => Employee::whereHas('user', function($query) {
                $query->where('status', 'active');
            })->count(),
            'total_spares' => Spare::count(),
            'low_stock_items' => Spare::where('stock_quantity', '<=', DB::raw('threshold_level'))->count(),
            'out_of_stock_items' => Spare::where('stock_quantity', 0)->count(),
            'total_approvals' => SpareApprovalPerforma::count(),
            'pending_approvals' => SpareApprovalPerforma::where('status', 'pending')->count(),
            'total_clients' => \App\Models\Client::count(),
            'active_clients' => \App\Models\Client::where('status', 'active')->count(),
            'total_spare_value' => Spare::sum(DB::raw('stock_quantity * unit_price')),
            'avg_resolution_time' => $this->getAverageResolutionTime(),
            'employee_performance' => $this->getAverageEmployeePerformance()
        ];
    }

    /**
     * Calculate SLA compliance percentage
     */
    private function calculateSlaCompliance()
    {
        $totalComplaints = Complaint::count();
        if ($totalComplaints === 0) return 100;
        
        // Get complaints that are resolved and within SLA time limits
        $compliantComplaints = Complaint::where('status', 'resolved')
            ->whereHas('slaRule', function($query) {
                $query->whereRaw('TIMESTAMPDIFF(HOUR, complaints.created_at, complaints.updated_at) <= sla_rules.max_resolution_time');
            })->count();
        
        return round(($compliantComplaints / $totalComplaints) * 100, 1);
    }

    /**
     * Get recent activity for dashboard
     */
    private function getRecentActivity()
    {
        $activities = collect();
        
        try {
            // Recent complaints
            $recentComplaints = Complaint::with(['client', 'assignedEmployee.user'])
                ->latest()
                ->limit(3)
                ->get();
                
            foreach ($recentComplaints as $complaint) {
                $activities->push([
                    'type' => 'complaint',
                    'title' => 'New complaint submitted',
                    'description' => $complaint->title,
                    'time' => $complaint->created_at->diffForHumans(),
                    'badge' => ucfirst($complaint->status),
                    'badge_class' => $this->getStatusBadgeClass($complaint->status)
                ]);
            }
            
            // Recent approvals
            $recentApprovals = SpareApprovalPerforma::with(['requestedBy.user'])
                ->latest()
                ->limit(2)
                ->get();
                
            foreach ($recentApprovals as $approval) {
                $activities->push([
                    'type' => 'approval',
                    'title' => 'Spare part approval',
                    'description' => 'Requested by ' . ($approval->requestedBy->user->username ?? 'Unknown'),
                    'time' => $approval->created_at->diffForHumans(),
                    'badge' => ucfirst($approval->status),
                    'badge_class' => $this->getApprovalBadgeClass($approval->status)
                ]);
            }
            
            // Recent employee activities
            $recentLeaves = EmployeeLeave::with(['employee.user'])
                ->where('status', 'pending')
                ->latest()
                ->limit(1)
                ->get();
                
            foreach ($recentLeaves as $leave) {
                $activities->push([
                    'type' => 'leave',
                    'title' => 'Employee leave request',
                    'description' => ($leave->employee->user->username ?? 'Unknown') . ' requested leave',
                    'time' => $leave->created_at->diffForHumans(),
                    'badge' => 'Pending',
                    'badge_class' => 'warning'
                ]);
            }
            
            // Recent spare part activities
            $recentSpares = Spare::where('stock_quantity', '<=', DB::raw('threshold_level'))
                ->latest('updated_at')
                ->limit(1)
                ->get();
                
            foreach ($recentSpares as $spare) {
                $activities->push([
                    'type' => 'spare',
                    'title' => 'Low stock alert',
                    'description' => $spare->item_name . ' is running low',
                    'time' => $spare->updated_at->diffForHumans(),
                    'badge' => 'Low Stock',
                    'badge_class' => 'warning'
                ]);
            }
            
        } catch (\Exception $e) {
            // If there's an error, return empty activities
            \Log::error('Error fetching recent activity: ' . $e->getMessage());
        }
        
        return $activities->sortByDesc('time')->take(5)->values();
    }

    /**
     * Get badge class for complaint status
     */
    private function getStatusBadgeClass($status)
    {
        switch (strtolower($status)) {
            case 'resolved':
                return 'success';
            case 'in_progress':
                return 'info';
            case 'pending':
                return 'warning';
            case 'closed':
                return 'secondary';
            default:
                return 'primary';
        }
    }

    /**
     * Get badge class for approval status
     */
    private function getApprovalBadgeClass($status)
    {
        switch (strtolower($status)) {
            case 'approved':
                return 'success';
            case 'pending':
                return 'warning';
            case 'rejected':
                return 'danger';
            default:
                return 'primary';
        }
    }

    /**
     * Generate complaint reports
     */
    public function complaints(Request $request)
    {
        // Set default values if not provided
        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        $groupBy = $request->group_by ?? 'status';
        $format = $request->format ?? 'html';

        // Validate only if parameters are provided
        if ($request->has('date_from') || $request->has('date_to')) {
            $request->validate([
                'date_from' => 'date',
                'date_to' => 'date|after_or_equal:date_from',
                'group_by' => 'nullable|in:status,type,priority,employee,client',
                'format' => 'nullable|in:html,pdf,excel',
            ]);
        }

        // Ensure dates are properly formatted and include time for full day coverage
        $dateFromStart = \Carbon\Carbon::parse($dateFrom)->startOfDay();
        $dateToEnd = \Carbon\Carbon::parse($dateTo)->endOfDay();
        
        // Use a base query and clone it for each computation to avoid mutation side-effects
        $baseQuery = Complaint::whereBetween('created_at', [$dateFromStart, $dateToEnd]);
        $query = (clone $baseQuery)->with(['client', 'assignedEmployee.user']);

        // Generate report data based on group_by
        switch ($groupBy) {
            case 'status':
                $data = (clone $baseQuery)->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get();
                break;

            case 'type':
                $data = (clone $baseQuery)->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->get();
                break;

            case 'priority':
                $data = (clone $baseQuery)->selectRaw('priority, COUNT(*) as count')
                    ->groupBy('priority')
                    ->get();
                break;

            case 'employee':
                $data = (clone $baseQuery)
                    ->whereNotNull('assigned_employee_id')
                    ->selectRaw('assigned_employee_id, COUNT(*) as count')
                    ->groupBy('assigned_employee_id')
                    ->get()
                    ->map(function($item) {
                        $item->assignedEmployee = \App\Models\Employee::with('user')->find($item->assigned_employee_id);
                        return $item;
                    });
                break;

            case 'client':
                $data = (clone $baseQuery)
                    ->selectRaw('client_id, COUNT(*) as count')
                    ->groupBy('client_id')
                    ->get()
                    ->map(function($item) {
                        $item->client = \App\Models\Client::find($item->client_id);
                        return $item;
                    });
                break;

            default:
                $data = (clone $baseQuery)->with(['client', 'assignedEmployee.user'])->get();
        }

        // Build summary metrics from clean clones of the base query
        $summary = [
            'total_complaints' => (clone $baseQuery)->count(),
            'resolved_complaints' => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count(),
            'pending_complaints' => (clone $baseQuery)->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'avg_resolution_time' => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
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
        // Set default values if not provided
        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        $department = $request->department;
        $format = $request->format ?? 'html';

        // Validate only if parameters are provided
        if ($request->has('date_from') || $request->has('date_to')) {
            $request->validate([
                'date_from' => 'date',
                'date_to' => 'date|after_or_equal:date_from',
                'department' => 'nullable|string',
                'format' => 'nullable|in:html,pdf,excel',
            ]);
        }

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
        // Set default values if not provided
        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        $category = $request->category;
        $format = $request->format ?? 'html';

        // Validate only if parameters are provided
        if ($request->has('date_from') || $request->has('date_to')) {
            $request->validate([
                'date_from' => 'date',
                'date_to' => 'date|after_or_equal:date_from',
                'category' => 'nullable|string',
                'format' => 'nullable|in:html,pdf,excel',
            ]);
        }

        $query = Spare::query();

        if ($category) {
            $query->where('category', $category);
        }

        $spares = $query->with(['complaintSpares' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('used_at', [$dateFrom, $dateTo]);
        }])->get()->map(function($spare) {
            $usage = $spare->complaintSpares;
            $totalUsed = $usage->sum('quantity');
            $totalCost = $usage->sum(function($item) use ($spare) {
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
        // Set default values if not provided
        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        $format = $request->format ?? 'html';

        // Validate only if parameters are provided
        if ($request->has('date_from') || $request->has('date_to')) {
            $request->validate([
                'date_from' => 'date',
                'date_to' => 'date|after_or_equal:date_from',
                'format' => 'nullable|in:html,pdf,excel',
            ]);
        }

        // Spare parts costs
        $spareCosts = DB::table('complaint_spares')
            ->join('spares', 'complaint_spares.spare_id', '=', 'spares.id')
            ->whereBetween('complaint_spares.used_at', [$dateFrom, $dateTo])
            ->selectRaw('spares.category, SUM(complaint_spares.quantity * spares.unit_price) as total_cost')
            ->groupBy('spares.category')
            ->get();

        // Approval costs - simplified approach
        $approvalCosts = SpareApprovalPerforma::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'approved')
            ->get()
            ->groupBy(function($approval) {
                return $approval->created_at->format('Y-m');
            })
            ->map(function($approvals) {
                return $approvals->count(); // For now, just count approvals per month
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
        // Make date parameters optional with defaults
        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        $format = $request->format ?? 'html';

        // Get SLA rules
        $slaRules = \App\Models\SlaRule::all()->keyBy('complaint_type');

        // Get complaints with SLA analysis
        $complaints = Complaint::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['client', 'assignedEmployee.user'])
            ->get()
            ->map(function($complaint) use ($slaRules) {
                $ageInHours = $complaint->created_at->diffInHours(now());
                
                // Get SLA rule for this complaint type
                $slaRule = $slaRules->get($complaint->category);
                $maxResponseTime = $slaRule ? $slaRule->max_response_time : 24; // Default 24 hours
                
                $isOverdue = $ageInHours > $maxResponseTime;
                $timeRemaining = max(0, $maxResponseTime - $ageInHours);
                
                // Calculate urgency level
                $urgencyLevel = 'low';
                if ($isOverdue) {
                    $urgencyLevel = 'critical';
                } elseif ($timeRemaining <= $maxResponseTime * 0.25) {
                    $urgencyLevel = 'high';
                } elseif ($timeRemaining <= $maxResponseTime * 0.5) {
                    $urgencyLevel = 'medium';
                }
                
                return [
                    'complaint' => $complaint,
                    'age_hours' => $ageInHours,
                    'max_response_time' => $maxResponseTime,
                    'time_remaining' => $timeRemaining,
                    'is_overdue' => $isOverdue,
                    'sla_status' => $isOverdue ? 'breached' : 'within_sla',
                    'urgency_level' => $urgencyLevel,
                    'sla_rule' => $slaRule,
                ];
            });

        // Calculate summary statistics
        $summary = [
            'total_complaints' => $complaints->count(),
            'within_sla' => $complaints->where('sla_status', 'within_sla')->count(),
            'breached_sla' => $complaints->where('sla_status', 'breached')->count(),
            'sla_compliance_rate' => $complaints->count() > 0 ? 
                round(($complaints->where('sla_status', 'within_sla')->count() / $complaints->count()) * 100, 2) : 0,
            'critical_urgent' => $complaints->where('urgency_level', 'critical')->count(),
            'high_priority' => $complaints->where('urgency_level', 'high')->count(),
            'average_resolution_time' => $complaints->filter(function($complaintData) {
                return $complaintData['complaint']->status === 'resolved';
            })->avg('age_hours') ?? 0,
        ];

        // Get SLA rules summary
        $slaRulesSummary = $slaRules->map(function($rule) use ($complaints) {
            $ruleComplaints = $complaints->filter(function($complaintData) use ($rule) {
                return $complaintData['complaint']->category === $rule->complaint_type;
            });
            return [
                'rule' => $rule,
                'total_complaints' => $ruleComplaints->count(),
                'within_sla' => $ruleComplaints->where('sla_status', 'within_sla')->count(),
                'breached_sla' => $ruleComplaints->where('sla_status', 'breached')->count(),
                'compliance_rate' => $ruleComplaints->count() > 0 ? 
                    round(($ruleComplaints->where('sla_status', 'within_sla')->count() / $ruleComplaints->count()) * 100, 2) : 0,
            ];
        });

        if ($format === 'html') {
            return view('admin.reports.sla', compact('complaints', 'summary', 'slaRulesSummary', 'dateFrom', 'dateTo'));
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
        try {
            if ($format === 'pdf') {
                return $this->exportToPDF($type, $data, $summary);
            } elseif ($format === 'excel') {
                return $this->exportToExcel($type, $data, $summary);
            } else {
                return $this->exportToJSON($type, $data, $summary);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report to PDF
     */
    private function exportToPDF($type, $data, $summary)
    {
        $filename = "{$type}_report_" . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        // For now, return JSON with download link
        // In production, you would use a PDF library like DomPDF or TCPDF
        return response()->json([
            'message' => 'PDF export functionality will be implemented with a PDF library',
            'filename' => $filename,
            'data' => $data,
            'summary' => $summary,
            'download_url' => route('admin.reports.download', ['type' => $type, 'format' => 'pdf'])
        ]);
    }

    /**
     * Export report to Excel
     */
    private function exportToExcel($type, $data, $summary)
    {
        $filename = "{$type}_report_" . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // For now, return JSON with download link
        // In production, you would use a library like Laravel Excel
        return response()->json([
            'message' => 'Excel export functionality will be implemented with Laravel Excel',
            'filename' => $filename,
            'data' => $data,
            'summary' => $summary,
            'download_url' => route('admin.reports.download', ['type' => $type, 'format' => 'excel'])
        ]);
    }

    /**
     * Export report to JSON
     */
    private function exportToJSON($type, $data, $summary)
    {
        $filename = "{$type}_report_" . now()->format('Y-m-d_H-i-s') . '.json';
        
        $exportData = [
            'report_type' => $type,
            'generated_at' => now()->toISOString(),
            'summary' => $summary,
            'data' => $data
        ];
        
        return response()->json($exportData)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Download report file
     */
    public function download($type, $format)
    {
        try {
            // Get report data based on type
            $request = new Request();
            $reportData = $this->getReportData($type, $request);
            
            if ($format === 'json') {
                return $this->exportToJSON($type, $reportData['data'], $reportData['summary']);
            } else {
                // For PDF and Excel, return a message with download instructions
                return response()->json([
                    'message' => "{$format} export functionality will be implemented with appropriate libraries",
                    'type' => $type,
                    'format' => $format,
                    'data' => $reportData
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report data based on type
     */
    private function getReportData($type, $request)
    {
        switch ($type) {
            case 'complaints':
                return $this->getComplaintsData($request);
            case 'employees':
                return $this->getEmployeesData($request);
            case 'spares':
                return $this->getSparesData($request);
            case 'financial':
                return $this->getFinancialData($request);
            default:
                throw new \Exception("Unknown report type: {$type}");
        }
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
