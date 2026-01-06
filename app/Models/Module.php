<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'is_system',
        'config',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($module) {
            if (empty($module->id)) {
                $module->id = (string) Str::uuid();
            }
            
            if (Auth::check()) {
                $module->created_by = Auth::id();
                $module->updated_by = Auth::id();
            }
        });

        static::updating(function ($module) {
            if (Auth::check()) {
                $module->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get all menus for this module
     */
    public function menus()
    {
        return $this->hasMany(Menu::class)->orderBy('sort_order');
    }

    /**
     * Get active menus for this module
     */
    public function activeMenus()
    {
        return $this->hasMany(Menu::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the user who created this module
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this module
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active modules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get modules ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Check if module is system module
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Get module configuration
     */
    public function getConfig($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config ?? [];
        }

        return data_get($this->config, $key, $default);
    }

    /**
     * Set module configuration
     */
    public function setConfig($key, $value = null)
    {
        $config = $this->config ?? [];
        
        if (is_array($key)) {
            $config = array_merge($config, $key);
        } else {
            data_set($config, $key, $value);
        }
        
        $this->config = $config;
        return $this;
    }
}
