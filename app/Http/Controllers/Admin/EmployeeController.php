<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
// Removed User and Role dependencies as employees no longer link to users
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
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
        $employees = Employee::orderBy('id', 'desc')->paginate(10);
        
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        // Clear any old input data to ensure clean form
        request()->session()->forget('_old_input');
        
        $response = response()->view('admin.employees.create');
        
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
        // Log incoming request data for debugging
        Log::info('Employee create request:', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:employees,email',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'biometric_id' => 'nullable|string|max:50|unique:employees',
            'date_of_hire' => 'nullable|date',
            'leave_quota' => 'nullable|integer|min:0|max:365',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            
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
            Log::info('Starting employee creation transaction');

            // Create employee record (no user creation)
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'department' => $request->department,
                'designation' => $request->designation,
                'phone' => $request->phone,
                'biometric_id' => $request->biometric_id,
                'date_of_hire' => $request->date_of_hire,
                'leave_quota' => $request->leave_quota ?? 30,
                'address' => $request->address,
                'status' => $request->status ?? 'active',
            ]);
            Log::info('Employee created successfully with ID: ' . $employee->id);

            DB::commit();
            Log::info('Transaction committed successfully');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully.',
                    'employee' => $employee
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating employee: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'biometric_id' => 'nullable|string|max:50|unique:employees,biometric_id,' . $employee->id,
            'date_of_hire' => 'nullable|date',
            'leave_quota' => 'required|integer|min:0|max:365',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
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

            // Update employee
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'department' => $request->department,
                'designation' => $request->designation,
                'phone' => $request->phone,
                'biometric_id' => $request->biometric_id,
                'date_of_hire' => $request->date_of_hire,
                'leave_quota' => $request->leave_quota,
                'address' => $request->address,
                'status' => $request->status,
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee updated successfully.',
                    'employee' => $employee
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
    public function destroy($id)
    {
        try {
            // Find employee by ID instead of using route model binding
            $employee = Employee::findOrFail($id);
            
            Log::info('Attempting to soft delete employee ID: ' . $employee->id);
            
            // Soft delete - no need to check for related records as soft delete preserves them
            $employee->delete(); // This will now soft delete due to SoftDeletes trait
            Log::info('Employee soft deleted successfully for ID: ' . $employee->id);

            // Check if request expects JSON response
            if (request()->ajax() || request()->wantsJson() || request()->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee deleted successfully.'
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully.');

        } catch (Exception $e) {
            Log::error('Error deleting employee ID ' . ($employee->id ?? $id) . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (request()->ajax() || request()->wantsJson() || request()->header('Accept') === 'application/json') {
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
            $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
            $employee->update(['status' => $newStatus]);

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
            $employees = Employee::whereIn('id', $request->employee_ids);
            
            switch ($request->action) {
                case 'delete':
                    $employees->delete();
                    $message = 'Selected employees deleted successfully.';
                    break;
                case 'activate':
                    $employees->update(['status' => 'active']);
                    $message = 'Selected employees activated successfully.';
                    break;
                case 'deactivate':
                    $employees->update(['status' => 'inactive']);
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