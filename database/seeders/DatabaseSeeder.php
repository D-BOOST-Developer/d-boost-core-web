<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Seeders are organized by category and dependency order:
     * 1. System Configuration (categories, users)
     * 2. Core Business Data (products, customers)
     * 3. Transaction Data (sales, expenses)
     * 4. Modular System (modules, menus, roles, permissions)
     * 5. CRM Features (segments, loyalty programs)
     * 6. Additional Menus (CRM menus)
     * 7. Audit Logs (sample audit trail)
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('Starting Database Seeding Process');
        $this->command->info('========================================');
        $this->command->newLine();

        // ============================================
        // 1. SYSTEM CONFIGURATION
        // ============================================
        $this->command->info('📋 Step 1: System Configuration');
        $this->call([
            UmkmCategorySeeder::class,
            UserSeeder::class,
        ]);
        $this->command->newLine();

        // ============================================
        // 2. CORE BUSINESS DATA
        // ============================================
        $this->command->info('📦 Step 2: Core Business Data');
        $this->call([
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);
        $this->command->newLine();

        // ============================================
        // 3. TRANSACTION DATA
        // ============================================
        $this->command->info('💰 Step 3: Transaction Data');
        $this->call([
            SaleSeeder::class,
            ExpenseSeeder::class,
        ]);
        $this->command->newLine();

        // ============================================
        // 4. MODULAR SYSTEM
        // ============================================
        $this->command->info('🔧 Step 4: Modular System (Modules, Menus, Roles)');
        $this->call([
            ModularSystemSeeder::class,
        ]);
        $this->command->newLine();

        // ============================================
        // 5. CRM FEATURES
        // ============================================
        $this->command->info('👥 Step 5: CRM Features');
        $this->call([
            CustomerSegmentSeeder::class,
            CustomerLoyaltyProgramSeeder::class,
        ]);
        $this->command->newLine();

        // ============================================
        // 6. ADDITIONAL MENUS
        // ============================================
        $this->command->info('📋 Step 6: Additional Menus');
        $this->call([
            CustomerRelationMenuSeeder::class,
        ]);
        $this->command->newLine();

        // ============================================
        // 7. AUDIT LOGS
        // ============================================
        $this->command->info('📝 Step 7: Audit Logs');
        $this->call([
            AuditLogSeeder::class,
        ]);
        $this->command->newLine();

        $this->command->info('========================================');
        $this->command->info('✅ All seeders completed successfully!');
        $this->command->info('========================================');
    }
}
