<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'client_id',
        'category',
        'priority',
        'description',
        'assigned_employee_id',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /**
     * Get the client that owns the complaint.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    /**
     * Get the employee assigned to the complaint.
     */
    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id')->withTrashed();
    }

    /**
     * Get the attachments for the complaint.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    /**
     * Get the logs for the complaint.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ComplaintLog::class);
    }

    /**
     * Get the spare parts used for the complaint.
     */
    public function spareParts(): HasMany
    {
        return $this->hasMany(ComplaintSpare::class);
    }

    /**
     * Get the spare approval performas for the complaint.
     */
    public function spareApprovals(): HasMany
    {
        return $this->hasMany(SpareApprovalPerforma::class);
    }

    /**
     * Get available complaint categories
     */
    public static function getCategories(): array
    {
        return [
            'technical' => 'Technical',
            'service' => 'Service',
            'billing' => 'Billing',
            'sanitary' => 'Sanitary',
            'electric' => 'Electric',
            'kitchen' => 'Kitchen',
            'plumbing' => 'Plumbing',
            'other' => 'Other',
        ];
    }

    /**
     * Get available complaint types (legacy method for SLA rules)
     */
    public static function getComplaintTypes(): array
    {
        return [
            'electric' => 'Electrical Issues',
            'sanitary' => 'Sanitary Issues',
            'kitchen' => 'Kitchen Appliances',
            'general' => 'General Maintenance',
        ];
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            'new' => 'New',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }

    /**
     * Get available priorities
     */
    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }

    /**
     * Get complaint category display name
     */
    public function getCategoryDisplayAttribute(): string
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get priority display name
     */
    public function getPriorityDisplayAttribute(): string
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }

    /**
     * Check if complaint is new
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Check if complaint is assigned
     */
    public function isAssigned(): bool
    {
        return $this->status === 'assigned';
    }

    /**
     * Check if complaint is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if complaint is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if complaint is closed
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if complaint is completed (resolved or closed)
     */
    public function isCompleted(): bool
    {
        return $this->status === 'resolved' || $this->status === 'closed';
    }

    /**
     * Get ticket number for the complaint
     */
    public function getTicketNumberAttribute(): string
    {
        $year = $this->created_at->format('Y');
        $month = $this->created_at->format('m');
        $id = str_pad($this->id, 5, '0', STR_PAD_LEFT);
        
        return "CMP-{$year}{$month}-{$id}";
    }

    /**
     * Get hours elapsed since creation
     */
    public function getHoursElapsedAttribute(): int
    {
        return $this->created_at->diffInHours(now());
    }

    /**
     * Check if complaint is overdue
     */
    public function isOverdue(int $days = 7): bool
    {
        return $this->created_at->addDays($days)->isPast() && !$this->isCompleted();
    }

    /**
     * Check if SLA response time is breached
     */
    public function isResponseTimeBreached(SlaRule $slaRule): bool
    {
        return $this->getHoursElapsedAttribute() > $slaRule->max_response_time;
    }

    /**
     * Check if SLA resolution time is breached
     */
    public function isResolutionTimeBreached(SlaRule $slaRule): bool
    {
        return $this->getHoursElapsedAttribute() > $slaRule->max_resolution_time;
    }

    /**
     * Check if SLA is breached
     */
    public function isSlaBreached(): bool
    {
        $slaRule = SlaRule::where('complaint_type', $this->category)
            ->where('status', 'active')
            ->first();

        if (!$slaRule) {
            return false;
        }

        return $this->getHoursElapsedAttribute() > $slaRule->max_resolution_time;
    }

    /**
     * Get hours overdue
     */
    public function getHoursOverdue(): int
    {
        $slaRule = SlaRule::where('complaint_type', $this->category)
            ->where('status', 'active')
            ->first();

        if (!$slaRule) {
            return 0;
        }

        $hoursElapsed = $this->getHoursElapsedAttribute();
        return max(0, $hoursElapsed - $slaRule->max_resolution_time);
    }

    /**
     * Get SLA rule for this complaint
     */
    public function slaRule()
    {
        return $this->belongsTo(SlaRule::class, 'category', 'complaint_type');
    }

    /**
     * Boot method to generate ticket number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {
            // Auto-generate ticket number will be handled by accessor
        });
    }

    /**
     * Check if complaint is pending (not completed)
     */
    public function isPending(): bool
    {
        return !$this->isCompleted();
    }

    /**
     * Get complaint age in days
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get resolution time in days
     */
    public function getResolutionTimeAttribute(): ?int
    {
        if (!$this->isCompleted()) {
            return null;
        }

        return $this->created_at->diffInDays($this->closed_at ?? now());
    }

    /**
     * Get total spare parts cost
     */
    public function getTotalSpareCostAttribute(): float
    {
        return $this->spareParts()
            ->join('spares', 'complaint_spares.spare_id', '=', 'spares.id')
            ->selectRaw('SUM(complaint_spares.quantity * spares.unit_price) as total')
            ->value('total') ?? 0;
    }


    /**
     * Get client name
     */
    public function getClientNameAttribute(): string
    {
        return $this->client ? $this->client->getDisplayNameAttribute() : 'Unknown Client';
    }

    /**
     * Get assigned employee name
     */
    public function getAssignedEmployeeNameAttribute(): string
    {
        return $this->assignedEmployee ? $this->assignedEmployee->getFullNameAttribute() : 'Unassigned';
    }

    /**
     * Scope for new complaints
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope for assigned complaints
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope for in progress complaints
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for resolved complaints
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope for closed complaints
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope for pending complaints
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['new', 'assigned', 'in_progress']);
    }

    /**
     * Scope for completed complaints
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for complaints by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for complaints by type (legacy method)
     */
    public function scopeByType($query, $type)
    {
        return $query->where('category', $type);
    }

    /**
     * Scope for complaints by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for complaints by assigned employee
     */
    public function scopeByAssignedEmployee($query, $employeeId)
    {
        return $query->where('assigned_employee_id', $employeeId);
    }

    /**
     * Scope for complaints by client
     */
    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope for overdue complaints
     */
    public function scopeOverdue($query, $days = 7)
    {
        return $query->where('created_at', '<', now()->subDays($days))
            ->whereIn('status', ['new', 'assigned', 'in_progress']);
    }
}