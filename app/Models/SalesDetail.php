<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetail extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_per_item',
        'discount_percentage',
        'discount_amount',
        'line_total',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount_per_item' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($salesDetail) {
            // Auto-calculate line total securely handling nulls
            $unitPrice = (float)($salesDetail->unit_price ?? 0);
            $discount = (float)($salesDetail->discount_per_item ?? 0);
            $quantity = (float)($salesDetail->quantity ?? 0);
            
            $salesDetail->line_total = ($unitPrice - $discount) * $quantity;
            
            // Auto-set user_id from sale if not set
            if (empty($salesDetail->user_id) && $salesDetail->sale) {
                $salesDetail->user_id = $salesDetail->sale->user_id;
            }
        });
    }

    /**
     * Get the sale for this detail
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product for this detail
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the profit for this line item
     */
    public function getProfitAttribute()
    {
        // Handle null product (deleted products)
        if (!$this->product) {
            // If product is deleted, calculate profit based on unit_price only
            return $this->unit_price * $this->quantity;
        }
        return ($this->unit_price - ($this->product->cost ?? 0)) * $this->quantity;
    }

    /**
     * Get the profit margin for this line item
     */
    public function getProfitMarginAttribute()
    {
        if ($this->unit_price <= 0) return 0;
        
        // Handle null product (deleted products)
        if (!$this->product) {
            // If product is deleted, assume 100% profit margin (cost = 0)
            return 100;
        }
        
        $cost = $this->product->cost ?? 0;
        return (($this->unit_price - $cost) / $this->unit_price) * 100;
    }

    /**
     * Get the total discount for this line item
     */
    public function getTotalDiscountAttribute()
    {
        return $this->discount_per_item * $this->quantity;
    }
}
