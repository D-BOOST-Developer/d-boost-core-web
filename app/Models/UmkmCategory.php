<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class UmkmCategory extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'description',
        'min_business_age',
        'max_business_age',
        'min_revenue',
        'max_revenue',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_business_age' => 'integer',
        'max_business_age' => 'integer',
        'min_revenue' => 'integer',
        'max_revenue' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->id)) {
                $category->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get all business profiles in this category
     */
    public function businessProfiles()
    {
        return $this->hasMany(UserBusinessProfile::class);
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Determine UMKM category based on business metrics
     * 
     * @param int $businessAgeMonths
     * @param int $monthlyRevenue
     * @return UmkmCategory|null
     */
    public static function determineCategory(int $businessAgeMonths, int $monthlyRevenue): ?UmkmCategory
    {
        // Try to find exact match based on both age and revenue
        $category = self::active()
            ->where(function ($query) use ($businessAgeMonths) {
                $query->where(function ($q) use ($businessAgeMonths) {
                    $q->where('min_business_age', '<=', $businessAgeMonths)
                      ->where(function ($subQ) use ($businessAgeMonths) {
                          $subQ->whereNull('max_business_age')
                               ->orWhere('max_business_age', '>=', $businessAgeMonths);
                      });
                })
                ->orWhereNull('min_business_age');
            })
            ->where(function ($query) use ($monthlyRevenue) {
                $query->where(function ($q) use ($monthlyRevenue) {
                    $q->where('min_revenue', '<=', $monthlyRevenue)
                      ->where(function ($subQ) use ($monthlyRevenue) {
                          $subQ->whereNull('max_revenue')
                               ->orWhere('max_revenue', '>=', $monthlyRevenue);
                      });
                })
                ->orWhereNull('min_revenue');
            })
            ->orderByRaw("CASE code WHEN 'STARTING' THEN 1 WHEN 'STAGNANT' THEN 2 WHEN 'GROWING' THEN 3 END")
            ->first();
            
        // If no exact match, use fallback logic based on revenue priority
        if (!$category) {
            // High revenue (> 50M) = GROWING, regardless of age
            if ($monthlyRevenue >= 50000000) {
                $category = self::where('code', 'GROWING')->first();
            }
            // Medium revenue (10M - 50M) = STAGNANT
            elseif ($monthlyRevenue >= 10000000) {
                $category = self::where('code', 'STAGNANT')->first();
            }
            // Low revenue (< 10M) = STARTING
            else {
                $category = self::where('code', 'STARTING')->first();
            }
        }
        
        return $category;
    }

    /**
     * Get category by code
     */
    public static function getByCode(string $code): ?UmkmCategory
    {
        return self::where('code', $code)->first();
    }
}
