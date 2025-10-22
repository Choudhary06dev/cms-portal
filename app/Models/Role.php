<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Get the users for the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the role permissions for the role.
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Check if role has permission for a module and action
     */
    public function hasPermission(string $permission): bool
    {
        // Admin role (role_id 1 or role_name 'admin') has access to everything
        if ($this->id === 1 || $this->role_name === 'admin') {
            return true;
        }

        // Parse permission string (e.g., "complaints.view", "users.edit")
        $parts = explode('.', $permission);
        if (count($parts) !== 2) {
            return false;
        }

        $module = $parts[0];
        $action = $parts[1];

        return $this->rolePermissions()
            ->where('module_name', $module)
            ->where("can_{$action}", true)
            ->exists();
    }

    /**
     * Get all permissions for this role
     */
    public function getPermissions(): array
    {
        return $this->rolePermissions()
            ->get()
            ->mapWithKeys(function ($permission) {
                return [
                    $permission->module_name => [
                        'view' => $permission->can_view,
                        'add' => $permission->can_add,
                        'edit' => $permission->can_edit,
                        'delete' => $permission->can_delete,
                    ]
                ];
            })
            ->toArray();
    }
}
