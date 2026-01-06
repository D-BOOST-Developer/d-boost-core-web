<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'json',
            'new_values' => 'json',
            'performed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($auditLog) {
            if (empty($auditLog->id)) {
                $auditLog->id = (string) Str::uuid();
            }
            
            if (empty($auditLog->performed_at)) {
                $auditLog->performed_at = now();
            }
            
            if (empty($auditLog->ip_address) && request()) {
                $auditLog->ip_address = request()->ip();
            }
            
            if (empty($auditLog->user_agent) && request()) {
                $auditLog->user_agent = request()->userAgent();
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
     * Get the user who performed this action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create an audit log entry
     */
    public static function log($tableName, $recordId, $action, $oldValues = null, $newValues = null, $userId = null)
    {
        try {
            return self::create([
                'user_id' => $userId ?? auth()->id(),
                'table_name' => $tableName,
                'record_id' => $recordId,
                'action' => $action,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request() ? request()->ip() : null,
                'user_agent' => request() ? request()->userAgent() : null,
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to prevent breaking the main operation
            \Log::error('Failed to create audit log: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Scope for specific table
     */
    public function scopeForTable($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Scope for specific record
     */
    public function scopeForRecord($query, $recordId)
    {
        return $query->where('record_id', $recordId);
    }

    /**
     * Scope for specific action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted action name
     */
    public function getActionNameAttribute()
    {
        return ucfirst($this->action);
    }

    /**
     * Get changes summary
     */
    public function getChangesSummaryAttribute()
    {
        if ($this->action === 'create') {
            return 'Created new record';
        }
        
        if ($this->action === 'delete') {
            return 'Deleted record';
        }
        
        if ($this->action === 'update' && $this->old_values && $this->new_values) {
            $changes = [];
            foreach ($this->new_values as $field => $newValue) {
                $oldValue = $this->old_values[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = "{$field}: '{$oldValue}' → '{$newValue}'";
                }
            }
            return implode(', ', $changes);
        }
        
        return 'No changes detected';
    }
}
