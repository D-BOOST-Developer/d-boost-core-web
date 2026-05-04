<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder for production deploys.
 *
 * Seeds only master/reference data + 1 default admin.
 * Customer-facing data (customers, sales, products, etc.) is intentionally
 * excluded — production starts with empty user-generated data.
 *
 * Run with:
 *   php artisan db:seed --class=ProductionSeeder --force
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('Running Production Seeder');
        $this->command->info('========================================');

        $this->call([
            UmkmCategorySeeder::class,
            ModularSystemSeeder::class,
            AdminSeeder::class,
        ]);

        $this->command->info('========================================');
        $this->command->info('✅ Production seeding complete');
        $this->command->info('========================================');
    }
}
