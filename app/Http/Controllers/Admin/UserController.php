<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users',
            'email' => 'nullable|email|max:150|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['role.rolePermissions', 'employee', 'complaintLogs', 'slaRules']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:150|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Check if user has any related records
        if ($user->complaintLogs()->count() > 0 || $user->slaRules()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete user with existing records.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $user->status === 'active' ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()
            ->with('success', 'Password reset successfully.');
    }

    /**
     * Get user permissions
     */
    public function getPermissions(User $user)
    {
        $permissions = $user->role ? $user->role->getPermissions() : [];
        $availableModules = RolePermission::getAvailableModules();
        $availableActions = RolePermission::getAvailableActions();

        return view('admin.users.permissions', compact('user', 'permissions', 'availableModules', 'availableActions'));
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        if (!$user->role) {
            return redirect()->back()
                ->with('error', 'User does not have a role assigned.');
        }

        // Clear existing permissions
        $user->role->rolePermissions()->delete();

        // Add new permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $module => $actions) {
                $user->role->rolePermissions()->create([
                    'module_name' => $module,
                    'can_view' => in_array('view', $actions),
                    'can_add' => in_array('add', $actions),
                    'can_edit' => in_array('edit', $actions),
                    'can_delete' => in_array('delete', $actions),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Get user activity log
     */
    public function getActivityLog(User $user)
    {
        $activities = $user->complaintLogs()
            ->with('complaint')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.activity', compact('user', 'activities'));
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,change_role',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $userIds = $request->user_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['status' => 'active']);
                $message = 'Selected users activated successfully.';
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                $message = 'Selected users deactivated successfully.';
                break;

            case 'change_role':
                $validator = Validator::make($request->all(), [
                    'role_id' => 'required|exists:roles,id',
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                User::whereIn('id', $userIds)->update(['role_id' => $request->role_id]);
                $message = 'Selected users role changed successfully.';
                break;

            case 'delete':
                // Check for related records before deletion
                $usersWithRecords = User::whereIn('id', $userIds)
                    ->where(function($q) {
                        $q->whereHas('complaintLogs')
                          ->orWhereHas('slaRules');
                    })
                    ->count();

                if ($usersWithRecords > 0) {
                    return redirect()->back()
                        ->with('error', 'Some users cannot be deleted due to existing records.');
                }

                User::whereIn('id', $userIds)->delete();
                $message = 'Selected users deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $query = User::with('role');

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->get();

        // Implementation for export (CSV, Excel, etc.)
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}
