<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample users for the system.
     * Note: User roles are assigned via UserRole model in ModularSystemSeeder
     */
    public function run(): void
    {
        $this->command->info('Seeding users...');

        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'admin@sdaumkm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Business Owner',
                'email' => 'owner@sdaumkm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Staff Member 1',
                'email' => 'staff1@sdaumkm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Staff Member 2',
                'email' => 'staff2@sdaumkm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✓ Users seeded successfully');
    }
}
