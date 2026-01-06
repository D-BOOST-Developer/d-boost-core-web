<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerNote extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'note_type',
        'content',
        'is_pinned',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($note) {
            // Auto-set user_id from customer if not set
            if (empty($note->user_id) && $note->customer) {
                $note->user_id = $note->customer->user_id;
            }
        });
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created the note
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for pinned notes
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope by note type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('note_type', $type);
    }

    /**
     * Scope for important notes
     */
    public function scopeImportant($query)
    {
        return $query->where('note_type', 'important');
    }

    /**
     * Scope for warnings
     */
    public function scopeWarnings($query)
    {
        return $query->where('note_type', 'warning');
    }

    /**
     * Get note type label
     */
    public function getNoteLabelAttribute(): string
    {
        return match($this->note_type) {
            'general' => 'Umum',
            'important' => 'Penting',
            'warning' => 'Peringatan',
            'reminder' => 'Pengingat',
            default => 'Catatan'
        };
    }

    /**
     * Get note type color
     */
    public function getNoteColorAttribute(): string
    {
        return match($this->note_type) {
            'general' => 'blue',
            'important' => 'purple',
            'warning' => 'red',
            'reminder' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Toggle pin status
     */
    public function togglePin()
    {
        $this->update(['is_pinned' => !$this->is_pinned]);
    }

    /**
     * Pin this note
     */
    public function pin()
    {
        $this->update(['is_pinned' => true]);
    }

    /**
     * Unpin this note
     */
    public function unpin()
    {
        $this->update(['is_pinned' => false]);
    }
}
