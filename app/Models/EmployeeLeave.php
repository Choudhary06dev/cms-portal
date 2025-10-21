<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the employee that owns the leave.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user through employee relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id')
            ->through('employee');
    }

    /**
     * Get available leave types
     */
    public static function getLeaveTypes(): array
    {
        return [
            'sick' => 'Sick Leave',
            'annual' => 'Annual Leave',
            'casual' => 'Casual Leave',
        ];
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    /**
     * Calculate leave days
     */
    public function getLeaveDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Check if leave is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if leave is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if leave is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get leave type display name
     */
    public function getLeaveTypeDisplayAttribute(): string
    {
        return self::getLeaveTypes()[$this->leave_type] ?? $this->leave_type;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if leave is currently active
     */
    public function isActive(): bool
    {
        if (!$this->isApproved()) {
            return false;
        }

        $today = now()->toDateString();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Check if leave is in the past
     */
    public function isPast(): bool
    {
        return $this->end_date < now()->toDateString();
    }

    /**
     * Check if leave is in the future
     */
    public function isFuture(): bool
    {
        return $this->start_date > now()->toDateString();
    }

    /**
     * Get leave duration in a readable format
     */
    public function getDurationAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return 'N/A';
        }

        $days = $this->getLeaveDaysAttribute();
        return $days . ' day' . ($days > 1 ? 's' : '');
    }

    /**
     * Scope for pending leaves
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved leaves
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected leaves
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for active leaves (currently on leave)
     */
    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }
}
