<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample expenses for testing and development.
     */
    public function run(): void
    {
        $this->command->info('Seeding expenses...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $expenseCategories = [
            'office_supplies' => ['amount_range' => [200000, 1000000], 'recurring' => false],
            'marketing' => ['amount_range' => [300000, 2000000], 'recurring' => false],
            'utilities' => ['amount_range' => [500000, 1500000], 'recurring' => true],
            'rent' => ['amount_range' => [2000000, 5000000], 'recurring' => true],
            'salaries' => ['amount_range' => [3000000, 8000000], 'recurring' => true],
            'transportation' => ['amount_range' => [100000, 500000], 'recurring' => false],
            'meals' => ['amount_range' => [50000, 300000], 'recurring' => false],
            'equipment' => ['amount_range' => [1000000, 5000000], 'recurring' => false],
            'maintenance' => ['amount_range' => [150000, 800000], 'recurring' => false],
            'other' => ['amount_range' => [50000, 500000], 'recurring' => false],
        ];

        $expenseDescriptions = [
            'office_supplies' => 'Pembelian alat tulis dan perlengkapan kantor',
            'marketing' => 'Biaya promosi dan iklan di media sosial',
            'utilities' => 'Tagihan listrik dan air bulanan',
            'rent' => 'Pembayaran sewa tempat usaha',
            'salaries' => 'Pembayaran gaji karyawan',
            'transportation' => 'Biaya transportasi dan pengiriman',
            'meals' => 'Biaya makan dan konsumsi',
            'equipment' => 'Pembelian peralatan dan mesin',
            'maintenance' => 'Biaya perbaikan dan maintenance',
            'other' => 'Pengeluaran operasional lainnya',
        ];

        $vendors = [
            'PT Toko Serba Ada',
            'CV Supplier Utama',
            'Toko Grosir Bandung',
            'PT Distributor Jaya',
            'CV Bahan Baku',
        ];

        // Generate expenses for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $expenseDate = Carbon::now()->subDays($i);
            
            // Generate 1-3 expenses per day
            $expenseCount = rand(1, 3);
            
            for ($j = 0; $j < $expenseCount; $j++) {
                $category = collect(array_keys($expenseCategories))->random();
                $categoryData = $expenseCategories[$category];
                
                $amount = rand($categoryData['amount_range'][0], $categoryData['amount_range'][1]);
                $isRecurring = $categoryData['recurring'];
                $description = $expenseDescriptions[$category];
                $title = $description ?: ucfirst(str_replace('_', ' ', $category));
                
                Expense::create([
                    'title' => $title,
                    'description' => $description,
                    'amount' => $amount,
                    'expense_date' => $expenseDate->format('Y-m-d'),
                    'category' => $category,
                    'vendor' => rand(0, 1) ? $vendors[array_rand($vendors)] : null,
                    'payment_method' => collect(['cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet'])->random(),
                    'is_recurring' => $isRecurring,
                    'recurring_period' => $isRecurring ? 'monthly' : null,
                    'created_by' => $admin->id,
                ]);
            }
        }

        $this->command->info('✓ Expenses seeded successfully');
    }
}
