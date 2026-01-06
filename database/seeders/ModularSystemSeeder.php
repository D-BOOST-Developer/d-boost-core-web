<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Menu;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\RoleMenu;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ModularSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Modular System Seeder...');

        // Create Modules
        $modules = $this->createModules();
        $this->command->info('✓ Modules created');
        
        // Create Menus
        $menus = $this->createMenus($modules);
        $this->command->info('✓ Menus created');
        
        // Create Roles
        $roles = $this->createRoles();
        $this->command->info('✓ Roles created');
        
        // Assign Menus to Roles
        $this->assignMenusToRoles($roles, $menus);
        $this->command->info('✓ Menus assigned to roles');
        
        // Migrate existing users to new role system
        $this->migrateExistingUsers($roles);
        $this->command->info('✓ Existing users migrated');

        $this->command->info('Modular System Seeder completed successfully!');
    }

    /**
     * Create all system modules
     */
    private function createModules(): array
    {
        $modules = [
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'description' => 'Main dashboard with analytics and KPIs',
                'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z',
                'color' => '#00FF9C',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'inventory',
                'display_name' => 'Inventory',
                'description' => 'Product and stock management',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'color' => '#3B82F6',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'finance',
                'display_name' => 'Finance',
                'description' => 'Sales and expense management',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                'color' => '#10B981',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'digital_storefront',
                'display_name' => 'Digital Storefront',
                'description' => 'Online storefront and sales channels',
                'icon' => 'M4 6h16M4 12h10M4 18h8',
                'color' => '#0EA5E9',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer Relations',
                'description' => 'Customer relationship management',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                'color' => '#8B5CF6',
                'sort_order' => 5,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'reports',
                'display_name' => 'Report',
                'description' => 'Business reports and analytics',
                'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'color' => '#F59E0B',
                'sort_order' => 6,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'audit_logs',
                'display_name' => 'Audit Logs',
                'description' => 'System audit and activity logs',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'color' => '#EF4444',
                'sort_order' => 7,
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'settings',
                'display_name' => 'Settings',
                'description' => 'System configuration and module management',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'color' => '#6B7280',
                'sort_order' => 8,
                'is_active' => true,
                'is_system' => true,
            ],
        ];

        $moduleInstances = [];
        foreach ($modules as $moduleData) {
            $name = $moduleData['name'];
            unset($moduleData['id']); // Remove ID to avoid conflicts
            
            $moduleInstances[$name] = Module::updateOrCreate(
                ['name' => $name],
                $moduleData
            );
        }

        return $moduleInstances;
    }

    /**
     * Create all system menus
     */
    private function createMenus(array $modules): array
    {
        $menus = [
            // ============================================
            // DASHBOARD MODULE
            // ============================================
            [
                'module_id' => $modules['dashboard']->id,
                'name' => 'dashboard_analytics',
                'display_name' => 'Analytics Dashboard',
                'description' => 'Comprehensive analytics and KPI overview',
                'route' => 'analytics',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read'],
            ],
            
            // ============================================
            // INVENTORY MODULE
            // ============================================
            [
                'module_id' => $modules['inventory']->id,
                'name' => 'inventory_dashboard',
                'display_name' => 'Inventory Dashboard',
                'description' => 'Inventory overview and stock management',
                'route' => 'inventory.dashboard',
                'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read'],
            ],
            [
                'module_id' => $modules['inventory']->id,
                'name' => 'products',
                'display_name' => 'Products',
                'description' => 'Product management',
                'route' => 'inventory.products.index',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['inventory']->id,
                'name' => 'inventory_transactions',
                'display_name' => 'Transactions',
                'description' => 'Inventory transactions and stock movements',
                'route' => 'inventory.transactions.index',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            
            // ============================================
            // FINANCE MODULE
            // ============================================
            [
                'module_id' => $modules['finance']->id,
                'name' => 'finance_dashboard',
                'display_name' => 'Finance Dashboard',
                'description' => 'Finance overview and financial metrics',
                'route' => 'finance.dashboard',
                'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read'],
            ],
            [
                'module_id' => $modules['finance']->id,
                'name' => 'sales',
                'display_name' => 'Sales',
                'description' => 'Sales management',
                'route' => 'finance.sales.index',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['finance']->id,
                'name' => 'expenses',
                'display_name' => 'Expenses',
                'description' => 'Expense management',
                'route' => 'finance.expenses.index',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['finance']->id,
                'name' => 'finance_sales_returns',
                'display_name' => 'Sales Returns',
                'description' => 'Manage sales returns',
                'route' => 'finance.sales-returns.index',
                'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['finance']->id,
                'name' => 'finance_accounts_receivable',
                'display_name' => 'Accounts Receivable',
                'description' => 'Manage accounts receivable',
                'route' => 'finance.accounts-receivable',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'sort_order' => 5,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read'],
            ],
            
            // ============================================
            // CUSTOMER MODULE
            // ============================================
            [
                'module_id' => $modules['customer']->id,
                'name' => 'customers',
                'display_name' => 'Customers',
                'description' => 'Customer management',
                'route' => 'customers.index',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            
            // ============================================
            // DIGITAL STOREFRONT MODULE
            // ============================================
            [
                'module_id' => $modules['digital_storefront']->id,
                'name' => 'storefront',
                'display_name' => 'Storefront',
                'description' => 'Online storefront management',
                'route' => 'storefront.index',
                'icon' => 'M4 6h16M4 12h10M4 18h8',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            
            // ============================================
            // AUDIT LOGS MODULE
            // ============================================
            [
                'module_id' => $modules['audit_logs']->id,
                'name' => 'audit_logs',
                'display_name' => 'Audit Logs',
                'description' => 'System audit logs',
                'route' => 'audit-logs.index',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read'],
            ],
            
            // ============================================
            // SETTINGS MODULE
            // ============================================
            [
                'module_id' => $modules['settings']->id,
                'name' => 'modules_management',
                'display_name' => 'Modules',
                'description' => 'Module management',
                'route' => 'settings.modules.index',
                'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['settings']->id,
                'name' => 'menus_management',
                'display_name' => 'Menus',
                'description' => 'Menu management',
                'route' => 'settings.menus.index',
                'icon' => 'M4 6h16M4 12h16M4 18h16',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['settings']->id,
                'name' => 'roles_management',
                'display_name' => 'Roles',
                'description' => 'Role management',
                'route' => 'settings.roles.index',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'module_id' => $modules['settings']->id,
                'name' => 'users_management',
                'display_name' => 'Users',
                'description' => 'User management',
                'route' => 'settings.users.index',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
        ];

        $menuInstances = [];
        foreach ($menus as $menuData) {
            $name = $menuData['name'];
            $moduleId = $menuData['module_id'];
            
            $menuInstances[$name] = Menu::updateOrCreate(
                ['name' => $name, 'module_id' => $moduleId],
                $menuData
            );
        }

        return $menuInstances;
    }

    /**
     * Create all system roles
     */
    private function createRoles(): array
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access including settings and configuration',
                'permissions' => ['*'],
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrative access to all business functions',
                'permissions' => ['dashboard.read', 'inventory.*', 'finance.*', 'customer.*', 'audit_logs.read'],
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'owner',
                'display_name' => 'Owner',
                'description' => 'Business owner with access to all operational functions',
                'permissions' => ['dashboard.read', 'inventory.*', 'finance.*', 'customer.*'],
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Basic staff access to daily operations',
                'permissions' => ['dashboard.read', 'inventory.read', 'finance.read', 'customer.read'],
                'is_active' => true,
                'is_system' => true,
            ],
            [
                'name' => 'users',
                'display_name' => 'Users',
                'description' => 'User management',
                'permissions' => ['dashboard.read', 'inventory.read', 'finance.read', 'customer.read'],
                'is_active' => true,
                'is_system' => true,
            ],
            
        ];

        $roleInstances = [];
        foreach ($roles as $roleData) {
            $name = $roleData['name'];
            
            $roleInstances[$name] = Role::updateOrCreate(
                ['name' => $name],
                $roleData
            );
        }

        return $roleInstances;
    }

    /**
     * Assign menus to roles with appropriate permissions
     */
    private function assignMenusToRoles(array $roles, array $menus): void
    {
        // Clear existing role menus to avoid duplicates
        DB::table('role_menus')->truncate();
        
        // Define menu permissions by role
        $roleMenuPermissions = [
            'super_admin' => [
                // All menus with full access
                '*' => ['*'],
            ],
            'admin' => [
                // Dashboard
                'dashboard_analytics' => ['read'],
                // Inventory
                'inventory_dashboard' => ['read'],
                'products' => ['read', 'create', 'update', 'delete'],
                'inventory_transactions' => ['read', 'create', 'update', 'delete'],
                // Finance
                'finance_dashboard' => ['read'],
                'sales' => ['read', 'create', 'update', 'delete'],
                'expenses' => ['read', 'create', 'update', 'delete'],
                'finance_sales_returns' => ['read', 'create', 'update', 'delete'],
                'finance_accounts_receivable' => ['read'],
                // Customer
                'customers' => ['read', 'create', 'update', 'delete'],
                // Storefront
                'storefront' => ['read', 'create', 'update', 'delete'],
                // Reports
                'business_reports' => ['read'],
                // Audit Logs
                'audit_logs' => ['read'],
            ],
            'owner' => [
                // Dashboard
                'dashboard_analytics' => ['read'],
                // Inventory
                'inventory_dashboard' => ['read'],
                'products' => ['read', 'create', 'update', 'delete'],
                'inventory_transactions' => ['read', 'create', 'update', 'delete'],
                // Finance
                'finance_dashboard' => ['read'],
                'sales' => ['read', 'create', 'update', 'delete'],
                'expenses' => ['read', 'create', 'update', 'delete'],
                'finance_sales_returns' => ['read', 'create', 'update', 'delete'],
                'finance_accounts_receivable' => ['read'],
                // Customer
                'customers' => ['read', 'create', 'update', 'delete'],
                // Storefront
                'storefront' => ['read', 'create', 'update', 'delete'],
                // Reports
                'business_reports' => ['read'],
            ],
            'staff' => [
                // Dashboard
                'dashboard_analytics' => ['read'],
                // Inventory
                'inventory_dashboard' => ['read'],
                'products' => ['read'],
                'inventory_transactions' => ['read'],
                // Finance
                'finance_dashboard' => ['read'],
                'sales' => ['read'],
                'expenses' => ['read'],
                'finance_sales_returns' => ['read'],
                'finance_accounts_receivable' => ['read'],
                // Customer
                'customers' => ['read'],
                // Storefront
                'storefront' => ['read'],
                // Reports
                'business_reports' => ['read'],
            ],
        ];

        // Assign menus to each role
        foreach ($roles as $roleName => $role) {
            $permissions = $roleMenuPermissions[$roleName] ?? [];
            
            foreach ($menus as $menuName => $menu) {
                $menuPermissions = ['read']; // Default
                
                if ($roleName === 'super_admin') {
                    $menuPermissions = ['*'];
                } elseif (isset($permissions[$menuName])) {
                    $menuPermissions = $permissions[$menuName];
                } elseif (isset($permissions['*'])) {
                    $menuPermissions = $permissions['*'];
                }
                
                RoleMenu::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'permissions' => $menuPermissions,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Migrate existing users to new role system
     */
    private function migrateExistingUsers(array $roles): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Check if user already has a role assigned
            $existingRole = UserRole::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if ($existingRole) {
                continue; // Skip if already has active role
            }
            
            // Try to get role from legacy field if exists
            $roleName = null;
            if (isset($user->role)) {
                $roleName = $user->role;
            } elseif ($user->email === 'admin@example.com' || $user->is_admin ?? false) {
                $roleName = 'super_admin';
            } else {
                $roleName = 'owner'; // Default role
            }
            
            if (isset($roles[$roleName])) {
                UserRole::updateOrCreate(
                    ['user_id' => $user->id, 'role_id' => $roles[$roleName]->id],
                    [
                        'is_active' => true,
                        'assigned_at' => now(),
                    ]
                );
            }
        }
    }
}
