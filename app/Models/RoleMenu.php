<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RoleMenu extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'menu_id',
        'permissions',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($roleMenu) {
            if (empty($roleMenu->id)) {
                $roleMenu->id = (string) Str::uuid();
            }
            
            if (Auth::check() && !$roleMenu->created_by) {
                $roleMenu->created_by = Auth::id();
            }
        });
    }

    /**
     * Get the role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the user who created this role menu
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get only active role menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get permissions array
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
     * Check if role menu has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->getPermissions();
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Add permission to role menu
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
     * Remove permission from role menu
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
     * Set permissions for role menu
     */
    public function setPermissions(array $permissions): self
    {
        $this->permissions = array_unique($permissions);
        return $this;
    }
}
