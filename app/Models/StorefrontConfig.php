<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StorefrontConfig extends Model
{
    use HasFactory, HasUuidAndAudit;

    /**
     * @deprecated Storefront now uses the unified MONŌ layout. Template constants are kept for backward
     * compatibility with legacy DB rows but are no longer dispatched on by controllers.
     */
    public const TEMPLATE_DEFAULT = 'default';
    /** @deprecated */
    public const TEMPLATE_CLOTHING = 'clothing';
    /** @deprecated */
    public const TEMPLATE_FOOD_BEVERAGE = 'food_beverage';
    /** @deprecated */
    public const TEMPLATE_ELECTRONICS = 'electronics';

    /** @deprecated MONŌ is the only layout; this map is unused. */
    public const TEMPLATE_TYPES = [
        self::TEMPLATE_DEFAULT => 'Default',
        self::TEMPLATE_CLOTHING => 'Clothing',
        self::TEMPLATE_FOOD_BEVERAGE => 'Food & Beverage',
        self::TEMPLATE_ELECTRONICS => 'Electronics',
    ];

    protected $fillable = [
        'user_id',
        'slug',
        'template_type',
        'storefront_title',
        'tagline',
        'logo_path',
        'header_bg_type',
        'header_bg_value',
        'primary_color',
        'secondary_color',
        'text_color',
        'grid_columns',
        'card_style',
        'card_spacing',
        'text_blocks',
        'footer_content',
        'social_links',
        'meta_description',
        'meta_keywords',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'text_blocks' => 'array',
            'social_links' => 'array',
            'is_published' => 'boolean',
            'grid_columns' => 'integer',
            'card_spacing' => 'integer',
        ];
    }

    /**
     * Get the user that owns this storefront config
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get client reviews for this storefront
     */
    public function reviews()
    {
        return $this->hasMany(ClientReview::class);
    }

    public const RESERVED_SLUGS = [
        'admin', 'api', 'dashboard', 'login', 'register', 'store', 'public',
        'assets', 'storage', 'onboarding', 'profile', 'create', 'edit',
        'preview', 'builder', 'logout', 'home', 'auth', 'settings',
    ];

    /**
     * Generate unique slug for the user's storefront.
     *
     * Priority: explicit customSlug → business_name from UserBusinessProfile →
     * user name → fallback "store-{userId}". Slug is NOT auto-regenerated when
     * business_name changes later — that would break shared/SEO links.
     */
    public static function generateUniqueSlug(User $user, ?string $customSlug = null): string
    {
        $user->loadMissing('businessProfile');

        $sources = [
            $customSlug,
            $user->businessProfile?->business_name,
            $user->name,
        ];

        $baseSlug = '';
        foreach ($sources as $source) {
            if (filled($source)) {
                $candidate = Str::slug($source);
                if ($candidate !== '') {
                    $baseSlug = $candidate;
                    break;
                }
            }
        }

        if ($baseSlug === '') {
            $baseSlug = 'store-' . $user->id;
        }

        if (in_array($baseSlug, self::RESERVED_SLUGS, true)) {
            $baseSlug .= '-store';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (self::where('slug', $slug)->where('user_id', '!=', $user->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get public URL for this storefront
     */
    public function getPublicUrlAttribute(): string
    {
        return route('public.store.show', ['slug' => $this->slug]);
    }

    /**
     * Get default color scheme
     */
    public static function getDefaultColors(): array
    {
        return [
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'text_color' => '#1F2937',
        ];
    }

    /**
     * Get template-specific default styles
     */
    public static function getTemplateDefaults(string $templateType): array
    {
        return match ($templateType) {
            self::TEMPLATE_CLOTHING => [
                'primary_color' => '#C9B99A',
                'secondary_color' => '#3D3D3D',
                'text_color' => '#1A1A1A',
                'header_bg_type' => 'color',
                'header_bg_value' => '#F5F3EF',
                'card_style' => 'minimal',
                'grid_columns' => 3,
            ],
            self::TEMPLATE_FOOD_BEVERAGE => [
                'primary_color' => '#3BB77E',
                'secondary_color' => '#FDC040',
                'text_color' => '#253D4E',
                'header_bg_type' => 'color',
                'header_bg_value' => '#FFFFFF',
                'card_style' => 'shadow',
                'grid_columns' => 4,
            ],
            self::TEMPLATE_ELECTRONICS => [
                'primary_color' => '#E74C3C',
                'secondary_color' => '#2C3E50',
                'text_color' => '#1A1A1A',
                'header_bg_type' => 'color',
                'header_bg_value' => '#FFFFFF',
                'card_style' => 'bordered',
                'grid_columns' => 5,
            ],
            default => [
                'primary_color' => '#3B82F6',
                'secondary_color' => '#10B981',
                'text_color' => '#1F2937',
                'header_bg_type' => 'gradient',
                'header_bg_value' => null,
                'card_style' => 'shadow',
                'grid_columns' => 3,
            ],
        };
    }

    /**
     * Publish the storefront
     */
    public function publish(): void
    {
        $this->update(['is_published' => true]);
    }

    /**
     * Unpublish the storefront
     */
    public function unpublish(): void
    {
        $this->update(['is_published' => false]);
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(): bool
    {
        $newStatus = !$this->is_published;
        $this->update(['is_published' => $newStatus]);
        return $newStatus;
    }

    /**
     * Get the full URL for the logo
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }

    /**
     * Check if logo exists
     */
    public function hasLogo(): bool
    {
        return !empty($this->logo_path) && Storage::disk('public')->exists($this->logo_path);
    }

    /**
     * Get header background CSS
     */
    public function getHeaderBackgroundCssAttribute(): string
    {
        switch ($this->header_bg_type) {
            case 'gradient':
                return $this->header_bg_value ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            case 'image':
                if ($this->header_bg_value) {
                    $url = asset('storage/' . $this->header_bg_value);
                    return "url('{$url}')";
                }
                return $this->primary_color;
            case 'color':
            default:
                return $this->header_bg_value ?? $this->primary_color;
        }
    }

    /**
     * Scope for published storefronts
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
