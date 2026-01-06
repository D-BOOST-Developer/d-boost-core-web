<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * Boot the trait
     */
    public static function bootBelongsToUser()
    {
        // Add global scope to filter by user_id
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->getModel()->getTable() . '.user_id', Auth::id());
            }
        });

        // Auto-set user_id when creating
        static::creating(function ($model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
    }

    /**
     * Get the user that owns this model
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope to get all records without user filter (for admin purposes)
     */
    public function scopeWithoutUserScope(Builder $query)
    {
        return $query->withoutGlobalScope('user');
    }

    /**
     * Scope to get records for a specific user
     */
    public function scopeForUser(Builder $query, $userId)
    {
        return $query->withoutGlobalScope('user')->where('user_id', $userId);
    }
}

