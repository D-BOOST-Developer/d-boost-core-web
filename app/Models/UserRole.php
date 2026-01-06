<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserRole extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'is_active',
        'assigned_at',
        'expires_at',
        'assigned_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($userRole) {
            if (empty($userRole->id)) {
                $userRole->id = (string) Str::uuid();
            }
            
            if (Auth::check() && !$userRole->assigned_by) {
                $userRole->assigned_by = Auth::id();
            }
            
            if (!$userRole->assigned_at) {
                $userRole->assigned_at = now();
            }
        });
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who assigned this role
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope to get only active user roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get non-expired user roles
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Check if user role is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if user role is currently valid
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
