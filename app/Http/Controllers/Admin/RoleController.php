<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with('rolePermissions');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('role_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $roles = $query->orderBy('role_name')->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $availableModules = RolePermission::getAvailableModules();
        $availableActions = RolePermission::getAvailableActions();
        
        return view('admin.roles.create', compact('availableModules', 'availableActions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:100|unique:roles',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // Add permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $module => $actions) {
                $role->rolePermissions()->create([
                    'module_name' => $module,
                    'can_view' => in_array('view', $actions),
                    'can_add' => in_array('add', $actions),
                    'can_edit' => in_array('edit', $actions),
                    'can_delete' => in_array('delete', $actions),
                ]);
            }
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['rolePermissions', 'users']);
        $availableModules = RolePermission::getAvailableModules();
        $availableActions = RolePermission::getAvailableActions();

        return view('admin.roles.show', compact('role', 'availableModules', 'availableActions'));
    }

    /**
     * Show the form for editing the role
     */
    public function edit(Role $role)
    {
        $role->load('rolePermissions');
        $availableModules = RolePermission::getAvailableModules();
        $availableActions = RolePermission::getAvailableActions();

        return view('admin.roles.edit', compact('role', 'availableModules', 'availableActions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:100|unique:roles,role_name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // Update permissions
        $role->rolePermissions()->delete();
        
        if ($request->has('permissions')) {
            foreach ($request->permissions as $module => $actions) {
                $role->rolePermissions()->create([
                    'module_name' => $module,
                    'can_view' => in_array('view', $actions),
                    'can_add' => in_array('add', $actions),
                    'can_edit' => in_array('edit', $actions),
                    'can_delete' => in_array('delete', $actions),
                ]);
            }
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role status
     */
    public function toggleStatus(Role $role)
    {
        $role->update([
            'status' => $role->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $role->status === 'active' ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Role {$status} successfully.");
    }

    /**
     * Get role permissions
     */
    public function getPermissions(Role $role)
    {
        $permissions = $role->getPermissions();
        $availableModules = RolePermission::getAvailableModules();
        $availableActions = RolePermission::getAvailableActions();

        return response()->json([
            'permissions' => $permissions,
            'availableModules' => $availableModules,
            'availableActions' => $availableActions,
        ]);
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        // Clear existing permissions
        $role->rolePermissions()->delete();

        // Add new permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $module => $actions) {
                $role->rolePermissions()->create([
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
     * Get role statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => Role::count(),
            'active' => Role::where('status', 'active')->count(),
            'inactive' => Role::where('status', 'inactive')->count(),
            'with_users' => Role::whereHas('users')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get role usage statistics
     */
    public function getUsageStatistics()
    {
        $usage = Role::withCount('users')
            ->orderBy('users_count', 'desc')
            ->get();

        return response()->json($usage);
    }

    /**
     * Bulk actions on roles
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $roleIds = $request->role_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Role::whereIn('id', $roleIds)->update(['status' => 'active']);
                $message = 'Selected roles activated successfully.';
                break;

            case 'deactivate':
                Role::whereIn('id', $roleIds)->update(['status' => 'inactive']);
                $message = 'Selected roles deactivated successfully.';
                break;

            case 'delete':
                // Check for users
                $rolesWithUsers = Role::whereIn('id', $roleIds)
                    ->whereHas('users')
                    ->count();

                if ($rolesWithUsers > 0) {
                    return redirect()->back()
                        ->with('error', 'Some roles cannot be deleted due to assigned users.');
                }

                Role::whereIn('id', $roleIds)->delete();
                $message = 'Selected roles deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export roles data
     */
    public function export(Request $request)
    {
        $query = Role::with('rolePermissions');

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('role_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $roles = $query->get();

        // Implementation for export
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}
