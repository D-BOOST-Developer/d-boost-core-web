<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLoyaltyPoint extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser;

    protected $fillable = [
        'user_id',
        'customer_id',
        'loyalty_program_id',
        'points_earned',
        'points_redeemed',
        'points_expired',
        'current_balance',
        'tier_level',
        'last_tier_update',
        'last_earned_at',
        'last_redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'points_earned' => 'decimal:2',
            'points_redeemed' => 'decimal:2',
            'points_expired' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'last_tier_update' => 'datetime',
            'last_earned_at' => 'datetime',
            'last_redeemed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loyaltyPoint) {
            // Auto-set user_id from customer if not set
            if (empty($loyaltyPoint->user_id) && $loyaltyPoint->customer) {
                $loyaltyPoint->user_id = $loyaltyPoint->customer->user_id;
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
     * Get the loyalty program
     */
    public function loyaltyProgram()
    {
        return $this->belongsTo(CustomerLoyaltyProgram::class, 'loyalty_program_id');
    }

    /**
     * Add points
     */
    public function addPoints(float $points, string $reason = 'Purchase')
    {
        $this->increment('points_earned', $points);
        $this->increment('current_balance', $points);
        $this->update(['last_earned_at' => now()]);

        // Update tier if needed
        $this->updateTier();

        // Log the transaction (could be in a separate points_transactions table)
        // For now, we'll just update the balance
    }

    /**
     * Redeem points
     */
    public function redeemPoints(float $points, string $reason = 'Reward redemption')
    {
        if ($this->current_balance < $points) {
            throw new \Exception('Insufficient points balance');
        }

        $this->increment('points_redeemed', $points);
        $this->decrement('current_balance', $points);
        $this->update(['last_redeemed_at' => now()]);

        // Update tier if needed (customer might have gone down a tier)
        $this->updateTier();
    }

    /**
     * Update customer tier based on current balance
     */
    public function updateTier()
    {
        if (!$this->loyaltyProgram) {
            return;
        }

        $newTier = $this->loyaltyProgram->getTierForPoints($this->current_balance);

        if ($newTier !== $this->tier_level) {
            $this->update([
                'tier_level' => $newTier,
                'last_tier_update' => now(),
            ]);
        }
    }

    /**
     * Expire old points (if program has expiry)
     */
    public function expireOldPoints()
    {
        if (!$this->loyaltyProgram || !$this->loyaltyProgram->points_expiry_days) {
            return;
        }

        // This is a simplified version. In production, you'd track individual point transactions
        // with their earn dates and expire them individually.
        $expiryDate = now()->subDays($this->loyaltyProgram->points_expiry_days);
        
        if ($this->last_earned_at && $this->last_earned_at < $expiryDate) {
            // Expire all points for simplicity (in reality, track individual transactions)
            $expiredPoints = $this->current_balance;
            $this->increment('points_expired', $expiredPoints);
            $this->update(['current_balance' => 0]);
            $this->updateTier();
        }
    }

    /**
     * Get tier level label
     */
    public function getTierLabelAttribute(): string
    {
        return match($this->tier_level) {
            'bronze' => 'Perunggu',
            'silver' => 'Perak',
            'gold' => 'Emas',
            'platinum' => 'Platinum',
            'diamond' => 'Berlian',
            default => 'Standar'
        };
    }

    /**
     * Get tier color
     */
    public function getTierColorAttribute(): string
    {
        return match($this->tier_level) {
            'bronze' => '#CD7F32',
            'silver' => '#C0C0C0',
            'gold' => '#FFD700',
            'platinum' => '#E5E4E2',
            'diamond' => '#B9F2FF',
            default => '#808080'
        };
    }

    /**
     * Check if customer can redeem points
     */
    public function canRedeem($points): bool
    {
        return $this->current_balance >= $points;
    }

    /**
     * Get remaining points until next tier
     */
    public function getPointsUntilNextTierAttribute(): float
    {
        if (!$this->loyaltyProgram) {
            return 0;
        }

        $tiers = $this->loyaltyProgram->getTierThresholds();
        $currentPoints = $this->current_balance;

        foreach ($tiers as $tier => $threshold) {
            if ($currentPoints < $threshold) {
                return $threshold - $currentPoints;
            }
        }

        return 0;
    }

    /**
     * Get points progress percentage
     */
    public function getPointsProgressAttribute(): float
    {
        if (!$this->loyaltyProgram) {
            return 0;
        }

        $tiers = $this->loyaltyProgram->getTierThresholds();
        $currentPoints = $this->current_balance;

        // Find current and next tier
        $currentTierPoints = 0;
        $nextTierPoints = 0;

        foreach ($tiers as $tier => $threshold) {
            if ($currentPoints >= $threshold) {
                $currentTierPoints = $threshold;
            } else {
                $nextTierPoints = $threshold;
                break;
            }
        }

        if ($nextTierPoints === 0) {
            return 100;
        }

        $progress = (($currentPoints - $currentTierPoints) / ($nextTierPoints - $currentTierPoints)) * 100;
        return min($progress, 100);
    }
}
