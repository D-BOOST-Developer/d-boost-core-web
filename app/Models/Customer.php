<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'gender',
        'age',
        'city',
        'address',
        'date_of_birth',
        'total_spent',
        'purchase_frequency',
        'last_purchase_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'date_of_birth' => 'date',
            'total_spent' => 'decimal:2',
            'purchase_frequency' => 'integer',
            'last_purchase_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all sales for this customer
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get customer's average order value
     */
    public function getAverageOrderValueAttribute()
    {
        $totalSales = $this->sales()->count();
        return $totalSales > 0 ? $this->total_spent / $totalSales : 0;
    }

    /**
     * Get customer's lifetime value
     */
    public function getLifetimeValueAttribute()
    {
        return $this->total_spent;
    }

    /**
     * Check if customer is VIP (high value)
     */
    public function isVip(): bool
    {
        return $this->total_spent >= 1000000; // 1M IDR threshold
    }

    /**
     * Get customer's age group
     */
    public function getAgeGroupAttribute()
    {
        if (!$this->age) return 'Unknown';
        
        if ($this->age < 18) return 'Under 18';
        if ($this->age < 25) return '18-24';
        if ($this->age < 35) return '25-34';
        if ($this->age < 45) return '35-44';
        if ($this->age < 55) return '45-54';
        return '55+';
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for VIP customers
     */
    public function scopeVip($query)
    {
        return $query->where('total_spent', '>=', 1000000);
    }

    /**
     * Scope for customers by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope for customers by gender
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    // ==================== CRM RELATIONSHIPS ====================

    /**
     * Get all interactions for this customer
     */
    public function interactions()
    {
        return $this->hasMany(CustomerInteraction::class);
    }

    /**
     * Get the segments this customer belongs to
     */
    public function segments()
    {
        return $this->belongsToMany(CustomerSegment::class, 'customer_segment_members')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    /**
     * Get loyalty points for this customer
     */
    public function loyaltyPoints()
    {
        return $this->hasOne(CustomerLoyaltyPoint::class);
    }

    /**
     * Get all feedback from this customer
     */
    public function feedbacks()
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    /**
     * Get wishlist items for this customer
     */
    public function wishlists()
    {
        return $this->hasMany(CustomerWishlist::class);
    }

    /**
     * Get internal notes for this customer
     */
    public function notes()
    {
        return $this->hasMany(CustomerNote::class);
    }

    /**
     * Get addresses for this customer
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    // ==================== CRM COMPUTED ATTRIBUTES ====================

    /**
     * Calculate RFM (Recency, Frequency, Monetary) Score
     * Returns array with individual scores and total
     */
    public function getRfmScoreAttribute(): array
    {
        // Recency Score (1-5, 5 being most recent)
        $daysSinceLastPurchase = $this->last_purchase_date 
            ? now()->diffInDays($this->last_purchase_date) 
            : 9999;
        
        if ($daysSinceLastPurchase <= 30) $recencyScore = 5;
        elseif ($daysSinceLastPurchase <= 60) $recencyScore = 4;
        elseif ($daysSinceLastPurchase <= 90) $recencyScore = 3;
        elseif ($daysSinceLastPurchase <= 180) $recencyScore = 2;
        else $recencyScore = 1;

        // Frequency Score (1-5, 5 being most frequent)
        $frequency = $this->purchase_frequency ?? 0;
        if ($frequency >= 10) $frequencyScore = 5;
        elseif ($frequency >= 7) $frequencyScore = 4;
        elseif ($frequency >= 4) $frequencyScore = 3;
        elseif ($frequency >= 2) $frequencyScore = 2;
        else $frequencyScore = 1;

        // Monetary Score (1-5, 5 being highest spending)
        $spent = $this->total_spent ?? 0;
        if ($spent >= 5000000) $monetaryScore = 5; // 5M+
        elseif ($spent >= 2000000) $monetaryScore = 4; // 2M+
        elseif ($spent >= 1000000) $monetaryScore = 3; // 1M+
        elseif ($spent >= 500000) $monetaryScore = 2; // 500K+
        else $monetaryScore = 1;

        $totalScore = $recencyScore + $frequencyScore + $monetaryScore;

        return [
            'recency' => $recencyScore,
            'frequency' => $frequencyScore,
            'monetary' => $monetaryScore,
            'total' => $totalScore,
            'max' => 15,
        ];
    }

    /**
     * Get customer value segment (VIP, High, Medium, Low)
     */
    public function getCustomerValueSegmentAttribute(): string
    {
        $rfm = $this->rfm_score;
        $total = $rfm['total'];

        if ($total >= 12) return 'VIP'; // Champions
        if ($total >= 9) return 'High Value'; // Loyal customers
        if ($total >= 6) return 'Medium Value'; // Potential loyalists
        return 'Low Value'; // At risk or new customers
    }

    /**
     * Calculate churn risk (0-100, higher = more risk)
     */
    public function getChurnRiskAttribute(): int
    {
        $risk = 0;

        // Days since last purchase (max 40 points)
        $daysSinceLastPurchase = $this->last_purchase_date 
            ? now()->diffInDays($this->last_purchase_date) 
            : 365;
        
        if ($daysSinceLastPurchase > 180) $risk += 40;
        elseif ($daysSinceLastPurchase > 90) $risk += 30;
        elseif ($daysSinceLastPurchase > 60) $risk += 20;
        elseif ($daysSinceLastPurchase > 30) $risk += 10;

        // Low purchase frequency (max 30 points)
        $frequency = $this->purchase_frequency ?? 0;
        if ($frequency == 0) $risk += 30;
        elseif ($frequency == 1) $risk += 20;
        elseif ($frequency <= 3) $risk += 10;

        // Low total spent (max 20 points)
        $spent = $this->total_spent ?? 0;
        if ($spent < 100000) $risk += 20;
        elseif ($spent < 500000) $risk += 10;

        // Inactive status (10 points)
        if (!$this->is_active) $risk += 10;

        return min($risk, 100);
    }

    /**
     * Get total loyalty points
     */
    public function getTotalLoyaltyPointsAttribute(): float
    {
        return $this->loyaltyPoints?->current_balance ?? 0;
    }

    /**
     * Get loyalty tier
     */
    public function getLoyaltyTierAttribute(): string
    {
        return $this->loyaltyPoints?->tier_level ?? 'bronze';
    }

    /**
     * Get last interaction date
     */
    public function getLastInteractionDateAttribute()
    {
        return $this->interactions()
                    ->latest('interaction_date')
                    ->value('interaction_date');
    }

    /**
     * Get recommended products based on purchase history
     */
    public function getRecommendedProductsAttribute()
    {
        // Get categories of previously purchased products
        $purchasedCategories = $this->sales()
            ->with('salesDetails.product')
            ->get()
            ->pluck('salesDetails')
            ->flatten()
            ->pluck('product.category')
            ->unique()
            ->filter()
            ->toArray();

        if (empty($purchasedCategories)) {
            // If no purchase history, return popular products
            return Product::active()
                         ->orderBy('stock_quantity', 'desc')
                         ->limit(5)
                         ->get();
        }

        // Return products from same categories that customer hasn't purchased yet
        $purchasedProductIds = $this->sales()
            ->with('salesDetails')
            ->get()
            ->pluck('salesDetails')
            ->flatten()
            ->pluck('product_id')
            ->unique()
            ->toArray();

        return Product::active()
                     ->whereIn('category', $purchasedCategories)
                     ->whereNotIn('id', $purchasedProductIds)
                     ->orderBy('stock_quantity', 'desc')
                     ->limit(5)
                     ->get();
    }

    // ==================== ADDITIONAL SCOPES ====================

    /**
     * Scope for customers by segment
     */
    public function scopeBySegment($query, $segmentId)
    {
        return $query->whereHas('segments', function ($q) use ($segmentId) {
            $q->where('customer_segments.id', $segmentId);
        });
    }

    /**
     * Scope for high value customers
     */
    public function scopeHighValue($query)
    {
        return $query->where('total_spent', '>=', 2000000);
    }

    /**
     * Scope for at-risk customers
     */
    public function scopeAtRisk($query)
    {
        return $query->where('last_purchase_date', '<', now()->subDays(90))
                    ->where('purchase_frequency', '>', 0); // Had purchases before
    }

    /**
     * Scope for customers without recent purchase
     */
    public function scopeWithoutRecentPurchase($query, $days = 30)
    {
        return $query->where(function ($q) use ($days) {
            $q->where('last_purchase_date', '<', now()->subDays($days))
              ->orWhereNull('last_purchase_date');
        });
    }

    /**
     * Scope for new customers (registered within X days)
     */
    public function scopeNewCustomers($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for customers by loyalty tier
     */
    public function scopeByLoyaltyTier($query, $tier)
    {
        return $query->whereHas('loyaltyPoints', function ($q) use ($tier) {
            $q->where('tier_level', $tier);
        });
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
     * Scope for customers with recent interactions
     */
    public function scopeWithRecentInteractions($query, $days = 30)
    {
        return $query->whereHas('interactions', function ($q) use ($days) {
            $q->where('interaction_date', '>=', now()->subDays($days));
        });
    }

    /**
     * Scope for customers with pending interactions
     */
    public function scopeWithPendingInteractions($query)
    {
        return $query->whereHas('interactions', function ($q) {
            $q->where('status', 'pending');
        });
    }

    /**
     * Get customer's lifetime value in formatted currency
     */
    public function getFormattedLifetimeValueAttribute(): string
    {
        return 'Rp ' . number_format($this->total_spent, 0, ',', '.');
    }

    /**
     * Get customer's average order value in formatted currency
     */
    public function getFormattedAverageOrderValueAttribute(): string
    {
        $totalSales = $this->sales()->count();
        $avg = $totalSales > 0 ? $this->total_spent / $totalSales : 0;
        return 'Rp ' . number_format($avg, 0, ',', '.');
    }

    /**
     * Check if customer has pending interactions
     */
    public function hasPendingInteractions(): bool
    {
        return $this->interactions()->where('status', 'pending')->exists();
    }

    /**
     * Get customer's purchase history count
     */
    public function getPurchaseHistoryCountAttribute(): int
    {
        return $this->sales()->count();
    }

    /**
     * Get customer's total feedback count
     */
    public function getTotalFeedbackCountAttribute(): int
    {
        return $this->feedbacks()->count();
    }

    /**
     * Get customer's wishlist count
     */
    public function getWishlistCountAttribute(): int
    {
        return $this->wishlists()->count();
    }

    /**
     * Get customer's total notes count
     */
    public function getTotalNotesCountAttribute(): int
    {
        return $this->notes()->count();
    }

    /**
     * Get customer's total addresses count
     */
    public function getTotalAddressesCountAttribute(): int
    {
        return $this->addresses()->count();
    }
}
