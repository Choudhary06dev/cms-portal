<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Spare;
use App\Models\EmployeeLeave;
use App\Models\SpareApprovalPerforma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display the admin dashboard
     */
    public function dashboard()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent complaints
        $recentComplaints = Complaint::with(['client', 'assignedEmployee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending approvals
        $pendingApprovals = SpareApprovalPerforma::with(['complaint', 'requestedBy', 'items.spare'])
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
            ->with(['client', 'assignedEmployee'])
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

        // Get monthly complaint trends
        $monthlyTrends = Complaint::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentComplaints',
            'pendingApprovals',
            'lowStockItems',
            'overdueComplaints',
            'complaintsByStatus',
            'complaintsByType',
            'monthlyTrends'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_complaints' => Complaint::count(),
            'pending_complaints' => Complaint::pending()->count(),
            'resolved_complaints' => Complaint::completed()->count(),
            'total_clients' => Client::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'total_spares' => Spare::count(),
            'low_stock_items' => Spare::lowStock()->count(),
            'out_of_stock_items' => Spare::outOfStock()->count(),
            'pending_approvals' => SpareApprovalPerforma::pending()->count(),
            'employees_on_leave' => EmployeeLeave::active()->count(),
        ];
    }

    /**
     * Get complaints summary for charts
     */
    public function getComplaintsSummary(Request $request)
    {
        $period = $request->get('period', '30'); // days
        
        $complaints = Complaint::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($complaints);
    }

    /**
     * Get employee performance data
     */
    public function getEmployeePerformance()
    {
        $employees = Employee::where('status', 'active')
            ->get()
            ->map(function($employee) {
                $metrics = $employee->getPerformanceMetrics();
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'department' => $employee->department,
                    'total_complaints' => $metrics['total_complaints'],
                    'resolved_complaints' => $metrics['resolved_complaints'],
                    'resolution_rate' => $metrics['resolution_rate'],
                ];
            });

        return response()->json($employees);
    }

    /**
     * Get spare parts consumption data
     */
    public function getSpareConsumption(Request $request)
    {
        $period = $request->get('period', '30'); // days
        
        $consumption = DB::table('complaint_spares')
            ->join('spares', 'complaint_spares.spare_id', '=', 'spares.id')
            ->where('complaint_spares.used_at', '>=', now()->subDays($period))
            ->selectRaw('spares.item_name, spares.category, SUM(complaint_spares.quantity) as total_quantity, SUM(complaint_spares.quantity * spares.unit_price) as total_cost')
            ->groupBy('spares.id', 'spares.item_name', 'spares.category')
            ->orderBy('total_quantity', 'desc')
            ->get();

        return response()->json($consumption);
    }

    /**
     * Get system health status
     */
    public function getSystemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'overdue_complaints' => Complaint::overdue()->count(),
            'low_stock_alerts' => Spare::lowStock()->count(),
            'pending_approvals' => SpareApprovalPerforma::pending()->count(),
        ];

        return response()->json($health);
    }

    /**
     * Check database health
     */
    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed'];
        }
    }

    /**
     * Check storage health
     */
    private function checkStorageHealth()
    {
        $totalSpace = disk_total_space(storage_path());
        $freeSpace = disk_free_space(storage_path());
        $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usedPercentage > 90) {
            return ['status' => 'critical', 'message' => 'Storage space critically low'];
        } elseif ($usedPercentage > 80) {
            return ['status' => 'warning', 'message' => 'Storage space running low'];
        } else {
            return ['status' => 'healthy', 'message' => 'Storage space adequate'];
        }
    }

    /**
     * Export dashboard data
     */
    public function exportDashboard(Request $request)
    {
        $format = $request->get('format', 'excel');
        $data = $this->getDashboardStats();
        
        // Add additional data for export
        $data['complaints_by_status'] = Complaint::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        $data['complaints_by_type'] = Complaint::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($data);
        } else {
            return $this->exportToExcel($data);
        }
    }

    /**
     * Export to PDF
     */
    private function exportToPdf($data)
    {
        // Implementation for PDF export
        // You can use libraries like DomPDF or TCPDF
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($data)
    {
        // Implementation for Excel export
        // You can use libraries like Laravel Excel
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    /**
     * Get notification count for admin
     */
    public function getNotificationCount()
    {
        $count = [
            'overdue_complaints' => Complaint::overdue()->count(),
            'pending_approvals' => SpareApprovalPerforma::pending()->count(),
            'low_stock_items' => Spare::lowStock()->count(),
            'new_complaints_today' => Complaint::whereDate('created_at', today())->count(),
        ];

        return response()->json($count);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request)
    {
        $type = $request->get('type');
        
        // Implementation for marking notifications as read
        // This could involve updating a notifications table or session
        
        return response()->json(['success' => true]);
    }

    /**
     * Get latest notifications for the topbar dropdown
     */
    public function getNotifications(Request $request)
    {
        // Build a simple aggregated notification feed (max 10)
        $notifications = collect();

        // 1) New complaints today
        $newComplaints = Complaint::with(['client'])
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', today())
            ->limit(5)
            ->get()
            ->map(function($c) {
                return [
                    'id' => 'complaint-'.$c->id,
                    'title' => 'New Complaint',
                    'message' => ($c->client->client_name ?? 'Client').': '.$c->title,
                    'type' => 'info',
                    'icon' => 'alert-circle',
                    'time' => $c->created_at->diffForHumans(),
                    'read' => false,
                    'url' => route('admin.complaints.show', $c->id),
                ];
            });

        // 2) Pending approvals
        $pendingApprovals = SpareApprovalPerforma::with(['complaint'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($a) {
                return [
                    'id' => 'approval-'.$a->id,
                    'title' => 'Approval Pending',
                    'message' => 'Performa #'.$a->id.' awaiting action',
                    'type' => 'warning',
                    'icon' => 'check-circle',
                    'time' => $a->created_at->diffForHumans(),
                    'read' => false,
                    'url' => route('admin.approvals.show', $a->id),
                ];
            });

        // 3) Low stock spares
        $lowStock = Spare::lowStock()
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get()
            ->map(function($s) {
                return [
                    'id' => 'spare-'.$s->id,
                    'title' => 'Low Stock',
                    'message' => $s->item_name.' stock at '.$s->stock_quantity,
                    'type' => 'danger',
                    'icon' => 'package',
                    'time' => now()->diffForHumans(),
                    'read' => false,
                    'url' => route('admin.spares.show', $s->id),
                ];
            });

        // 4) Overdue complaints
        $overdue = Complaint::overdue()
            ->with(['client'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get()
            ->map(function($c) {
                return [
                    'id' => 'overdue-'.$c->id,
                    'title' => 'Overdue Complaint',
                    'message' => ($c->client->client_name ?? 'Client').': '.$c->title,
                    'type' => 'danger',
                    'icon' => 'clock',
                    'time' => $c->created_at->diffForHumans(),
                    'read' => false,
                    'url' => route('admin.complaints.show', $c->id),
                ];
            });

        $notifications = $notifications
            ->merge($newComplaints)
            ->merge($pendingApprovals)
            ->merge($lowStock)
            ->merge($overdue)
            ->sortByDesc(function($n) { return strtotime($n['time']) ?: 0; })
            ->values()
            ->take(10);

        return response()->json([
            'unread' => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }
}
