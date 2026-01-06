<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class UmkmModulePreference extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'module_id',
        'priority_level',
        'is_recommended',
        'reason',
    ];

    protected $casts = [
        'priority_level' => 'integer',
        'is_recommended' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($preference) {
            if (empty($preference->id)) {
                $preference->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns this preference
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module for this preference
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Scope to get preferences ordered by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority_level', 'asc');
    }

    /**
     * Scope to get only recommended modules
     */
    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    /**
     * Scope to get user-selected modules
     */
    public function scopeUserSelected($query)
    {
        return $query->where('reason', 'user_selected');
    }

    /**
     * Scope to get system-suggested modules
     */
    public function scopeSystemSuggested($query)
    {
        return $query->where('reason', 'system_suggestion');
    }

    /**
     * Check if this is a high priority module (top 3)
     */
    public function isHighPriority(): bool
    {
        return $this->priority_level <= 3;
    }

    /**
     * Mark as user selected
     */
    public function markAsUserSelected(): void
    {
        $this->update(['reason' => 'user_selected']);
    }

    /**
     * Mark as system suggested
     */
    public function markAsSystemSuggested(): void
    {
        $this->update(['reason' => 'system_suggestion']);
    }
}
