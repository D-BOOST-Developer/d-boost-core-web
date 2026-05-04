<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_DEFAULT_EMAIL', 'admin@dboost.id');
        $password = env('ADMIN_DEFAULT_PASSWORD');
        $name = env('ADMIN_DEFAULT_NAME', 'D-BOOST Administrator');

        if (empty($password)) {
            $this->command->error('ADMIN_DEFAULT_PASSWORD not set in .env — skipping AdminSeeder.');
            $this->command->warn('Set ADMIN_DEFAULT_PASSWORD before running this seeder.');
            return;
        }

        $this->command->info("Seeding default admin: {$email}");

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
                'role' => 'super_admin',
            ]
        );

        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            $this->command->error('Role "super_admin" not found. Run ModularSystemSeeder first.');
            return;
        }

        UserRole::updateOrCreate(
            ['user_id' => $admin->id, 'role_id' => $superAdminRole->id],
            [
                'is_active' => true,
                'assigned_at' => now(),
            ]
        );

        $this->command->info("✓ Default admin ready (email: {$email})");
    }
}
