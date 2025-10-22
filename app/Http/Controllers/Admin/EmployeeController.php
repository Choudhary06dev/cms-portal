<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class EmployeeController extends Controller
{
    public function __construct()
    {
        // Middleware is handled in routes/web.php
    }

    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        $employees = Employee::with('user.role')->latest()->paginate(10);
        $roles = Role::all();
        
        return view('admin.employees.index', compact('employees', 'roles'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        // Clear any old input data to ensure clean form
        request()->session()->forget('_old_input');
        
        $users = User::whereDoesntHave('employee')->get();
        $roles = Role::all();
        
        $response = response()->view('admin.employees.create', compact('users', 'roles'));
        
        // Add cache-busting headers
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'nullable|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'biometric_id' => 'nullable|string|max:50|unique:employees,biometric_id',
            'leave_quota' => 'required|integer|min:0|max:365',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create user first
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password_hash' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'status' => 'active',
                'theme' => $request->theme ?? 'light',
            ]);

            // Create employee record
            $employee = Employee::create([
                'user_id' => $user->id,
                'department' => $request->department,
                'designation' => $request->designation,
                'biometric_id' => $request->biometric_id,
                'leave_quota' => $request->leave_quota,
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully.',
                    'employee' => $employee->load('user')
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating employee: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error creating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load('user.role');
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'employee' => $employee
            ]);
        }
        
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user.role');
        $roles = Role::all();
        
        return view('admin.employees.edit', compact('employee', 'roles'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username,' . $employee->user_id,
            'email' => 'nullable|email|max:150|unique:users,email,' . $employee->user_id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'biometric_id' => 'nullable|string|max:50|unique:employees,biometric_id,' . $employee->id,
            'leave_quota' => 'required|integer|min:0|max:365',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update user
            $updateUserData = [
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'theme' => $request->theme ?? $employee->user->theme,
            ];
            
            // Only update status if provided
            if ($request->has('status') && $request->status) {
                $updateUserData['status'] = $request->status;
            }

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

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee updated successfully.',
                    'employee' => $employee->load('user')
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee updated successfully.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating employee: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error updating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Check if employee has any related records
            if ($employee->assignedComplaints()->count() > 0 || 
                $employee->usedSpares()->count() > 0 || 
                $employee->requestedApprovals()->count() > 0) {
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete employee with existing records.'
                    ], 422);
                }
                
                return redirect()->back()
                    ->with('error', 'Cannot delete employee with existing records.');
            }

            // Delete user (which will cascade to employee due to foreign key)
            $employee->user->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee deleted successfully.'
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully.');

        } catch (Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting employee: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }

    /**
     * Get employee data for editing (AJAX)
     */
    public function getEditData(Employee $employee)
    {
        $employee->load('user.role');
        
        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    /**
     * Toggle employee status
     */
    public function toggleStatus(Employee $employee)
    {
        try {
            $newStatus = $employee->user->status === 'active' ? 'inactive' : 'active';
            $employee->user->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Employee status updated successfully.',
                'new_status' => $newStatus
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee leaves
     */
    public function getLeaves(Employee $employee)
    {
        $leaves = $employee->leaves()->latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'leaves' => $leaves
        ]);
    }

    /**
     * Create leave for employee
     */
    public function createLeave(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'leave_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $leave = $employee->leaves()->create([
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave request created successfully.',
                'leave' => $leave
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve leave
     */
    public function approveLeave(Employee $employee, $leaveId)
    {
        try {
            $leave = $employee->leaves()->findOrFail($leaveId);
            $leave->update(['status' => 'approved']);

            return response()->json([
                'success' => true,
                'message' => 'Leave approved successfully.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving leave: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject leave
     */
    public function rejectLeave(Employee $employee, $leaveId)
    {
        try {
            $leave = $employee->leaves()->findOrFail($leaveId);
            $leave->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Leave rejected successfully.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting leave: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee performance
     */
    public function getPerformance(Employee $employee)
    {
        $metrics = $employee->getPerformanceMetrics();
        
        return response()->json([
            'success' => true,
            'metrics' => $metrics
        ]);
    }

    /**
     * Bulk action for employees
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:delete,activate,deactivate',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employees = Employee::whereIn('id', $request->employee_ids)->with('user');
            
            switch ($request->action) {
                case 'delete':
                    // Delete users (which will cascade to employees due to foreign key)
                    foreach ($employees->get() as $employee) {
                        $employee->user->delete();
                    }
                    $message = 'Selected employees deleted successfully.';
                    break;
                case 'activate':
                    $employees->get()->each(function ($employee) {
                        $employee->user->update(['status' => 'active']);
                    });
                    $message = 'Selected employees activated successfully.';
                    break;
                case 'deactivate':
                    $employees->get()->each(function ($employee) {
                        $employee->user->update(['status' => 'inactive']);
                    });
                    $message = 'Selected employees deactivated successfully.';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export employees
     */
    public function export()
    {
        // Implementation for exporting employees
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented.'
        ]);
    }
}