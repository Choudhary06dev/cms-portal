<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department',
        'designation',
        'biometric_id',
        'leave_quota',
    ];

    protected $casts = [
        'leave_quota' => 'integer',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full name of the employee
     */
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->username : 'Unknown Employee';
    }

    /**
     * Get the leaves for the employee.
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    /**
     * Get the complaints assigned to this employee.
     */
    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_employee_id');
    }

    /**
     * Get the spare parts used by this employee.
     */
    public function usedSpares(): HasMany
    {
        return $this->hasMany(ComplaintSpare::class, 'used_by');
    }

    /**
     * Get the spare approval performas requested by this employee.
     */
    public function requestedApprovals(): HasMany
    {
        return $this->hasMany(SpareApprovalPerforma::class, 'requested_by');
    }

    /**
     * Get the spare approval performas approved by this employee.
     */
    public function approvedApprovals(): HasMany
    {
        return $this->hasMany(SpareApprovalPerforma::class, 'approved_by');
    }

    /**
     * Get employee's username
     */
    public function getUsernameAttribute(): string
    {
        return $this->user ? $this->user->username : 'Unknown';
    }

    /**
     * Get employee's email
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get employee's phone
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->user ? $this->user->phone : null;
    }

    /**
     * Get employee's status
     */
    public function getStatusAttribute(): string
    {
        return $this->user ? $this->user->status : 'inactive';
    }

    /**
     * Check if employee is active
     */
    public function isActive(): bool
    {
        return $this->user && $this->user->isActive();
    }

    /**
     * Get total leaves taken this year
     */
    public function getTotalLeavesTaken(): int
    {
        return $this->leaves()
            ->where('status', 'approved')
            ->whereYear('created_at', now()->year)
            ->sum('leave_days');
    }

    /**
     * Get remaining leave quota
     */
    public function getRemainingLeaves(): int
    {
        return $this->leave_quota - $this->getTotalLeavesTaken();
    }

    /**
     * Get pending leave requests
     */
    public function getPendingLeaves(): int
    {
        return $this->leaves()
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get employee performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        $totalComplaints = $this->assignedComplaints()->count();
        $resolvedComplaints = $this->assignedComplaints()
            ->where('status', 'resolved')
            ->count();
        $closedComplaints = $this->assignedComplaints()
            ->where('status', 'closed')
            ->count();

        return [
            'total_complaints' => $totalComplaints,
            'resolved_complaints' => $resolvedComplaints,
            'closed_complaints' => $closedComplaints,
            'resolution_rate' => $totalComplaints > 0 ? round(($resolvedComplaints + $closedComplaints) / $totalComplaints * 100, 2) : 0,
        ];
    }

    /**
     * Get available departments
     */
    public static function getAvailableDepartments(): array
    {
        return [
            'electric' => 'Electrical Department',
            'sanitary' => 'Sanitary Department',
            'kitchen' => 'Kitchen Appliances',
            'general' => 'General Maintenance',
            'admin' => 'Administration',
        ];
    }

    /**
     * Get available designations
     */
    public static function getAvailableDesignations(): array
    {
        return [
            'technician' => 'Technician',
            'senior_technician' => 'Senior Technician',
            'supervisor' => 'Supervisor',
            'manager' => 'Manager',
            'admin' => 'Administrator',
        ];
    }
}