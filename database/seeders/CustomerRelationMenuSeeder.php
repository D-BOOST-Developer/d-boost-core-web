<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Module;
use App\Models\RoleMenu;
use App\Models\Role;
use Illuminate\Support\Str;

class CustomerRelationMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Customer module
        $customerModule = Module::where('name', 'customer')->first();
        
        if (!$customerModule) {
            $this->command->error('Customer module not found. Please run ModularSystemSeeder first.');
            return;
        }

        // Get all roles
        $roles = Role::all();

        // Create Interactions Menu
        $interactions = Menu::updateOrCreate(
            ['name' => 'customer_interactions'],
            [
                'id' => Str::uuid(),
                'module_id' => $customerModule->id,
                'display_name' => 'Interactions',
                'description' => 'Customer interactions and communications',
                'route' => 'customers.interactions.index',
                'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ]
        );

        // Create Segments Menu
        $segments = Menu::updateOrCreate(
            ['name' => 'customer_segments'],
            [
                'id' => Str::uuid(),
                'module_id' => $customerModule->id,
                'display_name' => 'Segments',
                'description' => 'Customer segmentation and targeting',
                'route' => 'customers.segments.index',
                'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ]
        );

        // Create Loyalty Program Menu
        $loyalty = Menu::updateOrCreate(
            ['name' => 'customer_loyalty'],
            [
                'id' => Str::uuid(),
                'module_id' => $customerModule->id,
                'display_name' => 'Loyalty Program',
                'description' => 'Customer loyalty points and rewards',
                'route' => 'customers.loyalty.index',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ]
        );

        // Create Feedback Menu
        $feedback = Menu::updateOrCreate(
            ['name' => 'customer_feedback'],
            [
                'id' => Str::uuid(),
                'module_id' => $customerModule->id,
                'display_name' => 'Feedback',
                'description' => 'Customer feedback and reviews',
                'route' => 'customers.feedback.index',
                'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
                'sort_order' => 5,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ]
        );

        // Create Analytics Menu
        $analytics = Menu::updateOrCreate(
            ['name' => 'customer_analytics'],
            [
                'id' => Str::uuid(),
                'module_id' => $customerModule->id,
                'display_name' => 'Analytics',
                'description' => 'Customer analytics and insights',
                'route' => 'customers.analytics.dashboard',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'sort_order' => 6,
                'is_active' => true,
                'is_system' => true,
                'permissions' => ['read'],
            ]
        );

        // Assign menus to roles
        $menus = [
            $interactions,
            $segments,
            $loyalty,
            $feedback,
            $analytics,
        ];

        foreach ($roles as $role) {
            // Skip audit_logs and settings roles
            if (in_array($role->name, ['audit_logs', 'settings'])) {
                continue;
            }

            foreach ($menus as $menu) {
                // Determine permissions based on role
                $permissions = ['read'];
                
                if (!in_array($role->name, ['staff'])) {
                    $permissions = ['read', 'create', 'update', 'delete'];
                }

                RoleMenu::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'permissions' => $permissions,
                    ]
                );
            }
        }

        $this->command->info('Customer Relations menus created successfully!');
    }
}
