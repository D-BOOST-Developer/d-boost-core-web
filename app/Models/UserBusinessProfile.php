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
    public function getGoogleMapsEmbedUrlAttribute(): ?string
    {
        if (!$this->google_maps_link) {
            return null;
        }

        // If it's already an embed URL
        if (str_contains($this->google_maps_link, 'google.com/maps/embed')) {
            return $this->google_maps_link;
        }

        // Extract coordinates or place ID from various Google Maps URL formats
        $url = $this->google_maps_link;
        
        // Try to extract coordinates from @lat,lng format
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
            return "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3000!2d{$lng}!3d{$lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM!5e0!3m2!1sen!2sid!4v1";
        }

        // If URL contains place, convert to embed format
        if (str_contains($url, 'google.com/maps/place')) {
            return str_replace('/maps/place/', '/maps/embed/v1/place?q=', $url) . '&key=';
        }

        // Return original URL as embed attempt
        return "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15000!2d106.8456!3d-6.2088!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM!5e0!3m2!1sen!2sid!4v1";
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
}
