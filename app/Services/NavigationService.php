<?php

namespace App\Services;

use App\Models\User;
use App\Models\Module;
use App\Models\Menu;
use Illuminate\Support\Collection;

class NavigationService
{
    /**
     * Get navigation menu for authenticated user
     */
    public function getNavigationForUser(User $user): Collection
    {
        // Get user's accessible menus grouped by modules
        $accessibleMenus = $user->getAccessibleMenus();
        
        // Get active modules ordered by sort_order
        $modules = Module::active()->ordered()->get();
        
        $navigation = collect();
        
        foreach ($modules as $module) {
            $moduleMenus = $accessibleMenus->get($module->name, collect());
            
            if ($moduleMenus->isNotEmpty()) {
                $navigation->push([
                    'module' => $module,
                    'menus' => $moduleMenus->sortBy('sort_order')
                ]);
            }
        }
        // Ensure Settings module is visible for super admins even without explicit role menu assignments
        if ($user->isSuperAdmin() && !$navigation->pluck('module.name')->contains('settings')) {
            $settingsModule = Module::active()->where('name', 'settings')->first();
            if ($settingsModule) {
                $settingsMenus = Menu::where('module_id', $settingsModule->id)
                                     ->where('is_active', true)
                                     ->orderBy('sort_order')
                                     ->get();

                if ($settingsMenus->isNotEmpty()) {
                    $navigation->push([
                        'module' => $settingsModule,
                        'menus' => $settingsMenus
                    ]);
                }
            }
        }

        return $navigation->sortBy(fn ($item) => $item['module']->sort_order)->values();
    }

    /**
     * Get breadcrumb for current route
     */
    public function getBreadcrumb(string $routeName): array
    {
        $breadcrumb = [];
        
        // Find menu by route name
        $menu = Menu::where('route', $routeName)->first();
        
        if ($menu) {
            $breadcrumb[] = [
                'title' => $menu->display_name,
                'url' => $menu->getUrl(),
                'active' => true
            ];
            
            // Add parent breadcrumbs
            $parent = $menu->parent;
            while ($parent) {
                array_unshift($breadcrumb, [
                    'title' => $parent->display_name,
                    'url' => $parent->getUrl(),
                    'active' => false
                ]);
                $parent = $parent->parent;
            }
            
            // Add module breadcrumb
            if ($menu->module) {
                array_unshift($breadcrumb, [
                    'title' => $menu->module->display_name,
                    'url' => '#',
                    'active' => false
                ]);
            }
        }
        
        return $breadcrumb;
    }

    /**
     * Check if user can access specific route
     */
    public function canAccessRoute(User $user, string $routeName): bool
    {
        $menu = Menu::where('route', $routeName)->first();
        
        if (!$menu) {
            return true; // Allow access to routes not defined in menu system
        }
        
        return $user->canAccessMenu($menu->id, 'read');
    }

    /**
     * Get active menu item for current route
     */
    public function getActiveMenu(string $routeName): ?Menu
    {
        // First, try exact match or route pattern match
        $menu = Menu::where('route', $routeName)
                   ->orWhere('route', 'LIKE', $routeName . '.%')
                   ->first();
        
        if ($menu) {
            return $menu;
        }
        
        // If not found and route is a resource action (create, edit, show), 
        // try to find the corresponding index menu
        $resourceActions = ['create', 'edit', 'show', 'store', 'update', 'destroy'];
        
        foreach ($resourceActions as $action) {
            $actionPattern = '.' . $action;
            if (substr($routeName, -strlen($actionPattern)) === $actionPattern) {
                // Extract base route (e.g., 'products' from 'products.create' or 'settings.products' from 'settings.products.create')
                $baseRoute = substr($routeName, 0, -strlen($actionPattern));
                
                // Try to find menu with base route + .index (e.g., 'products.index' or 'settings.products.index')
                $indexMenu = Menu::where('route', $baseRoute . '.index')->first();
                
                if ($indexMenu) {
                    return $indexMenu;
                }
                
                // Also try with just the base route name
                $baseMenu = Menu::where('route', $baseRoute)->first();
                
                if ($baseMenu) {
                    return $baseMenu;
                }
            }
        }
        
        return null;
    }

    /**
     * Get current module based on route
     */
    public function getCurrentModule(string $routeName): ?Module
    {
        $menu = $this->getActiveMenu($routeName);
        return $menu ? $menu->module : null;
    }

    /**
     * Get navigation for specific module
     */
    public function getModuleNavigation(User $user, Module $module): Collection
    {
        $roleIds = $user->activeRoles()->pluck('roles.id');
        
        if ($roleIds->isEmpty()) {
            return collect();
        }

        return Menu::whereHas('roleMenus', function($query) use ($roleIds) {
                    $query->whereIn('role_id', $roleIds)
                          ->where('is_active', true);
                })
                ->where('module_id', $module->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
    }

    /**
     * Check if current route is modular dashboard
     */
    public function isModularDashboard(string $routeName): bool
    {
        return $routeName === 'dashboard';
    }

    /**
     * Generate navigation tree structure
     */
    public function getNavigationTree(User $user): array
    {
        $navigation = $this->getNavigationForUser($user);
        $tree = [];
        
        foreach ($navigation as $moduleData) {
            $module = $moduleData['module'];
            $menus = $moduleData['menus'];
            
            $moduleItem = [
                'id' => $module->id,
                'name' => $module->name,
                'display_name' => $module->display_name,
                'icon' => $module->icon,
                'color' => $module->color,
                'sort_order' => $module->sort_order,
                'children' => []
            ];
            
            // Build menu tree for this module
            $rootMenus = $menus->whereNull('parent_id');
            
            foreach ($rootMenus as $menu) {
                $menuItem = $this->buildMenuTree($menu, $menus);
                $moduleItem['children'][] = $menuItem;
            }
            
            $tree[] = $moduleItem;
        }
        
        return $tree;
    }

    /**
     * Build menu tree recursively
     */
    private function buildMenuTree(Menu $menu, Collection $allMenus): array
    {
        $menuItem = [
            'id' => $menu->id,
            'name' => $menu->name,
            'display_name' => $menu->display_name,
            'route' => $menu->route,
            'url' => $menu->getUrl(),
            'icon' => $menu->icon,
            'sort_order' => $menu->sort_order,
            'is_active_route' => $menu->isActiveRoute(),
            'children' => []
        ];
        
        // Get child menus
        $childMenus = $allMenus->where('parent_id', $menu->id)->sortBy('sort_order');
        
        foreach ($childMenus as $childMenu) {
            $menuItem['children'][] = $this->buildMenuTree($childMenu, $allMenus);
        }
        
        return $menuItem;
    }

    /**
     * Get quick access menus (frequently used)
     */
    public function getQuickAccessMenus(User $user, int $limit = 5): Collection
    {
        $accessibleMenus = $user->getAccessibleMenus();
        
        // Flatten all menus and get most commonly used ones
        $allMenus = $accessibleMenus->flatten();
        
        // For now, return first few menus. In future, this could be based on usage analytics
        return $allMenus->take($limit);
    }

    /**
     * Search menus by name
     */
    public function searchMenus(User $user, string $query): Collection
    {
        $accessibleMenus = $user->getAccessibleMenus();
        $allMenus = $accessibleMenus->flatten();
        
        return $allMenus->filter(function ($menu) use ($query) {
            return stripos($menu->display_name, $query) !== false ||
                   stripos($menu->description, $query) !== false;
        });
    }
}
