<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class UserBusinessProfile extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_category',
        'business_logo_path',
        'business_age_month',
        'monthly_revenue_range',
        'umkm_category_id',
        'owner_name',
        'contact_email',
        'contact_phone',
        'usage_reason',
        'onboarding_completed',
        // New fields
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'qris_barcode_path',
        'google_maps_link',
        'business_address',
    ];

    protected $casts = [
        'business_age_month' => 'integer',
        'onboarding_completed' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($profile) {
            if (empty($profile->id)) {
                $profile->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns this business profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the UMKM category for this business
     */
    public function umkmCategory()
    {
        return $this->belongsTo(UmkmCategory::class);
    }

    /**
     * Check if onboarding is completed
     */
    public function isOnboardingCompleted(): bool
    {
        return $this->onboarding_completed;
    }

    /**
     * Mark onboarding as completed
     */
    public function completeOnboarding(): void
    {
        $this->update(['onboarding_completed' => true]);
    }

    /**
     * Get formatted revenue range
     */
    public function getFormattedRevenueAttribute(): string
    {
        return $this->monthly_revenue_range;
    }

    /**
     * Get business age in years (formatted)
     */
    public function getBusinessAgeYearsAttribute(): float
    {
        return round($this->business_age_month / 12, 1);
    }

    /**
     * Scope to get only completed profiles
     */
    public function scopeCompleted($query)
    {
        return $query->where('onboarding_completed', true);
    }

    /**
     * Scope to get profiles by UMKM category
     */
    public function scopeByCategory($query, string $categoryCode)
    {
        return $query->whereHas('umkmCategory', function ($q) use ($categoryCode) {
            $q->where('code', $categoryCode);
        });
    }

    /**
     * Parse revenue range to get numeric value for categorization
     * Examples: "< 10M", "10M - 50M", "> 50M"
     */
    public function getRevenueNumericValue(): int
    {
        $range = $this->monthly_revenue_range;
        
        // Extract numbers from the range
        if (preg_match('/(\d+)/', $range, $matches)) {
            $value = (int) $matches[1];
            
            // Convert to actual value based on suffix
            if (stripos($range, 'M') !== false || stripos($range, 'juta') !== false) {
                return $value * 1000000; // Million
            } elseif (stripos($range, 'K') !== false || stripos($range, 'ribu') !== false) {
                return $value * 1000; // Thousand
            }
            
            return $value;
        }
        
        return 0;
    }

    /**
     * Get list of Indonesian banks for dropdown
     */
    public static function getIndonesianBanks(): array
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'MANDIRI' => 'Bank Mandiri',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'CIMB' => 'CIMB Niaga',
            'DANAMON' => 'Bank Danamon',
            'PERMATA' => 'Bank Permata',
            'OCBC' => 'Bank OCBC NISP',
            'PANIN' => 'Bank Panin',
            'MEGA' => 'Bank Mega',
            'BTN' => 'Bank BTN',
            'JAGO' => 'Bank Jago',
            'BTPN' => 'Bank BTPN',
            'SEABANK' => 'SeaBank Indonesia',
            'BLU' => 'Bank Digital BCA (Blu)',
            'JENIUS' => 'Jenius (BTPN)',
            'OTHER' => 'Lainnya',
        ];
    }

    /**
     * Get formatted bank information
     */
    public function getFormattedBankInfoAttribute(): ?string
    {
        if (!$this->bank_name) {
            return null;
        }

        $banks = self::getIndonesianBanks();
        $bankName = $banks[$this->bank_name] ?? $this->bank_name;
        
        return $bankName . ' - ' . $this->bank_account_number;
    }

    /**
     * Get Google Maps embed URL from link
     */
    /**
     * Get Google Maps embed URL from link
     */
    public function getGoogleMapsEmbedUrlAttribute(): ?string
    {
        $url = $this->google_maps_link;
        if (!$url) {
            // Fallback to address if no link
            if ($this->business_address) {
                return "https://maps.google.com/maps?q=" . urlencode($this->business_address) . "&z=15&output=embed";
            }
            return null;
        }

        // 1. If it's already an embed URL, return as is
        if (str_contains($url, 'output=embed') || str_contains($url, '/embed?')) {
            return $url;
        }

        // 2. Try to extract coordinates (@lat,lng)
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
            return "https://maps.google.com/maps?q={$lat},{$lng}&z=15&output=embed";
        }

        // 3. Try to match /maps/place/PLACE_NAME
        if (preg_match('/\/maps\/place\/([^\/]+)/', $url, $matches)) {
            $place = $matches[1];
            return "https://maps.google.com/maps?q={$place}&z=15&output=embed";
        }
        
        // 4. Try to match /maps/search/QUERY
        if (preg_match('/\/maps\/search\/([^\/]+)/', $url, $matches)) {
            $query = $matches[1];
            return "https://maps.google.com/maps?q={$query}&z=15&output=embed";
        }

        // 5. Short links (maps.app.goo.gl or goo.gl) - hard to parse without expansion
        // We fallback to address if present, or just try to use the address from the profile
        if ($this->business_address) {
            return "https://maps.google.com/maps?q=" . urlencode($this->business_address) . "&z=15&output=embed";
        }

        // 6. Last resort: default view (Jakarta) to avoid broken iframe
        return "https://maps.google.com/maps?q=-6.2088,106.8456&z=10&output=embed";
    }

    /**
     * Get business logo URL
     */
    public function getBusinessLogoUrlAttribute(): ?string
    {
        if (!$this->business_logo_path) {
            return null;
        }

        return asset('storage/' . $this->business_logo_path);
    }

    /**
     * Get QRIS image URL
     */
    public function getQrisImageUrlAttribute(): ?string
    {
        if (!$this->qris_barcode_path) {
            return null;
        }

        return asset('storage/' . $this->qris_barcode_path);
    }

    /**
     * Extract coordinates from Google Maps link
     * Returns array ['lat' => ..., 'lng' => ...] or null
     */
    public function getExtractedCoordinatesAttribute(): ?array
    {
        if (!$this->google_maps_link) {
            return null;
        }

        // Try to extract coordinates from @lat,lng format
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $this->google_maps_link, $matches)) {
            return [
                'lat' => $matches[1],
                'lng' => $matches[2],
            ];
        }

        return null;
    }

    /**
     * Get formatted coordinates string (lat, lng)
     */
    public function getFormattedCoordinatesAttribute(): ?string
    {
        $coords = $this->extracted_coordinates;
        if (!$coords) {
            return null;
        }

        return "{$coords['lat']}, {$coords['lng']}";
    }

    /**
     * Extract place name from Google Maps link
     */
    public function getExtractedPlaceNameAttribute(): ?string
    {
        if (!$this->google_maps_link) {
            return null;
        }

        // Try to match /maps/place/PLACE_NAME
        if (preg_match('/\/maps\/place\/([^\/]+)/', $this->google_maps_link, $matches)) {
            // URL decode and replace + with spaces
            return urldecode(str_replace('+', ' ', $matches[1]));
        }

        return null;
    }
}
