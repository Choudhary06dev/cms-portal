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
     * Check if role has permission for a module
     */
    public function hasPermission(string $permission): bool
    {
        // Admin role (role_id 1 or role_name 'admin') has access to everything
        if ($this->id === 1 || $this->role_name === 'admin') {
            return true;
        }

        // Extract module name from "module.action" format (e.g., "users.view" -> "users")
        $module = explode('.', $permission)[0];

        // For module-only permissions
        return $this->rolePermissions()
            ->where('module_name', $module)
            ->exists();
    }

    /**
     * Get all permissions for this role
     */
    public function getPermissions(): array
    {
        return $this->rolePermissions()
            ->get()
            ->pluck('module_name')
            ->toArray();
    }
}
