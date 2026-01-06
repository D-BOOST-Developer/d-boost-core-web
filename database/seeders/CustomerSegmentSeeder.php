<?php

namespace Database\Seeders;

use App\Models\CustomerSegment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates default customer segments for CRM functionality.
     */
    public function run(): void
    {
        $this->command->info('Seeding customer segments...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $segments = [
            [
                'name' => 'VIP Customers',
                'description' => 'Pelanggan dengan total pembelian >= Rp 5.000.000',
                'segment_type' => 'auto',
                'criteria' => [
                    'total_spent_min' => 5000000,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'High Value',
                'description' => 'Pelanggan dengan total pembelian Rp 2.000.000 - Rp 4.999.999',
                'segment_type' => 'auto',
                'criteria' => [
                    'total_spent_min' => 2000000,
                    'total_spent_max' => 4999999,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'At Risk',
                'description' => 'Pelanggan yang tidak melakukan pembelian dalam 90 hari terakhir',
                'segment_type' => 'auto',
                'criteria' => [
                    'last_purchase_days' => -90,
                    'purchase_frequency_min' => 1,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'New Customers',
                'description' => 'Pelanggan baru yang terdaftar dalam 30 hari terakhir',
                'segment_type' => 'manual',
                'criteria' => [],
                'is_active' => true,
            ],
            [
                'name' => 'Frequent Buyers',
                'description' => 'Pelanggan dengan frekuensi pembelian >= 5 kali',
                'segment_type' => 'auto',
                'criteria' => [
                    'purchase_frequency_min' => 5,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Jakarta Customers',
                'description' => 'Pelanggan dari Jakarta',
                'segment_type' => 'auto',
                'criteria' => [
                    'city' => ['Jakarta'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($segments as $segmentData) {
            CustomerSegment::updateOrCreate(
                ['name' => $segmentData['name']],
                array_merge($segmentData, [
                    'created_by' => $admin->id,
                ])
            );
        }

        $this->command->info('✓ Customer segments seeded successfully');
    }
}
