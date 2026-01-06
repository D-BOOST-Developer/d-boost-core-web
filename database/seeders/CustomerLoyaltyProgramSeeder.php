<?php

namespace Database\Seeders;

use App\Models\CustomerLoyaltyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerLoyaltyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates default loyalty program for customer rewards.
     */
    public function run(): void
    {
        $this->command->info('Seeding customer loyalty program...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        CustomerLoyaltyProgram::updateOrCreate(
            ['name' => 'Program Loyalitas Utama'],
            [
                'description' => 'Program loyalitas default dengan sistem tier dan poin',
                'points_per_idr' => 0.001, // 1 poin per 1000 IDR
                'tier_thresholds' => [
                    'bronze' => 0,
                    'silver' => 1000,
                    'gold' => 5000,
                    'platinum' => 20000,
                ],
                'tier_benefits' => [
                    'bronze' => [
                        'discount' => 0,
                        'description' => 'Member baru',
                    ],
                    'silver' => [
                        'discount' => 5,
                        'description' => 'Diskon 5%, akses promo eksklusif',
                    ],
                    'gold' => [
                        'discount' => 10,
                        'description' => 'Diskon 10%, gratis ongkir setiap bulan',
                    ],
                    'platinum' => [
                        'discount' => 15,
                        'description' => 'Diskon 15%, prioritas customer service, hadiah ulang tahun',
                    ],
                ],
                'rewards' => [
                    [
                        'name' => 'Diskon Rp 50.000',
                        'points_required' => 500,
                        'description' => 'Voucher diskon Rp 50.000',
                    ],
                    [
                        'name' => 'Diskon Rp 100.000',
                        'points_required' => 1000,
                        'description' => 'Voucher diskon Rp 100.000',
                    ],
                    [
                        'name' => 'Gratis Ongkir',
                        'points_required' => 300,
                        'description' => 'Gratis ongkir untuk pembelian berikutnya',
                    ],
                    [
                        'name' => 'Produk Gratis',
                        'points_required' => 2000,
                        'description' => 'Pilih 1 produk gratis (max Rp 200.000)',
                    ],
                ],
                'is_active' => true,
                'start_date' => now(),
                'end_date' => null,
                'points_expiry_days' => 365, // Poin berlaku 1 tahun
                'created_by' => $admin->id,
            ]
        );

        $this->command->info('✓ Customer loyalty program seeded successfully');
    }
}
