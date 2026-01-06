<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MarketplaceLink extends Model
{
    use HasFactory, HasUuidAndAudit, SoftDeletes;

    protected $fillable = [
        'user_id',
        'link_type',
        'platform',
        'url',
        'display_name',
        'description',
        'image_path',
        'price',
        'priority_order',
        'is_active',
        'click_count',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'click_count' => 'integer',
            'priority_order' => 'integer',
        ];
    }

    /**
     * Get the user that owns this marketplace link
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for links by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('link_type', $type);
    }

    /**
     * Scope for ordered links (by priority)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Auto-detect marketplace platform from URL
     */
    public static function detectPlatform(string $url): string
    {
        $patterns = [
            'shopee' => '/shopee\.co\.id/i',
            'tokopedia' => '/tokopedia\.com/i',
            'lazada' => '/lazada\.co\.id/i',
            'bukalapak' => '/bukalapak\.com/i',
            'blibli' => '/blibli\.com/i',
            'jdid' => '/jd\.id/i',
            'zalora' => '/zalora\.co\.id/i',
            'bhinneka' => '/bhinneka\.com/i',
            'instagram' => '/instagram\.com/i',
            'facebook' => '/facebook\.com/i',
            'whatsapp' => '/wa\.me|whatsapp\.com/i',
        ];

        foreach ($patterns as $platform => $pattern) {
            if (preg_match($pattern, $url)) {
                return $platform;
            }
        }

        return 'other';
    }

    /**
     * Increment click count for analytics
     */
    public function incrementClicks(): void
    {
        $this->increment('click_count');
    }

    /**
     * Get the full URL for the link image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    /**
     * Check if link has an image
     */
    public function hasImage(): bool
    {
        return !empty($this->image_path) && Storage::disk('public')->exists($this->image_path);
    }

    /**
     * Get platform icon/badge
     */
    public function getPlatformIconAttribute(): string
    {
        $icons = [
            'shopee' => '🛍️',
            'tokopedia' => '🛒',
            'lazada' => '🏪',
            'bukalapak' => '🏬',
            'blibli' => '🏢',
            'instagram' => '📷',
            'facebook' => '👥',
            'whatsapp' => '💬',
        ];

        return $icons[$this->platform] ?? '🔗';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): ?string
    {
        if (!$this->price) {
            return null;
        }

        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
