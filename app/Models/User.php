<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
            
            if (Auth::check()) {
                $user->created_by = Auth::id();
                $user->updated_by = Auth::id();
            }
        });

        static::updating(function ($user) {
            if (Auth::check()) {
                $user->updated_by = Auth::id();
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
     * Legacy role methods - moved to new modular system below
     */

    /**
     * Get all products created by this user
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    /**
     * Get all customers created by this user
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    /**
     * Get all sales created by this user
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    /**
     * Get all expenses created by this user
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    /**
     * Get all marketplace links for this user (Digital Storefront)
     */
    public function marketplaceLinks()
    {
        return $this->hasMany(MarketplaceLink::class);
    }

    /**
     * Get storefront configuration for this user (Digital Storefront)
     */
    public function storefrontConfig()
    {
        return $this->hasOne(StorefrontConfig::class);
    }

    /**
     * Get user's business profile (UMKM Personalization)
     */
    public function businessProfile()
    {
        return $this->hasOne(UserBusinessProfile::class);
    }

    /**
     * Get user's module preferences (UMKM Personalization)
     */
    public function modulePreferences()
    {
        return $this->hasMany(UmkmModulePreference::class);
    }

    /**
     * Check if user has completed onboarding
     */
    public function hasCompletedOnboarding(): bool
    {
        return $this->businessProfile?->onboarding_completed ?? false;
    }

    /**
     * Get personalized modules ordered by user preferences
     */
    public function getPersonalizedModules()
    {
        $userModuleIds = $this->modulePreferences()
            ->byPriority()
            ->pluck('module_id')
            ->toArray();

        if (empty($userModuleIds)) {
            // No preferences yet, return all accessible modules
            return Module::active()->ordered()->get();
        }

        // Get modules with user's priority order
        $modules = Module::active()->get();
        
        return $modules->sortBy(function ($module) use ($userModuleIds) {
            $index = array_search($module->id, $userModuleIds);
            return $index !== false ? $index : 999;
        })->values();
    }


    /**
     * Get user roles (new modular system)
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Get roles through user roles (new modular system)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot(['is_active', 'assigned_at', 'expires_at', 'assigned_by'])
                    ->withTimestamps();
    }

    /**
     * Get active roles for this user
     */
    public function activeRoles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->wherePivot('is_active', true)
                    ->where(function($query) {
                        $query->whereNull('user_roles.expires_at')
                              ->orWhere('user_roles.expires_at', '>', now());
                    })
                    ->withPivot(['is_active', 'assigned_at', 'expires_at', 'assigned_by'])
                    ->withTimestamps();
    }

    /**
     * Check if user has specific role (new modular system)
     */
    public function hasRole(string $roleName): bool
    {
        return $this->activeRoles()->where('roles.name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->activeRoles()->whereIn('roles.name', $roleNames)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roleNames): bool
    {
        $userRoles = $this->activeRoles()->pluck('roles.name')->toArray();
        return count(array_intersect($roleNames, $userRoles)) === count($roleNames);
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $roleId, $expiresAt = null): bool
    {
        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        // Check if user already has this role
        $existingUserRole = $this->userRoles()
                                ->where('role_id', $roleId)
                                ->first();

        if ($existingUserRole) {
            // Update existing role
            $existingUserRole->update([
                'is_active' => true,
                'expires_at' => $expiresAt,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
        } else {
            // Create new user role
            UserRole::create([
                'user_id' => $this->id,
                'role_id' => $roleId,
                'is_active' => true,
                'expires_at' => $expiresAt,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Remove role from user
     */
    public function removeRole(string $roleId): bool
    {
        return $this->userRoles()
                   ->where('role_id', $roleId)
                   ->delete() > 0;
    }

    /**
     * Get accessible menus for this user based on their roles
     */
    public function getAccessibleMenus()
    {
        $roleIds = $this->activeRoles()->pluck('roles.id');
        
        if ($roleIds->isEmpty()) {
            return collect();
        }

        return Menu::whereHas('roleMenus', function($query) use ($roleIds) {
                    $query->whereIn('role_id', $roleIds)
                          ->where('is_active', true);
                })
                ->where('is_active', true)
                ->with(['module' => function($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get()
                ->groupBy('module.name');
    }

    /**
     * Check if user can access specific menu
     */
    public function canAccessMenu($menuId, $permission = 'read'): bool
    {
        $roleIds = $this->activeRoles()->pluck('roles.id');
        
        if ($roleIds->isEmpty()) {
            return false;
        }

        return RoleMenu::whereIn('role_id', $roleIds)
                      ->where('menu_id', $menuId)
                      ->where('is_active', true)
                      ->where(function($query) use ($permission) {
                          $query->whereJsonContains('permissions', $permission)
                                ->orWhereJsonContains('permissions', '*');
                      })
                      ->exists();
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $roles = $this->activeRoles()->get();
        
        foreach ($roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get all permissions for this user
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        $roles = $this->activeRoles()->get();
        
        foreach ($roles as $role) {
            $permissions = array_merge($permissions, $role->getPermissions());
        }
        
        return array_unique($permissions);
    }

    /**
     * Legacy role methods for backward compatibility
     */
    public function isAdminLegacy(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwnerLegacy(): bool
    {
        return $this->role === 'owner';
    }

    public function isStaffLegacy(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * New modular role methods (updated to avoid redeclaration)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->isAdminLegacy();
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner') || $this->isOwnerLegacy();
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff') || $this->isStaffLegacy();
    }

    /**
     * Check if user is super admin (system admin)
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Get user's primary role (for display purposes)
     */
    public function getPrimaryRole(): ?Role
    {
        return $this->activeRoles()->first();
    }

    /**
     * Get user's primary role name
     */
    public function getPrimaryRoleName(): string
    {
        $primaryRole = $this->getPrimaryRole();
        
        if ($primaryRole) {
            return $primaryRole->display_name;
        }
        
        // Fallback to legacy role
        return ucfirst($this->role ?? 'user');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
