<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create roles
        $adminRole = Role::firstOrCreate(
            ['role_name' => 'admin'],
            ['description' => 'System Administrator with full access']
        );

        $managerRole = Role::firstOrCreate(
            ['role_name' => 'manager'],
            ['description' => 'Manager with most permissions except user management']
        );

        $employeeRole = Role::firstOrCreate(
            ['role_name' => 'employee'],
            ['description' => 'Regular employee with limited permissions']
        );

        $clientRole = Role::firstOrCreate(
            ['role_name' => 'client'],
            ['description' => 'Client with very limited access']
        );

        // Clear existing permissions for these roles
        RolePermission::whereIn('role_id', [$adminRole->id, $managerRole->id, $employeeRole->id, $clientRole->id])->delete();

        // Define modules and actions
        $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
        $actions = ['view', 'add', 'edit', 'delete'];

        // Admin gets all permissions
        foreach ($modules as $module) {
            RolePermission::create([
                'role_id' => $adminRole->id,
                'module_name' => $module,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }

        // Manager permissions (no user/role management)
        $managerModules = ['employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
        foreach ($managerModules as $module) {
            RolePermission::create([
                'role_id' => $managerRole->id,
                'module_name' => $module,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }

        // Employee permissions (limited)
        $employeeModules = ['complaints', 'spares', 'reports'];
        foreach ($employeeModules as $module) {
            RolePermission::create([
                'role_id' => $employeeRole->id,
                'module_name' => $module,
                'can_view' => true,
                'can_add' => $module === 'complaints', // Can only add complaints
                'can_edit' => $module === 'complaints', // Can only edit complaints
                'can_delete' => false,
            ]);
        }

        // Client permissions (very limited - only view their own data)
        $clientModules = ['complaints', 'reports'];
        foreach ($clientModules as $module) {
            RolePermission::create([
                'role_id' => $clientRole->id,
                'module_name' => $module,
                'can_view' => true,
                'can_add' => $module === 'complaints', // Can only add complaints
                'can_edit' => false,
                'can_delete' => false,
            ]);
        }
    }
}
