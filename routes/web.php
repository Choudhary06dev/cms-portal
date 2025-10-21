<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\ComplaintCrudController as AdminComplaintCrudController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\SpareController as AdminSpareController;
use App\Http\Controllers\Admin\ApprovalController as AdminApprovalController;
use App\Http\Controllers\Admin\SlaController as AdminSlaController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::get('/admin', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

// Legacy redirects from root auth to admin auth
Route::redirect('/login', '/admin/login');
Route::redirect('/register', '/admin/register');

// Typo/alias redirect for dashboard
Route::redirect('/admin/rdashboard', '/admin/dashboard');

Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Theme management
    Route::post('/theme', [App\Http\Controllers\ThemeController::class, 'update'])->name('theme.update');
    Route::get('/theme', [App\Http\Controllers\ThemeController::class, 'get'])->name('theme.get');
});

require __DIR__.'/auth.php';

// Admin routes
Route::middleware(['auth', 'verified', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/real-time-updates', [AdminDashboardController::class, 'getRealTimeUpdates'])->name('dashboard.real-time-updates');
    
    // User Management (Admin only)
    Route::middleware(['permission:users.view'])->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('users/{user}/permissions', [AdminUserController::class, 'getPermissions'])->name('users.permissions');
        Route::post('users/{user}/permissions', [AdminUserController::class, 'updatePermissions'])->name('users.update-permissions');
        Route::get('users/{user}/activity', [AdminUserController::class, 'getActivityLog'])->name('users.activity');
        Route::post('users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
        Route::get('users/export', [AdminUserController::class, 'export'])->name('users.export');
    });
    
    // Role Management (Admin only)
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::resource('roles', AdminRoleController::class)->except(['destroy']);
        Route::post('roles/{role}/toggle-status', [AdminRoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::get('roles/{role}/permissions', [AdminRoleController::class, 'showPermissions'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [AdminRoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        Route::get('roles/statistics', [AdminRoleController::class, 'getStatistics'])->name('roles.statistics');
        Route::get('roles/usage-statistics', [AdminRoleController::class, 'getUsageStatistics'])->name('roles.usage-statistics');
        Route::post('roles/bulk-action', [AdminRoleController::class, 'bulkAction'])->name('roles.bulk-action');
        Route::get('roles/export', [AdminRoleController::class, 'export'])->name('roles.export');
    });
    
    // Role delete requires delete permission
    Route::middleware(['permission:roles.delete'])->group(function () {
        Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Test permissions (remove in production)
    // Route::get('test-permissions', function() {
    //     $roles = \App\Models\Role::with('rolePermissions')->get();
    //     return view('admin.test-permissions', compact('roles'));
    // })->name('test-permissions');
    
    // Employee Management
    Route::middleware(['permission:employees.view'])->group(function () {
        Route::resource('employees', AdminEmployeeController::class);
        Route::get('employees/{employee}/edit-data', [AdminEmployeeController::class, 'getEditData'])->name('employees.edit-data');
        Route::post('employees/{employee}/toggle-status', [AdminEmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::get('employees/{employee}/leaves', [AdminEmployeeController::class, 'getLeaves'])->name('employees.leaves');
        Route::post('employees/{employee}/leaves', [AdminEmployeeController::class, 'createLeave'])->name('employees.create-leave');
        Route::post('employees/{employee}/leaves/{leave}/approve', [AdminEmployeeController::class, 'approveLeave'])->name('employees.approve-leave');
        Route::post('employees/{employee}/leaves/{leave}/reject', [AdminEmployeeController::class, 'rejectLeave'])->name('employees.reject-leave');
        Route::get('employees/{employee}/performance', [AdminEmployeeController::class, 'getPerformance'])->name('employees.performance');
        Route::post('employees/bulk-action', [AdminEmployeeController::class, 'bulkAction'])->name('employees.bulk-action');
        Route::get('employees/export', [AdminEmployeeController::class, 'export'])->name('employees.export');
    });
    
    // Client Management
    Route::middleware(['permission:clients.view'])->group(function () {
        Route::resource('clients', AdminClientController::class);
        Route::post('clients/{client}/toggle-status', [AdminClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::get('clients/{client}/complaints', [AdminClientController::class, 'getComplaints'])->name('clients.complaints');
        Route::get('clients/{client}/performance', [AdminClientController::class, 'getPerformanceMetrics'])->name('clients.performance');
        Route::get('clients/statistics', [AdminClientController::class, 'getStatistics'])->name('clients.statistics');
        Route::get('clients/chart-data', [AdminClientController::class, 'getChartData'])->name('clients.chart-data');
        Route::get('clients/top-clients', [AdminClientController::class, 'getTopClients'])->name('clients.top-clients');
        Route::post('clients/bulk-action', [AdminClientController::class, 'bulkAction'])->name('clients.bulk-action');
        Route::get('clients/export', [AdminClientController::class, 'export'])->name('clients.export');
    });
    
    // Complaint Management
    Route::middleware(['permission:complaints.view'])->group(function () {
        Route::resource('complaints', AdminComplaintController::class);
        Route::post('complaints/{complaint}/assign', [AdminComplaintController::class, 'assign'])->name('complaints.assign');
        Route::post('complaints/{complaint}/update-status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update-status');
        Route::post('complaints/{complaint}/add-notes', [AdminComplaintController::class, 'addNotes'])->name('complaints.add-notes');
        Route::get('complaints/{complaint}/print-slip', [AdminComplaintController::class, 'printSlip'])->name('complaints.print-slip');
        Route::get('complaints/statistics', [AdminComplaintController::class, 'getStatistics'])->name('complaints.statistics');
        Route::get('complaints/chart-data', [AdminComplaintController::class, 'getChartData'])->name('complaints.chart-data');
        Route::get('complaints/by-type', [AdminComplaintController::class, 'getByType'])->name('complaints.by-type');
        Route::get('complaints/overdue', [AdminComplaintController::class, 'getOverdue'])->name('complaints.overdue');
        Route::get('complaints/employee-performance', [AdminComplaintController::class, 'getEmployeePerformance'])->name('complaints.employee-performance');
        Route::post('complaints/bulk-action', [AdminComplaintController::class, 'bulkAction'])->name('complaints.bulk-action');
        Route::get('complaints/export', [AdminComplaintController::class, 'export'])->name('complaints.export');
    });
    
    // Spare Parts Management
    Route::middleware(['permission:spares.view'])->group(function () {
        Route::resource('spares', AdminSpareController::class);
        Route::post('spares/{spare}/add-stock', [AdminSpareController::class, 'addStock'])->name('spares.add-stock');
        Route::post('spares/{spare}/remove-stock', [AdminSpareController::class, 'removeStock'])->name('spares.remove-stock');
        Route::get('spares/low-stock', [AdminSpareController::class, 'getLowStock'])->name('spares.low-stock');
        Route::get('spares/out-of-stock', [AdminSpareController::class, 'getOutOfStock'])->name('spares.out-of-stock');
        Route::get('spares/stock-alerts', [AdminSpareController::class, 'getStockAlerts'])->name('spares.stock-alerts');
        Route::get('spares/{spare}/stock-movement-chart', [AdminSpareController::class, 'getStockMovementChart'])->name('spares.stock-movement-chart');
        Route::get('spares/{spare}/usage-statistics', [AdminSpareController::class, 'getUsageStatistics'])->name('spares.usage-statistics');
        Route::get('spares/top-used', [AdminSpareController::class, 'getTopUsedSpares'])->name('spares.top-used');
        Route::get('spares/category-statistics', [AdminSpareController::class, 'getCategoryStatistics'])->name('spares.category-statistics');
        Route::post('spares/bulk-action', [AdminSpareController::class, 'bulkAction'])->name('spares.bulk-action');
        Route::get('spares/export', [AdminSpareController::class, 'export'])->name('spares.export');
    });
    
    // Approval Management
    Route::middleware(['permission:approvals.view'])->group(function () {
        Route::resource('approvals', AdminApprovalController::class)->except(['edit', 'store', 'update', 'destroy']);
        Route::post('approvals/{approval}/approve', [AdminApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{approval}/reject', [AdminApprovalController::class, 'reject'])->name('approvals.reject');
        Route::get('approvals/statistics', [AdminApprovalController::class, 'getStatistics'])->name('approvals.statistics');
        Route::get('approvals/chart-data', [AdminApprovalController::class, 'getChartData'])->name('approvals.chart-data');
        Route::get('approvals/monthly-trends', [AdminApprovalController::class, 'getMonthlyTrends'])->name('approvals.monthly-trends');
        Route::get('approvals/overdue', [AdminApprovalController::class, 'getOverdueApprovals'])->name('approvals.overdue');
        Route::get('approvals/employee-performance', [AdminApprovalController::class, 'getEmployeePerformance'])->name('approvals.employee-performance');
        Route::get('approvals/cost-analysis', [AdminApprovalController::class, 'getCostAnalysis'])->name('approvals.cost-analysis');
        Route::post('approvals/bulk-action', [AdminApprovalController::class, 'bulkAction'])->name('approvals.bulk-action');
        Route::get('approvals/export', [AdminApprovalController::class, 'export'])->name('approvals.export');
    });
    
    // SLA Management
    Route::middleware(['permission:sla.view'])->group(function () {
        Route::resource('sla', AdminSlaController::class)->parameters(['sla' => 'slaRule']);
        Route::post('sla/{slaRule}/toggle-status', [AdminSlaController::class, 'toggleStatus'])->name('sla.toggle-status');
        Route::get('sla/statistics', [AdminSlaController::class, 'getStatistics'])->name('sla.statistics');
        Route::get('sla/chart-data', [AdminSlaController::class, 'getChartData'])->name('sla.chart-data');
        Route::get('sla/breach-analysis', [AdminSlaController::class, 'getBreachAnalysis'])->name('sla.breach-analysis');
        Route::get('sla/performance-by-type', [AdminSlaController::class, 'getPerformanceByType'])->name('sla.performance-by-type');
        Route::get('sla/escalation-alerts', [AdminSlaController::class, 'getEscalationAlerts'])->name('sla.escalation-alerts');
        Route::post('sla/{slaRule}/test', [AdminSlaController::class, 'testSlaRule'])->name('sla.test');
        Route::post('sla/bulk-action', [AdminSlaController::class, 'bulkAction'])->name('sla.bulk-action');
        Route::get('sla/export', [AdminSlaController::class, 'export'])->name('sla.export');
    });
    
    // Reports
    Route::middleware(['permission:reports.view'])->group(function () {
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/complaints', [AdminReportController::class, 'complaints'])->name('reports.complaints');
        Route::get('reports/employees', [AdminReportController::class, 'employees'])->name('reports.employees');
        Route::get('reports/spares', [AdminReportController::class, 'spares'])->name('reports.spares');
        Route::get('reports/sla', [AdminReportController::class, 'sla'])->name('reports.sla');
        Route::get('reports/financial', [AdminReportController::class, 'financial'])->name('reports.financial');
        Route::get('reports/performance', [AdminReportController::class, 'performance'])->name('reports.performance');
        Route::get('reports/export/{type}', [AdminReportController::class, 'export'])->name('reports.export');
    });
    
    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/general', [App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('settings/notifications', [App\Http\Controllers\Admin\SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('settings/security', [App\Http\Controllers\Admin\SettingsController::class, 'updateSecurity'])->name('settings.security');
    
    // Help & Support
    Route::get('help', [App\Http\Controllers\Admin\HelpController::class, 'index'])->name('help.index');
    Route::get('help/faq', [App\Http\Controllers\Admin\HelpController::class, 'faq'])->name('help.faq');
    Route::get('help/documentation', [App\Http\Controllers\Admin\HelpController::class, 'documentation'])->name('help.documentation');
    Route::get('help/contact', [App\Http\Controllers\Admin\HelpController::class, 'contact'])->name('help.contact');
    Route::post('help/contact', [App\Http\Controllers\Admin\HelpController::class, 'submitTicket'])->name('help.submit-ticket');
});
