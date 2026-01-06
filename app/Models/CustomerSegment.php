<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSegment extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'criteria',
        'segment_type',
        'customer_count',
        'is_active',
        'last_refreshed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'is_active' => 'boolean',
            'last_refreshed_at' => 'datetime',
        ];
    }

    /**
     * Get all customers in this segment
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_segment_members')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    /**
     * Refresh segment members based on criteria (for auto segments)
     */
    public function refreshMembers()
    {
        if ($this->segment_type !== 'auto' || !$this->criteria) {
            return;
        }

        // Start with all customers
        $query = Customer::query();

        // Apply criteria filters
        $criteria = $this->criteria;

        // Total spent criteria
        if (isset($criteria['total_spent_min'])) {
            $query->where('total_spent', '>=', $criteria['total_spent_min']);
        }
        if (isset($criteria['total_spent_max'])) {
            $query->where('total_spent', '<=', $criteria['total_spent_max']);
        }

        // Purchase frequency criteria
        if (isset($criteria['purchase_frequency_min'])) {
            $query->where('purchase_frequency', '>=', $criteria['purchase_frequency_min']);
        }

        // City criteria
        if (isset($criteria['city'])) {
            $query->whereIn('city', (array)$criteria['city']);
        }

        // Gender criteria
        if (isset($criteria['gender'])) {
            $query->whereIn('gender', (array)$criteria['gender']);
        }

        // Age range criteria
        if (isset($criteria['age_min'])) {
            $query->where('age', '>=', $criteria['age_min']);
        }
        if (isset($criteria['age_max'])) {
            $query->where('age', '<=', $criteria['age_max']);
        }

        // Last purchase date criteria (recency)
        if (isset($criteria['last_purchase_days'])) {
            $query->where('last_purchase_date', '>=', now()->subDays($criteria['last_purchase_days']));
        }

        // Active status
        if (isset($criteria['is_active'])) {
            $query->where('is_active', $criteria['is_active']);
        }

        // Get matching customer IDs
        $customerIds = $query->pluck('id')->toArray();

        // Sync customers to segment
        $this->customers()->sync($customerIds);

        // Update customer count and last refreshed timestamp
        $this->update([
            'customer_count' => count($customerIds),
            'last_refreshed_at' => now(),
        ]);
    }

    /**
     * Scope for active segments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for auto segments
     */
    public function scopeAutoSegments($query)
    {
        return $query->where('segment_type', 'auto');
    }

    /**
     * Scope for manual segments
     */
    public function scopeManualSegments($query)
    {
        return $query->where('segment_type', 'manual');
    }

    /**
     * Get segment type label
     */
    public function getSegmentTypeLabelAttribute(): string
    {
        return match($this->segment_type) {
            'auto' => 'Otomatis',
            'manual' => 'Manual',
            default => 'Tidak Diketahui'
        };
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
     * Toggle segment status
     */
    public function toggleStatus()
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Activate segment
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate segment
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Add customer to segment
     */
    public function addCustomer($customerId)
    {
        $this->customers()->attach($customerId);
        $this->incrementCustomerCount();
    }

    /**
     * Remove customer from segment
     */
    public function removeCustomer($customerId)
    {
        $this->customers()->detach($customerId);
        $this->decrementCustomerCount();
    }

    /**
     * Increment customer count
     */
    public function incrementCustomerCount()
    {
        $this->increment('customer_count');
    }

    /**
     * Decrement customer count
     */
    public function decrementCustomerCount()
    {
        $this->decrement('customer_count');
    }
}
