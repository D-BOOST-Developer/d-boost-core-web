<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample customers for testing and development.
     */
    public function run(): void
    {
        $this->command->info('Seeding customers...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $customers = [
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'phone' => '081234567890',
                'gender' => 'female',
                'age' => 28,
                'city' => 'Jakarta',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'date_of_birth' => '1995-03-15',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'phone' => '081234567891',
                'gender' => 'male',
                'age' => 35,
                'city' => 'Bandung',
                'address' => 'Jl. Asia Afrika No. 456, Bandung',
                'date_of_birth' => '1988-07-22',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika@email.com',
                'phone' => '081234567892',
                'gender' => 'female',
                'age' => 42,
                'city' => 'Surabaya',
                'address' => 'Jl. Pemuda No. 789, Surabaya',
                'date_of_birth' => '1981-11-08',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@email.com',
                'phone' => '081234567893',
                'gender' => 'male',
                'age' => 29,
                'city' => 'Yogyakarta',
                'address' => 'Jl. Malioboro No. 321, Yogyakarta',
                'date_of_birth' => '1994-05-12',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Rina Wati',
                'email' => 'rina.wati@email.com',
                'phone' => '081234567894',
                'gender' => 'female',
                'age' => 31,
                'city' => 'Medan',
                'address' => 'Jl. Gatot Subroto No. 654, Medan',
                'date_of_birth' => '1992-09-25',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@email.com',
                'phone' => '081234567895',
                'gender' => 'male',
                'age' => 38,
                'city' => 'Solo',
                'address' => 'Jl. Slamet Riyadi No. 987, Solo',
                'date_of_birth' => '1985-12-03',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@email.com',
                'phone' => '081234567896',
                'gender' => 'female',
                'age' => 26,
                'city' => 'Denpasar',
                'address' => 'Jl. Gajah Mada No. 147, Denpasar',
                'date_of_birth' => '1997-04-18',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@email.com',
                'phone' => '081234567897',
                'gender' => 'male',
                'age' => 33,
                'city' => 'Makassar',
                'address' => 'Jl. Ahmad Yani No. 258, Makassar',
                'date_of_birth' => '1990-08-14',
                'total_spent' => 0,
                'purchase_frequency' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['email' => $customerData['email']],
                array_merge($customerData, [
                    'created_by' => $admin->id,
                ])
            );
        }

        $this->command->info('✓ Customers seeded successfully');
    }
}
