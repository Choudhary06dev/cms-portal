<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spare extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_code',
        'brand_name',
        'product_nature',
        'item_name',
        'category',
        'unit',
        'unit_price',
        'total_received_quantity',
        'issued_quantity',
        'stock_quantity',
        'threshold_level',
        'supplier',
        'description',
        'last_stock_in_at',
        'last_updated',
    ];
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_received_quantity' => 'integer',
        'issued_quantity' => 'integer',
        'stock_quantity' => 'integer',
        'threshold_level' => 'integer',
        'last_stock_in_at' => 'datetime',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the stock logs for the spare.
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(SpareStockLog::class);
    }

    /**
     * Get the complaint spares for the spare.
     */
    public function complaintSpares(): HasMany
    {
        return $this->hasMany(ComplaintSpare::class);
    }

    /**
     * Get the spare approval items for the spare.
     */
    public function approvalItems(): HasMany
    {
        return $this->hasMany(SpareApprovalItem::class);
    }

    /**
     * Get available categories
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
     * Canonical DB categories (enum values in `spares` table)
     */
    public static function getCanonicalCategories(): array
    {
        return ['electrical', 'plumbing', 'kitchen', 'general', 'tools', 'consumables'];
    }

    /**
     * Normalize any UI category key to canonical DB value
     */
    public static function normalizeCategory(string $category): string
    {
        $map = [
            'electric' => 'electrical',
            'sanitary' => 'plumbing',
            'technical' => 'general',
            'service' => 'consumables',
            'billing' => 'consumables',
            'other' => 'general',
            // direct passthroughs
            'kitchen' => 'kitchen',
            'plumbing' => 'plumbing',
            'electrical' => 'electrical',
            'general' => 'general',
            'tools' => 'tools',
            'consumables' => 'consumables',
        ];
        return $map[$category] ?? $category;
    }

    /**
     * Get available units
     */
    public static function getUnits(): array
    {
        return [
            'pcs' => 'Pieces',
            'kg' => 'Kilograms',
            'm' => 'Meters',
            'ft' => 'Feet',
            'box' => 'Box',
            'roll' => 'Roll',
            'set' => 'Set',
        ];
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute(): string
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get unit display name
     */
    public function getUnitDisplayAttribute(): string
    {
        return self::getUnits()[$this->unit] ?? $this->unit;
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->threshold_level;
    }

    /**
     * Check if stock is out
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Check if stock is sufficient
     */
    public function isStockSufficient(int $requiredQuantity): bool
    {
        return $this->stock_quantity >= $requiredQuantity;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get stock status display
     */
    public function getStockStatusDisplayAttribute(): string
    {
        $statuses = [
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
        ];

        return $statuses[$this->getStockStatusAttribute()] ?? 'Unknown';
    }

    /**
     * Get stock status color
     */
    public function getStockStatusColorAttribute(): string
    {
        $colors = [
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'in_stock' => 'success',
        ];

        return $colors[$this->getStockStatusAttribute()] ?? 'muted';
    }

    /**
     * Get total value of current stock
     */
    public function getTotalValueAttribute(): float
    {
        return $this->stock_quantity * $this->unit_price;
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted total value
     */
    public function getFormattedTotalValueAttribute(): string
    {
        return '₹' . number_format($this->getTotalValueAttribute(), 2);
    }

    /**
     * Percentage of utilized stock based on cumulative issued vs received
     */
    public function getUtilizationPercentAttribute(): float
    {
        $totalReceived = (int)($this->total_received_quantity ?? 0);
        if ($totalReceived <= 0) {
            return 0.0;
        }
        $issued = (int)($this->issued_quantity ?? 0);
        $percent = ($issued / $totalReceived) * 100.0;
        // Clamp between 0 and 100 for display sanity
        if ($percent < 0) {
            return 0.0;
        }
        if ($percent > 100) {
            return 100.0;
        }
        return round($percent, 2);
    }

    /**
     * Add stock
     */
    public function addStock(int $quantity, string $remarks = null, int $referenceId = null): void
    {
        $this->stock_quantity += $quantity;
        // Track cumulative received
        $this->total_received_quantity = (int)($this->total_received_quantity ?? 0) + $quantity;
        $this->last_stock_in_at = now();
        $this->last_updated = now();
        $this->save();

        // Log the stock change
        $this->stockLogs()->create([
            'change_type' => 'in',
            'quantity' => $quantity,
            'reference_id' => $referenceId,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Return stock back to inventory (undo issued) without affecting total received
     */
    public function returnStock(int $quantity, string $remarks = null, int $referenceId = null): void
    {
        if ($quantity <= 0) {
            return;
        }
        $this->stock_quantity += $quantity;
        // Decrease issued to reflect return, never below zero
        $this->issued_quantity = max(0, (int)($this->issued_quantity ?? 0) - $quantity);
        $this->last_updated = now();
        $this->save();

        // Log the stock change
        $this->stockLogs()->create([
            'change_type' => 'in',
            'quantity' => $quantity,
            'reference_id' => $referenceId,
            'remarks' => $remarks ?? 'Returned to stock',
        ]);
    }

    /**
     * Remove stock
     */
    public function removeStock(int $quantity, string $remarks = null, int $referenceId = null): bool
    {
        if (!$this->isStockSufficient($quantity)) {
            return false;
        }

        $this->stock_quantity -= $quantity;
        // Track cumulative issued
        $this->issued_quantity = max(0, (int)($this->issued_quantity ?? 0) + $quantity);
        $this->last_updated = now();
        $saved = $this->save();

        if (!$saved) {
            \Log::error('Failed to save stock reduction', [
                'spare_id' => $this->id,
                'quantity' => $quantity,
                'new_stock' => $this->stock_quantity
            ]);
            return false;
        }

        // Log the stock change
        try {
            $this->stockLogs()->create([
                'change_type' => 'out',
                'quantity' => $quantity,
                'reference_id' => $referenceId,
                'remarks' => $remarks,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create stock log', [
                'spare_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }

        return true;
    }

    /**
     * Get stock movement summary
     */
    public function getStockMovementSummary(int $days = 30): array
    {
        $logs = $this->stockLogs()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $inStock = $logs->where('change_type', 'in')->sum('quantity');
        $outStock = $logs->where('change_type', 'out')->sum('quantity');

        return [
            'in_stock' => $inStock,
            'out_stock' => $outStock,
            'net_movement' => $inStock - $outStock,
            'movement_count' => $logs->count(),
        ];
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= threshold_level');
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Scope for in stock items
     */
    public function scopeInStock($query)
    {
        return $query->whereRaw('stock_quantity > threshold_level');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for items with stock above threshold
     */
    public function scopeAboveThreshold($query)
    {
        return $query->whereRaw('stock_quantity > threshold_level');
    }

    /**
     * Scope for recently updated items
     */
    public function scopeRecentlyUpdated($query, $days = 7)
    {
        return $query->where('last_updated', '>=', now()->subDays($days));
    }
}
