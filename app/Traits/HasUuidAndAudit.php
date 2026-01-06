<?php

namespace App\Traits;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

trait HasUuidAndAudit
{
    /**
     * Store original values for audit
     */
    protected $auditOriginal = [];

    /**
     * Boot the trait.
     */
    protected static function bootHasUuidAndAudit()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::created(function ($model) {
            // Create audit log for creation
            if (class_exists(AuditLog::class) && get_class($model) !== AuditLog::class) {
                AuditLog::log(
                    $model->getTable(),
                    $model->getKey(),
                    'create',
                    null,
                    $model->toArray()
                );
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
            
            // Store original values for audit
            $model->auditOriginal = $model->getOriginal();
        });

        static::updated(function ($model) {
            // Create audit log for update
            if (class_exists(AuditLog::class) && get_class($model) !== AuditLog::class) {
                AuditLog::log(
                    $model->getTable(),
                    $model->getKey(),
                    'update',
                    $model->auditOriginal ?? [],
                    $model->toArray()
                );
            }
        });

        static::deleted(function ($model) {
            // Create audit log for deletion
            if (class_exists(AuditLog::class) && get_class($model) !== AuditLog::class) {
                AuditLog::log(
                    $model->getTable(),
                    $model->getKey(),
                    'delete',
                    $model->toArray(),
                    null
                );
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Get the fillable attributes for audit fields.
     */
    public function getAuditFillable()
    {
        return ['created_by', 'updated_by'];
    }

    /**
     * Relationship to the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to the user who last updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
