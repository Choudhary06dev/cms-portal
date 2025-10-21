<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpareApprovalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'performa_id',
        'spare_id',
        'quantity_requested',
        'reason',
    ];

    /**
     * Get the performa that owns the item.
     */
    public function performa(): BelongsTo
    {
        return $this->belongsTo(SpareApprovalPerforma::class, 'performa_id');
    }

    /**
     * Get the spare that is requested.
     */
    public function spare(): BelongsTo
    {
        return $this->belongsTo(Spare::class);
    }

    /**
     * Get spare name
     */
    public function getSpareNameAttribute(): string
    {
        return $this->spare ? $this->spare->item_name : 'Unknown Spare';
    }

    /**
     * Get spare category
     */
    public function getSpareCategoryAttribute(): string
    {
        return $this->spare ? $this->spare->getCategoryDisplayAttribute() : 'Unknown';
    }

    /**
     * Get spare unit
     */
    public function getSpareUnitAttribute(): string
    {
        return $this->spare ? $this->spare->getUnitDisplayAttribute() : 'Unknown';
    }

    /**
     * Get spare unit price
     */
    public function getSpareUnitPriceAttribute(): float
    {
        return $this->spare ? $this->spare->unit_price : 0;
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format($this->getSpareUnitPriceAttribute(), 2);
    }

    /**
     * Get total estimated cost for this item
     */
    public function getTotalEstimatedCostAttribute(): float
    {
        return $this->quantity_requested * $this->getSpareUnitPriceAttribute();
    }

    /**
     * Get formatted total estimated cost
     */
    public function getFormattedTotalEstimatedCostAttribute(): string
    {
        return '₹' . number_format($this->getTotalEstimatedCostAttribute(), 2);
    }

    /**
     * Get spare stock status
     */
    public function getSpareStockStatusAttribute(): string
    {
        if (!$this->spare) {
            return 'unknown';
        }

        return $this->spare->getStockStatusAttribute();
    }

    /**
     * Get spare stock status display
     */
    public function getSpareStockStatusDisplayAttribute(): string
    {
        if (!$this->spare) {
            return 'Unknown';
        }

        return $this->spare->getStockStatusDisplayAttribute();
    }

    /**
     * Get spare stock status color
     */
    public function getSpareStockStatusColorAttribute(): string
    {
        if (!$this->spare) {
            return 'muted';
        }

        return $this->spare->getStockStatusColorAttribute();
    }

    /**
     * Check if spare is available in sufficient quantity
     */
    public function isSpareAvailable(): bool
    {
        if (!$this->spare) {
            return false;
        }

        return $this->spare->isStockSufficient($this->quantity_requested);
    }

    /**
     * Get availability status
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->spare) {
            return 'unavailable';
        }

        if ($this->isSpareAvailable()) {
            return 'available';
        } elseif ($this->spare->isOutOfStock()) {
            return 'out_of_stock';
        } else {
            return 'insufficient_stock';
        }
    }

    /**
     * Get availability status display
     */
    public function getAvailabilityStatusDisplayAttribute(): string
    {
        $statuses = [
            'available' => 'Available',
            'insufficient_stock' => 'Insufficient Stock',
            'out_of_stock' => 'Out of Stock',
            'unavailable' => 'Unavailable',
        ];

        return $statuses[$this->getAvailabilityStatusAttribute()] ?? 'Unknown';
    }

    /**
     * Get availability status color
     */
    public function getAvailabilityStatusColorAttribute(): string
    {
        $colors = [
            'available' => 'success',
            'insufficient_stock' => 'warning',
            'out_of_stock' => 'danger',
            'unavailable' => 'muted',
        ];

        return $colors[$this->getAvailabilityStatusAttribute()] ?? 'muted';
    }

    /**
     * Get formatted quantity with unit
     */
    public function getFormattedQuantityAttribute(): string
    {
        return $this->quantity_requested . ' ' . $this->getSpareUnitAttribute();
    }

    /**
     * Get reason or default message
     */
    public function getReasonDisplayAttribute(): string
    {
        return $this->reason ?: 'No reason provided';
    }

    /**
     * Scope for specific performa
     */
    public function scopeForPerforma($query, $performaId)
    {
        return $query->where('performa_id', $performaId);
    }

    /**
     * Scope for specific spare
     */
    public function scopeForSpare($query, $spareId)
    {
        return $query->where('spare_id', $spareId);
    }

    /**
     * Scope for available items
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('spare', function ($q) {
            $q->whereRaw('stock_quantity > 0');
        });
    }

    /**
     * Scope for unavailable items
     */
    public function scopeUnavailable($query)
    {
        return $query->whereHas('spare', function ($q) {
            $q->where('stock_quantity', '<=', 0);
        });
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('spare', function ($q) {
            $q->whereRaw('stock_quantity <= threshold_level');
        });
    }
}
