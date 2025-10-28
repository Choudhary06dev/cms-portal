<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'module_name',
    ];

    /**
     * Get the role that owns the permission.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all available modules
     */
    public static function getAvailableModules(): array
    {
        return [
            'users' => 'User Management',
            'employees' => 'Employee Management',
            'clients' => 'Client Management',
            'complaints' => 'Complaint Management',
            'spares' => 'Spare Parts Management',
            'reports' => 'Reports & Analytics',
            'settings' => 'System Settings',
        ];
    }

    /**
     * Get permission summary
     */
    public function getPermissionSummary(): array
    {
        return [
            'module' => $this->module_name,
        ];
    }
}
