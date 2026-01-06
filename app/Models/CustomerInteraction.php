<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerInteraction extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'interaction_type',
        'subject',
        'description',
        'interaction_date',
        'status',
        'priority',
        'assigned_to',
        'follow_up_date',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'interaction_date' => 'datetime',
            'follow_up_date' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($interaction) {
            // Auto-set user_id from customer if not set
            if (empty($interaction->user_id) && $interaction->customer) {
                $interaction->user_id = $interaction->customer->user_id;
            }
        });
    }

    /**
     * Get the customer for this interaction
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user assigned to this interaction
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this interaction
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for interactions by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('interaction_type', $type);
    }

    /**
     * Scope for interactions by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending interactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for scheduled interactions
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for overdue interactions
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('interaction_date', '<', now());
    }

    /**
     * Scope for upcoming follow-ups
     */
    public function scopeUpcomingFollowUps($query, $days = 7)
    {
        return $query->whereNotNull('follow_up_date')
                    ->whereBetween('follow_up_date', [now(), now()->addDays($days)])
                    ->orderBy('follow_up_date');
    }

    /**
     * Check if interaction is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'scheduled' && $this->interaction_date < now();
    }

    /**
     * Mark interaction as completed
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Get interaction type label
     */
    public function getInteractionTypeLabelAttribute(): string
    {
        return match($this->interaction_type) {
            'call' => 'Panggilan',
            'email' => 'Email',
            'sms' => 'SMS',
            'chat' => 'Chat',
            'meeting' => 'Pertemuan',
            'visit' => 'Kunjungan',
            'feedback' => 'Umpan Balik',
            default => 'Lainnya'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Tertunda',
            'scheduled' => 'Dijadwalkan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => 'Normal'
        };
    }

    /**
     * Check if interaction is due soon (within 3 days)
     */
    public function isDueSoon(): bool
    {
        return $this->status === 'scheduled' && 
               $this->interaction_date <= now()->addDays(3) &&
               $this->interaction_date > now();
    }

    /**
     * Mark as pending
     */
    public function markAsPending()
    {
        $this->update(['status' => 'pending']);
    }

    /**
     * Mark as scheduled
     */
    public function markAsScheduled()
    {
        $this->update(['status' => 'scheduled']);
    }

    /**
     * Cancel interaction
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}
