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

    protected $fillable = [
        'user_id',
        'slug',
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
     * Generate unique slug from user name
     */
    public static function generateUniqueSlug(User $user, ?string $customSlug = null): string
    {
        $baseSlug = $customSlug ? Str::slug($customSlug) : Str::slug($user->name);
        $slug = $baseSlug;
        $counter = 1;

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
