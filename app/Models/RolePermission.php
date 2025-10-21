<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'module_name',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_add' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
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
     * Get all available actions
     */
    public static function getAvailableActions(): array
    {
        return [
            'view' => 'View',
            'add' => 'Add',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ];
    }

    /**
     * Check if permission allows specific action
     */
    public function allowsAction(string $action): bool
    {
        $actionField = "can_{$action}";
        return isset($this->$actionField) ? $this->$actionField : false;
    }

    /**
     * Get permission summary
     */
    public function getPermissionSummary(): array
    {
        return [
            'module' => $this->module_name,
            'permissions' => [
                'view' => $this->can_view,
                'add' => $this->can_add,
                'edit' => $this->can_edit,
                'delete' => $this->can_delete,
            ]
        ];
    }
}
