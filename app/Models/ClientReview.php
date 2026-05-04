<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ClientReview extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'storefront_config_id',
        'client_name',
        'client_email',
        'review_text',
        'rating',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($review) {
            if (empty($review->id)) {
                $review->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the storefront config that owns this review
     */
    public function storefrontConfig()
    {
        return $this->belongsTo(StorefrontConfig::class);
    }

    /**
     * Scope to get only approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Get formatted rating as stars
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get first name only for display
     */
    public function getDisplayNameAttribute(): string
    {
        $parts = explode(' ', $this->client_name);
        if (count($parts) > 1) {
            return $parts[0] . ' ' . substr($parts[1], 0, 1) . '.';
        }
        return $this->client_name;
    }
}
