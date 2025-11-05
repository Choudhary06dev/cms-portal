<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockApprovalData extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'spare_id',
        'complaint_id',
        'approval_id',
        'issue_date',
        'category',
        'product_name',
        'available_stock',
        'requested_stock',
        'approval_stock',
        'issued_quantity',
        'status',
        'remarks',
        'issued_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'available_stock' => 'integer',
        'requested_stock' => 'integer',
        'approval_stock' => 'integer',
        'issued_quantity' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the spare that owns the stock approval data.
     */
    public function spare(): BelongsTo
    {
        return $this->belongsTo(Spare::class);
    }

    /**
     * Get the complaint related to this stock approval.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get the approval related to this stock approval.
     */
    public function approval(): BelongsTo
    {
        return $this->belongsTo(SpareApprovalPerforma::class, 'approval_id');
    }

    /**
     * Get the employee who issued the stock.
     */
    public function issuedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'issued_by');
    }

    /**
     * Get the employee who approved the stock.
     */
    public function approvedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}

