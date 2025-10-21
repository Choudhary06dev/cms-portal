<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing roles (they should already exist from migration)
        $adminRole = Role::where('role_name', 'admin')->first();
        $managerRole = Role::where('role_name', 'manager')->first();
        $employeeRole = Role::where('role_name', 'employee')->first();
        $clientRole = Role::where('role_name', 'client')->first();

        if (!$adminRole) {
            $adminRole = Role::create([
                'role_name' => 'admin',
                'description' => 'System Administrator with full access',
            ]);
        }

        if (!$managerRole) {
            $managerRole = Role::create([
                'role_name' => 'manager',
                'description' => 'Manager with limited administrative access',
            ]);
        }

        if (!$employeeRole) {
            $employeeRole = Role::create([
                'role_name' => 'employee',
                'description' => 'Employee with basic access',
            ]);
        }

        if (!$clientRole) {
            $clientRole = Role::create([
                'role_name' => 'client',
                'description' => 'Client with read-only access',
            ]);
        }

        // Create admin user
        $adminUser = User::create([
            'username' => 'admin',
            'email' => 'admin@company.com',
            'phone' => '+1234567890',
            'password_hash' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        // Create manager user
        $managerUser = User::create([
            'username' => 'manager',
            'email' => 'manager@company.com',
            'phone' => '+1234567891',
            'password_hash' => Hash::make('password'),
            'role_id' => $managerRole->id,
            'status' => 'active',
        ]);

        // Create employee user
        $employeeUser = User::create([
            'username' => 'employee',
            'email' => 'employee@company.com',
            'phone' => '+1234567892',
            'password_hash' => Hash::make('password'),
            'role_id' => $employeeRole->id,
            'status' => 'active',
        ]);

        // Create client user
        $clientUser = User::create([
            'username' => 'client',
            'email' => 'client@company.com',
            'phone' => '+1234567893',
            'password_hash' => Hash::make('password'),
            'role_id' => $clientRole->id,
            'status' => 'active',
        ]);

        // Set up admin permissions
        $this->setupAdminPermissions($adminRole);
        $this->setupManagerPermissions($managerRole);
        $this->setupEmployeePermissions($employeeRole);
        $this->setupClientPermissions($clientRole);

        $this->command->info('Roles and users created successfully!');
        $this->command->info('Admin login: admin / password');
        $this->command->info('Manager login: manager / password');
        $this->command->info('Employee login: employee / password');
        $this->command->info('Client login: client / password');
    }

    private function setupAdminPermissions(Role $role)
    {
        $modules = ['users', 'roles', 'employees', 'clients', 'complaints', 'spares', 'approvals', 'reports', 'sla'];
        $actions = ['view', 'add', 'edit', 'delete'];

        foreach ($modules as $module) {
            $role->rolePermissions()->create([
                'module_name' => $module,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }
    }

    private function setupManagerPermissions(Role $role)
    {
        $modules = ['employees', 'clients', 'complaints', 'spares', 'approvals', 'reports'];
        $actions = ['view', 'add', 'edit'];

        foreach ($modules as $module) {
            $role->rolePermissions()->create([
                'module_name' => $module,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => false,
            ]);
        }
    }

    private function setupEmployeePermissions(Role $role)
    {
        $modules = ['complaints', 'spares'];
        $actions = ['view', 'add', 'edit'];

        foreach ($modules as $module) {
            $role->rolePermissions()->create([
                'module_name' => $module,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => false,
            ]);
        }
    }

    private function setupClientPermissions(Role $role)
    {
        $modules = ['complaints'];
        $actions = ['view', 'add'];

        foreach ($modules as $module) {
            $role->rolePermissions()->create([
                'module_name' => $module,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => false,
                'can_delete' => false,
            ]);
        }
    }
}
