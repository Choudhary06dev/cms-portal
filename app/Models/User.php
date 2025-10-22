<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password_hash',
        'email',
        'phone',
        'role_id',
        'status',
        'theme',
    ];

    protected $hidden = [
        'password_hash',
        'password',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the employee record for the user.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get the complaints logged by this user.
     */
    public function loggedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'logged_by');
    }

    /**
     * Get the SLA rules where this user is notified.
     */
    public function slaRules(): HasMany
    {
        return $this->hasMany(SlaRule::class, 'notify_to');
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    /**
     * Check if user has permission for a module and action
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Get user's display name (username)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->username;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    /**
     * Check if user has any admin permissions
     */
    public function hasAnyAdminPermission(): bool
    {
        if (!$this->role) {
            return false;
        }

        // Check if user has any permissions in any module
        return $this->role->rolePermissions()->exists();
    }
}