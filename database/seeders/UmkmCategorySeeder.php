<?php

namespace Database\Seeders;

use App\Models\UmkmCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UmkmCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates UMKM categories for business classification.
     */
    public function run(): void
    {
        $this->command->info('Seeding UMKM categories...');

        $categories = [
            [
                'code' => 'STARTING',
                'name' => 'Starting Business',
                'description' => 'New or early-stage businesses that are just beginning their journey. Focus on basic operational setup and foundational processes.',
                'min_business_age' => 0,
                'max_business_age' => 12,
                'min_revenue' => 0,
                'max_revenue' => 10000000, // 10 million IDR
                'is_active' => true,
            ],
            [
                'code' => 'STAGNANT',
                'name' => 'Stagnant Business',
                'description' => 'Established businesses with low growth or facing challenges. Focus on optimization, problem diagnosis, and efficiency improvements.',
                'min_business_age' => 12,
                'max_business_age' => 36,
                'min_revenue' => 10000000, // 10 million IDR
                'max_revenue' => 50000000, // 50 million IDR
                'is_active' => true,
            ],
            [
                'code' => 'GROWING',
                'name' => 'Growing Business',
                'description' => 'Well-managed and growing businesses ready to scale. Focus on analytics, advanced reporting, and strategic planning.',
                'min_business_age' => 36,
                'max_business_age' => null, // No upper limit
                'min_revenue' => 50000000, // 50 million IDR
                'max_revenue' => null, // No upper limit
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            UmkmCategory::updateOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );
        }

        $this->command->info('✓ UMKM categories seeded successfully');
    }
}
