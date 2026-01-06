<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerLoyaltyProgram extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'points_per_idr',
        'tier_thresholds',
        'tier_benefits',
        'rewards',
        'is_active',
        'start_date',
        'end_date',
        'points_expiry_days',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'points_per_idr' => 'decimal:4',
            'tier_thresholds' => 'array',
            'tier_benefits' => 'array',
            'rewards' => 'array',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Calculate points for a given amount
     */
    public function calculatePointsForAmount(float $amount): float
    {
        return round($amount * $this->points_per_idr, 2);
    }

    /**
     * Get tier for given points
     */
    public function getTierForPoints(float $points): string
    {
        $thresholds = $this->tier_thresholds ?? [
            'bronze' => 0,
            'silver' => 1000,
            'gold' => 5000,
            'platinum' => 20000,
        ];

        if ($points >= $thresholds['platinum']) return 'platinum';
        if ($points >= $thresholds['gold']) return 'gold';
        if ($points >= $thresholds['silver']) return 'silver';
        return 'bronze';
    }

    /**
     * Get benefits for a tier
     */
    public function getTierBenefits(string $tier): array
    {
        $benefits = $this->tier_benefits ?? [];
        return $benefits[$tier] ?? [];
    }

    /**
     * Check if program is currently active
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        
        if ($this->start_date && $now < $this->start_date) {
            return false;
        }
        
        if ($this->end_date && $now > $this->end_date) {
            return false;
        }
        
        return true;
    }

    /**
     * Scope for active programs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }
}
