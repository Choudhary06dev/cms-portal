<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminController as AdminController;
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
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Default Routes
|--------------------------------------------------------------------------
*/
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

// Legacy redirects
Route::redirect('/login', '/admin/login');
Route::redirect('/register', '/admin/register');
Route::redirect('/admin/rdashboard', '/admin/dashboard');

// Dashboard (for verified users)
Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Theme management
    Route::post('/theme', [App\Http\Controllers\ThemeController::class, 'update'])->name('theme.update');
    Route::get('/theme', [App\Http\Controllers\ThemeController::class, 'get'])->name('theme.get');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ===============================
    // 🏠 Dashboard
    // ===============================
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Notifications API
    Route::get('/notifications/api', [AdminController::class, 'getNotifications'])->name('notifications.api');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/real-time-updates', [AdminDashboardController::class, 'getRealTimeUpdates'])->name('dashboard.real-time-updates');
    
    // ===============================
    // 👤 User Management
    // ===============================
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
    
    // ===============================
    // 🧩 Role Management
    // ===============================
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::resource('roles', AdminRoleController::class)->except(['destroy']);
        Route::get('roles/{role}/permissions', [AdminRoleController::class, 'showPermissions'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [AdminRoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        Route::get('roles/statistics', [AdminRoleController::class, 'getStatistics'])->name('roles.statistics');
        Route::post('roles/bulk-action', [AdminRoleController::class, 'bulkAction'])->name('roles.bulk-action');
        Route::get('roles/export', [AdminRoleController::class, 'export'])->name('roles.export');
    });
    Route::middleware(['permission:roles.delete'])->delete('roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');

    // ===============================
    // 👨‍💼 Employee Management
    // ===============================
    Route::middleware(['permission:employees.view'])->group(function () {
        // Resource routes
        Route::resource('employees', AdminEmployeeController::class)->except(['destroy']);
        
        // Extra AJAX/helper routes
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
    
    // Delete routes
    Route::middleware(['permission:employees.delete'])->group(function () {
        Route::delete('employees/{employee}', [AdminEmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // ===============================
    // 👥 Clients
    // ===============================
    Route::middleware(['permission:clients.view'])->group(function () {
        Route::resource('clients', AdminClientController::class);
        Route::post('clients/{client}/toggle-status', [AdminClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::get('clients/{client}/complaints', [AdminClientController::class, 'getComplaints'])->name('clients.complaints');
        Route::get('clients/export', [AdminClientController::class, 'export'])->name('clients.export');
    });
    
    // ===============================
    // 🛠 Complaints
    // ===============================
    Route::middleware(['permission:complaints.view'])->group(function () {
        Route::resource('complaints', AdminComplaintController::class);
        Route::post('complaints/{complaint}/assign', [AdminComplaintController::class, 'assign'])->name('complaints.assign');
        Route::post('complaints/{complaint}/update-status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update-status');
        Route::post('complaints/{complaint}/add-spare-parts', [AdminComplaintController::class, 'addSpareParts'])->name('complaints.add-spare-parts');
        Route::get('complaints/{complaint}/print-slip', [AdminComplaintController::class, 'printSlip'])->name('complaints.print-slip');
    });

    // ===============================
    // ⚙️ Spares, Approvals, SLA, Reports, Settings, Help
    // ===============================
    Route::resource('spares', AdminSpareController::class)->middleware(['permission:spares.view']);
    Route::get('spares/{spare}/edit-data', [AdminSpareController::class, 'editData'])->name('spares.edit-data');
    Route::resource('approvals', AdminApprovalController::class)->middleware(['permission:approvals.view']);
    Route::post('approvals/{approval}/approve', [AdminApprovalController::class, 'approve'])->middleware(['permission:approvals.view'])->name('approvals.approve');
    Route::post('approvals/{approval}/reject', [AdminApprovalController::class, 'reject'])->middleware(['permission:approvals.view'])->name('approvals.reject');
    Route::post('approvals/bulk-action', [AdminApprovalController::class, 'bulkAction'])->middleware(['permission:approvals.view'])->name('approvals.bulk-action');
    Route::resource('sla', AdminSlaController::class)->middleware(['permission:sla.view']);
    Route::post('sla/{sla}/toggle-status', [AdminSlaController::class, 'toggleStatus'])->name('sla.toggle-status');
    Route::get('reports', [AdminReportController::class, 'index'])->middleware(['permission:reports.view'])->name('reports.index');
    Route::get('reports/complaints', [AdminReportController::class, 'complaints'])->middleware(['permission:reports.view'])->name('reports.complaints');
    Route::get('reports/spares', [AdminReportController::class, 'spares'])->middleware(['permission:reports.view'])->name('reports.spares');
    Route::get('reports/employees', [AdminReportController::class, 'employees'])->middleware(['permission:reports.view'])->name('reports.employees');
    Route::get('reports/financial', [AdminReportController::class, 'financial'])->middleware(['permission:reports.view'])->name('reports.financial');
    Route::get('reports/sla', [AdminReportController::class, 'sla'])->middleware(['permission:reports.view'])->name('reports.sla');
    Route::get('reports/download/{type}/{format}', [AdminReportController::class, 'download'])->middleware(['permission:reports.view'])->name('reports.download');
    
    // Debug route for testing reports
    Route::get('reports/test', function() {
        return response()->json([
            'message' => 'Reports routes are working!',
            'timestamp' => now(),
            'routes' => [
                'complaints' => route('admin.reports.complaints'),
                'employees' => route('admin.reports.employees'),
                'spares' => route('admin.reports.spares'),
                'financial' => route('admin.reports.financial'),
            ]
        ]);
    })->name('reports.test');

    // ===============================
    // 🔍 Search
    // ===============================
    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('search/api', [SearchController::class, 'api'])->name('search.api');
    

    // Settings & Help
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/general', [App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('settings/notifications', [App\Http\Controllers\Admin\SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('settings/security', [App\Http\Controllers\Admin\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::get('help', [App\Http\Controllers\Admin\HelpController::class, 'index'])->name('help.index');
    Route::get('help/documentation', [App\Http\Controllers\Admin\HelpController::class, 'documentation'])->name('help.documentation');
    Route::get('help/faq', [App\Http\Controllers\Admin\HelpController::class, 'faq'])->name('help.faq');
    Route::get('help/contact', [App\Http\Controllers\Admin\HelpController::class, 'contact'])->name('help.contact');
    Route::post('help/contact', [App\Http\Controllers\Admin\HelpController::class, 'submitTicket'])->name('help.submit-ticket');
});
