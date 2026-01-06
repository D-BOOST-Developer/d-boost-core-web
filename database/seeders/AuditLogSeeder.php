<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample audit logs for testing and development.
     */
    public function run(): void
    {
        $this->command->info('Seeding audit logs...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        $owner = User::where('email', 'owner@sdaumkm.com')->first();
        $staff = User::where('email', 'staff1@sdaumkm.com')->first();

        if (!$admin || !$owner || !$staff) {
            $this->command->warn('Users not found. Please run UserSeeder first.');
            return;
        }

        $product = Product::first();
        $customer = Customer::first();

        if (!$product || !$customer) {
            $this->command->warn('Products or customers not found. Please run ProductSeeder and CustomerSeeder first.');
            return;
        }

        $auditLogs = [
            [
                'user_id' => $admin->id,
                'table_name' => 'products',
                'record_id' => $product->id,
                'action' => 'create',
                'old_values' => null,
                'new_values' => json_encode([
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'cost' => $product->cost,
                    'stock_quantity' => $product->stock_quantity,
                    'is_active' => $product->is_active
                ]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'performed_at' => now()->subDays(5),
            ],
            [
                'user_id' => $owner->id,
                'table_name' => 'products',
                'record_id' => $product->id,
                'action' => 'update',
                'old_values' => json_encode([
                    'price' => $product->price,
                    'stock_quantity' => $product->stock_quantity
                ]),
                'new_values' => json_encode([
                    'price' => $product->price + 5000,
                    'stock_quantity' => $product->stock_quantity - 5
                ]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'performed_at' => now()->subDays(3),
            ],
            [
                'user_id' => $staff->id,
                'table_name' => 'customers',
                'record_id' => $customer->id,
                'action' => 'create',
                'old_values' => null,
                'new_values' => json_encode([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'city' => $customer->city,
                    'is_active' => $customer->is_active
                ]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'performed_at' => now()->subDays(2),
            ],
            [
                'user_id' => $admin->id,
                'table_name' => 'users',
                'record_id' => $staff->id,
                'action' => 'update',
                'old_values' => json_encode([
                    'is_active' => true,
                ]),
                'new_values' => json_encode([
                    'is_active' => false,
                ]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'performed_at' => now()->subDays(1),
            ],
        ];

        foreach ($auditLogs as $log) {
            AuditLog::create($log);
        }

        $this->command->info('✓ Audit logs seeded successfully');
    }
}
