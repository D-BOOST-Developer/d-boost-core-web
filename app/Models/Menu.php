<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'module_id',
        'parent_id',
        'name',
        'display_name',
        'description',
        'route',
        'url',
        'icon',
        'sort_order',
        'is_active',
        'is_system',
        'permissions',
        'config',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'permissions' => 'array',
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($menu) {
            if (empty($menu->id)) {
                $menu->id = (string) Str::uuid();
            }
            
            if (Auth::check()) {
                $menu->created_by = Auth::id();
                $menu->updated_by = Auth::id();
            }
        });

        static::updating(function ($menu) {
            if (Auth::check()) {
                $menu->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get the module this menu belongs to
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the parent menu
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get child menus
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get active child menus
     */
    public function activeChildren()
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get roles that have access to this menu
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menus')
                    ->withPivot(['permissions', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get role menu relationships
     */
    public function roleMenus()
    {
        return $this->hasMany(RoleMenu::class);
    }

    /**
     * Get the user who created this menu
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this menu
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get menus ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope to get root menus (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if menu is system menu
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Check if menu has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get menu URL
     */
    public function getUrl(): string
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->route) {
            return route($this->route);
        }

        return '#';
    }

    /**
     * Check if menu is active based on current route
     */
    public function isActiveRoute(): bool
    {
        if ($this->route) {
            return request()->routeIs($this->route . '*');
        }

        return false;
    }

    /**
     * Get menu permissions
     */
    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * Check if menu has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * Get menu configuration
     */
    public function getConfig($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config ?? [];
        }

        return data_get($this->config, $key, $default);
    }

    /**
     * Set menu configuration
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
