<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerFeedback extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $table = 'customer_feedbacks';

    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_id',
        'product_id',
        'rating',
        'feedback_type',
        'comment',
        'is_published',
        'admin_response',
        'responded_by',
        'responded_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'responded_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feedback) {
            // Auto-set user_id from customer if not set
            if (empty($feedback->user_id) && $feedback->customer) {
                $feedback->user_id = $feedback->customer->user_id;
            }
        });
    }

    /**
     * Get the customer who gave feedback
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the related sale
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the related product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who responded
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Scope by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for published feedback
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for pending feedback (not responded)
     */
    public function scopePending($query)
    {
        return $query->whereNull('admin_response');
    }

    /**
     * Scope for feedback with responses
     */
    public function scopeResponded($query)
    {
        return $query->whereNotNull('admin_response');
    }

    /**
     * Scope for positive feedback (4-5 stars)
     */
    public function scopePositive($query)
    {
        return $query->whereIn('rating', [4, 5]);
    }

    /**
     * Scope for negative feedback (1-2 stars)
     */
    public function scopeNegative($query)
    {
        return $query->whereIn('rating', [1, 2]);
    }

    /**
     * Scope by feedback type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('feedback_type', $type);
    }

    /**
     * Get star rating as HTML
     */
    public function getStarsHtmlAttribute(): string
    {
        $fullStars = $this->rating;
        $emptyStars = 5 - $fullStars;
        
        $html = str_repeat('★', $fullStars);
        $html .= str_repeat('☆', $emptyStars);
        
        return $html;
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
     * Check if feedback has been responded
     */
    public function hasResponse(): bool
    {
        return !is_null($this->admin_response);
    }

    /**
     * Get rating label
     */
    public function getRatingLabelAttribute(): string
    {
        return match($this->rating) {
            5 => 'Sangat Baik',
            4 => 'Baik',
            3 => 'Cukup',
            2 => 'Kurang',
            1 => 'Sangat Kurang',
            default => 'Tidak Dinilai'
        };
    }

    /**
     * Get feedback type label
     */
    public function getFeedbackTypeLabelAttribute(): string
    {
        return match($this->feedback_type) {
            'complaint' => 'Keluhan',
            'suggestion' => 'Saran',
            'praise' => 'Pujian',
            'question' => 'Pertanyaan',
            default => 'Lainnya'
        };
    }
}
