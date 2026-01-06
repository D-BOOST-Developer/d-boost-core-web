<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
        'is_system',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'permissions' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($role) {
            if (empty($role->id)) {
                $role->id = (string) Str::uuid();
            }
            
            if (Auth::check()) {
                $role->created_by = Auth::id();
                $role->updated_by = Auth::id();
            }
        });

        static::updating(function ($role) {
            if (Auth::check()) {
                $role->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get users with this role
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withPivot(['is_active', 'assigned_at', 'expires_at', 'assigned_by'])
                    ->withTimestamps();
    }

    /**
     * Get user role relationships
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Get menus accessible by this role
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menus')
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
     * Get the user who created this role
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this role
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if role is system role
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Get role permissions
     */
    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * Accessor to ensure permissions is always an array
     */
    public function getPermissionsAttribute($value)
    {
        // If it's already an array, return it
        if (is_array($value)) {
            return $value;
        }
        
        // If it's a string (JSON), decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        // If null or other type, return empty array
        return [];
    }


    /**
     * Check if role has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * Add permission to role
     */
    public function addPermission(string $permission): self
    {
        $permissions = $this->getPermissions();
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
        }
        
        return $this;
    }

    /**
     * Remove permission from role
     */
    public function removePermission(string $permission): self
    {
        $permissions = $this->getPermissions();
        $key = array_search($permission, $permissions);
        
        if ($key !== false) {
            unset($permissions[$key]);
            $this->permissions = array_values($permissions);
        }
        
        return $this;
    }

    /**
     * Get accessible menus for this role with user context
     */
    public function getAccessibleMenus($userId = null)
    {
        $query = $this->menus()
                     ->where('menus.is_active', true)
                     ->where('role_menus.is_active', true)
                     ->with(['module' => function($q) {
                         $q->where('is_active', true)->orderBy('sort_order');
                     }])
                     ->orderBy('menus.sort_order');

        return $query->get()->groupBy('module.name');
    }

    /**
     * Check if role can access specific menu
     */
    public function canAccessMenu($menuId, $permission = 'read'): bool
    {
        $roleMenu = $this->roleMenus()
                        ->where('menu_id', $menuId)
                        ->where('is_active', true)
                        ->first();

        if (!$roleMenu) {
            return false;
        }

        $permissions = $roleMenu->permissions ?? [];
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }
}
