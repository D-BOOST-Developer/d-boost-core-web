<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, HasUuidAndAudit, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'sku',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_level',
        'max_stock_level',
        'category',
        'unit',
        'image_path',
        'is_active',
        'is_published_to_storefront',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_level' => 'integer',
            'max_stock_level' => 'integer',
            'is_active' => 'boolean',
            'is_published_to_storefront' => 'boolean',
        ];
    }

    /**
     * Global scope for multi-tenancy
     */
    protected static function booted()
    {
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('products.user_id', auth()->id());
            }
        });

        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }

    /**
     * Get inventory transactions for this product
     */
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Get user who owns this product
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get sales details for this product
     */
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    /**
     * Get total quantity sold
     */
    public function getTotalSoldAttribute()
    {
        return $this->salesDetails()->sum('quantity');
    }

    /**
     * Get total revenue from this product
     */
    public function getTotalRevenueAttribute()
    {
        return $this->salesDetails()->sum('line_total');
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Check if product is overstock
     */
    public function isOverstock(): bool
    {
        return $this->max_stock_level && $this->stock_quantity >= $this->max_stock_level;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } elseif ($this->isOverstock()) {
            return 'overstock';
        }
        return 'normal';
    }

    /**
     * Get stock status badge HTML
     */
    public function getStockStatusBadgeAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Out of Stock</span>',
            'low_stock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>',
            'overstock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Overstock</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Normal</span>',
        };
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if ($this->price <= 0) return 0;
        return (($this->price - $this->cost) / $this->price) * 100;
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for products published to the public storefront.
     * Requires the publish flag, an active product, and stock on hand.
     */
    public function scopePublishedToStorefront($query)
    {
        return $query->where('is_published_to_storefront', true)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope for low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock_level');
    }

    /**
     * Scope for products by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Safely reduce stock quantity (prevents negative stock)
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false; // Insufficient stock
        }

        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    /**
     * Safely increase stock quantity
     */
    public function increaseStock(int $quantity): bool
    {
        $this->increment('stock_quantity', $quantity);
        return true;
    }

    /**
     * Check if sufficient stock is available
     */
    public function hasSufficientStock(int $requiredQuantity): bool
    {
        return $this->stock_quantity >= $requiredQuantity;
    }

    /**
     * Get the full URL for the product image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    /**
     * Check if product has an image
     */
    public function hasImage()
    {
        return !empty($this->image_path) && Storage::disk('public')->exists($this->image_path);
    }

    // ==================== CRM RELATIONSHIPS ====================

    /**
     * Get wishlist entries for this product
     */
    public function wishlists()
    {
        return $this->hasMany(CustomerWishlist::class);
    }

    /**
     * Get all feedback for this product
     */
    public function feedbacks()
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    /**
     * Get average rating from customer feedback
     */
    public function getAverageRatingAttribute()
    {
        return $this->feedbacks()->published()->avg('rating') ?? 0;
    }

    /**
     * Get total feedback count
     */
    public function getTotalFeedbackCountAttribute()
    {
        return $this->feedbacks()->published()->count();
    }
}

