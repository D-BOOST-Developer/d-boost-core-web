<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWishlist extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser;

    protected $fillable = [
        'user_id',
        'customer_id',
        'product_id',
        'priority',
        'notes',
        'notify_on_sale',
        'notify_on_stock',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_on_sale' => 'boolean',
            'notify_on_stock' => 'boolean',
            'notified_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wishlist) {
            // Auto-set user_id from customer if not set
            if (empty($wishlist->user_id) && $wishlist->customer) {
                $wishlist->user_id = $wishlist->customer->user_id;
            }
        });
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for high priority items
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for items with sale notifications enabled
     */
    public function scopeNotifyOnSale($query)
    {
        return $query->where('notify_on_sale', true);
    }

    /**
     * Scope for items with stock notifications enabled
     */
    public function scopeNotifyOnStock($query)
    {
        return $query->where('notify_on_stock', true);
    }

    /**
     * Get creator user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get updater user
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => 'Tidak Ditentukan'
        };
    }

    /**
     * Check if product is in stock
     */
    public function isProductInStock(): bool
    {
        return $this->product?->stock_quantity > 0;
    }

    /**
     * Get product current price
     */
    public function getProductPriceAttribute()
    {
        return $this->product?->selling_price ?? 0;
    }
}
