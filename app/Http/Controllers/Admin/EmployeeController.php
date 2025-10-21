<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user.role']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('department', 'like', "%{$search}%")
              ->orWhere('designation', 'like', "%{$search}%")
              ->orWhere('biometric_id', 'like', "%{$search}%");
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        // Filter by designation
        if ($request->has('designation') && $request->designation) {
            $query->where('designation', $request->designation);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = Employee::getAvailableDepartments();
        $designations = Employee::getAvailableDesignations();

        return view('admin.employees.index', compact('employees', 'departments', 'designations'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = Employee::getAvailableDepartments();
        $designations = Employee::getAvailableDesignations();
        $roles = Role::whereIn('role_name', ['employee', 'manager'])->get();
        
        return view('admin.employees.create', compact('departments', 'designations', 'roles'));
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users',
            'full_name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'biometric_id' => 'nullable|string|max:50|unique:employees',
            'leave_quota' => 'required|integer|min:0|max:365',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user first
        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => 'active',
        ]);

        // Create employee record
        $employee = Employee::create([
            'user_id' => $user->id,
            'department' => $request->department,
            'designation' => $request->designation,
            'biometric_id' => $request->biometric_id,
            'leave_quota' => $request->leave_quota,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        $employee->load(['user.role', 'leaves', 'assignedComplaints.client', 'usedSpares.spare']);
        
        // Get performance metrics
        $performance = $employee->getPerformanceMetrics();
        
        // Get recent activities
        $recentActivities = $employee->assignedComplaints()
            ->with('client')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.employees.show', compact('employee', 'performance', 'recentActivities'));
    }

    /**
     * Show the form for editing the employee
     */
    public function edit(Employee $employee)
    {
        $departments = Employee::getAvailableDepartments();
        $designations = Employee::getAvailableDesignations();
        $roles = Role::whereIn('role_name', ['employee', 'manager'])->get();
        
        return view('admin.employees.edit', compact('employee', 'departments', 'designations', 'roles'));
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username,' . $employee->user_id,
            'full_name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:users,email,' . $employee->user_id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'biometric_id' => 'nullable|string|max:50|unique:employees,biometric_id,' . $employee->id,
            'leave_quota' => 'required|integer|min:0|max:365',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update user
        $updateUserData = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $updateUserData['password_hash'] = Hash::make($request->password);
        }

        $employee->user->update($updateUserData);

        // Update employee
        $employee->update([
            'department' => $request->department,
            'designation' => $request->designation,
            'biometric_id' => $request->biometric_id,
            'leave_quota' => $request->leave_quota,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee
     */
    public function destroy(Employee $employee)
    {
        // Check if employee has any related records
        if ($employee->assignedComplaints()->count() > 0 || 
            $employee->usedSpares()->count() > 0 || 
            $employee->requestedApprovals()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete employee with existing records.');
        }

        // Delete user (which will cascade to employee due to foreign key)
        $employee->user->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Toggle employee status
     */
    public function toggleStatus(Employee $employee)
    {
        $employee->user->update([
            'status' => $employee->user->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $employee->user->status === 'active' ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Employee {$status} successfully.");
    }

    /**
     * Get employee performance data
     */
    public function getPerformance(Employee $employee)
    {
        $performance = $employee->getPerformanceMetrics();
        
        // Get monthly performance data
        $monthlyData = $employee->assignedComplaints()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total, SUM(CASE WHEN status IN ("resolved", "closed") THEN 1 ELSE 0 END) as completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'performance' => $performance,
            'monthly_data' => $monthlyData
        ]);
    }

    /**
     * Get employee leave summary
     */
    public function getLeaveSummary(Employee $employee)
    {
        $leaves = $employee->leaves()
            ->selectRaw('leave_type, status, COUNT(*) as count, SUM(leave_days) as total_days')
            ->groupBy('leave_type', 'status')
            ->get();

        $summary = [
            'total_quota' => $employee->leave_quota,
            'used_leaves' => $employee->getTotalLeavesTaken(),
            'remaining_leaves' => $employee->getRemainingLeaves(),
            'pending_leaves' => $employee->getPendingLeaves(),
            'leaves_by_type' => $leaves->groupBy('leave_type'),
        ];

        return response()->json($summary);
    }

    /**
     * Get employee workload
     */
    public function getWorkload(Employee $employee)
    {
        $workload = [
            'assigned_complaints' => $employee->assignedComplaints()->count(),
            'pending_complaints' => $employee->assignedComplaints()->pending()->count(),
            'completed_this_month' => $employee->assignedComplaints()
                ->completed()
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'avg_resolution_time' => $employee->assignedComplaints()
                ->completed()
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
                ->value('avg_hours') ?? 0,
        ];

        return response()->json($workload);
    }

    /**
     * Bulk actions on employees
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,change_department,change_designation',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $employeeIds = $request->employee_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Employee::whereIn('id', $employeeIds)
                    ->whereHas('user')
                    ->get()
                    ->each(function($employee) {
                        $employee->user->update(['status' => 'active']);
                    });
                $message = 'Selected employees activated successfully.';
                break;

            case 'deactivate':
                Employee::whereIn('id', $employeeIds)
                    ->whereHas('user')
                    ->get()
                    ->each(function($employee) {
                        $employee->user->update(['status' => 'inactive']);
                    });
                $message = 'Selected employees deactivated successfully.';
                break;

            case 'change_department':
                $validator = Validator::make($request->all(), [
                    'department' => 'required|string|max:100',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Employee::whereIn('id', $employeeIds)->update(['department' => $request->department]);
                $message = 'Selected employees department changed successfully.';
                break;

            case 'change_designation':
                $validator = Validator::make($request->all(), [
                    'designation' => 'required|string|max:100',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                Employee::whereIn('id', $employeeIds)->update(['designation' => $request->designation]);
                $message = 'Selected employees designation changed successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export employees data
     */
    public function export(Request $request)
    {
        $query = Employee::with(['user.role']);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        if ($request->has('designation') && $request->designation) {
            $query->where('designation', $request->designation);
        }

        $employees = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}