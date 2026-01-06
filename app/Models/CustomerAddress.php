<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'address_type',
        'label',
        'recipient_name',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'is_default',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            // Auto-set user_id from customer if not set
            if (empty($address->user_id) && $address->customer) {
                $address->user_id = $address->customer->user_id;
            }
        });
    }

    /**
     * Set this address as default
     */
    public function setAsDefault()
    {
        // Unset other default addresses for this customer
        self::where('customer_id', $this->customer_id)
            ->where('id', '!=', $this->id)
            ->where('address_type', $this->address_type)
            ->update(['is_default' => false]);

        // Set this address as default
        $this->update(['is_default' => true]);
    }

    /**
     * Scope for default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope by address type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('address_type', $type);
    }

    /**
     * Scope for shipping addresses
     */
    public function scopeShipping($query)
    {
        return $query->whereIn('address_type', ['shipping', 'both']);
    }

    /**
     * Scope for billing addresses
     */
    public function scopeBilling($query)
    {
        return $query->whereIn('address_type', ['billing', 'both']);
    }

    /**
     * Get full formatted address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
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
     * Get address type label
     */
    public function getAddressTypeLabelAttribute(): string
    {
        return match($this->address_type) {
            'shipping' => 'Pengiriman',
            'billing' => 'Tagihan',
            'both' => 'Pengiriman & Tagihan',
            default => 'Lainnya'
        };
    }

    /**
     * Get short address (first line only)
     */
    public function getShortAddressAttribute(): string
    {
        return $this->address ?? '';
    }

    /**
     * Get city and province
     */
    public function getCityProvinceAttribute(): string
    {
        $parts = array_filter([$this->city, $this->province]);
        return implode(', ', $parts);
    }
}
