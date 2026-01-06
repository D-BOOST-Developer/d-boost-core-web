<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample products for testing and development.
     */
    public function run(): void
    {
        $this->command->info('Seeding products...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'Kopi Arabica Premium',
                'description' => 'Kopi arabica premium dengan cita rasa yang khas dan aroma yang harum',
                'sku' => 'KOPI-001',
                'price' => 85000,
                'cost' => 55000,
                'stock_quantity' => 150,
                'min_stock_level' => 20,
                'category' => 'Beverages',
                'is_active' => true,
            ],
            [
                'name' => 'Teh Hijau Organik',
                'description' => 'Teh hijau organik berkualitas tinggi tanpa pestisida',
                'sku' => 'TEH-001',
                'price' => 45000,
                'cost' => 28000,
                'stock_quantity' => 200,
                'min_stock_level' => 30,
                'category' => 'Beverages',
                'is_active' => true,
            ],
            [
                'name' => 'Keripik Singkong Pedas',
                'description' => 'Keripik singkong dengan bumbu pedas yang gurih dan renyah',
                'sku' => 'KERIPIK-001',
                'price' => 25000,
                'cost' => 15000,
                'stock_quantity' => 300,
                'min_stock_level' => 50,
                'category' => 'Snacks',
                'is_active' => true,
            ],
            [
                'name' => 'Madu Hutan Asli',
                'description' => 'Madu hutan asli 100% murni tanpa campuran gula',
                'sku' => 'MADU-001',
                'price' => 120000,
                'cost' => 80000,
                'stock_quantity' => 80,
                'min_stock_level' => 15,
                'category' => 'Health',
                'is_active' => true,
            ],
            [
                'name' => 'Batik Tulis Jogja',
                'description' => 'Batik tulis asli Jogja dengan motif tradisional',
                'sku' => 'BATIK-001',
                'price' => 350000,
                'cost' => 220000,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'category' => 'Fashion',
                'is_active' => true,
            ],
            [
                'name' => 'Tas Rajut Handmade',
                'description' => 'Tas rajut buatan tangan dengan kualitas premium',
                'sku' => 'TAS-001',
                'price' => 180000,
                'cost' => 110000,
                'stock_quantity' => 40,
                'min_stock_level' => 8,
                'category' => 'Fashion',
                'is_active' => true,
            ],
            [
                'name' => 'Sambal Pecel Madiun',
                'description' => 'Sambal pecel khas Madiun dengan rasa autentik',
                'sku' => 'SAMBAL-001',
                'price' => 35000,
                'cost' => 22000,
                'stock_quantity' => 120,
                'min_stock_level' => 25,
                'category' => 'Food',
                'is_active' => true,
            ],
            [
                'name' => 'Kerajinan Bambu Miniatur',
                'description' => 'Miniatur rumah adat dari bambu buatan pengrajin lokal',
                'sku' => 'BAMBU-001',
                'price' => 75000,
                'cost' => 45000,
                'stock_quantity' => 60,
                'min_stock_level' => 10,
                'category' => 'Handicraft',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, [
                    'created_by' => $admin->id,
                ])
            );
        }

        $this->command->info('✓ Products seeded successfully');
    }
}
