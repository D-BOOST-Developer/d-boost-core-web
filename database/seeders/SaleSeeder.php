<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample sales transactions for testing and development.
     */
    public function run(): void
    {
        $this->command->info('Seeding sales...');

        $admin = User::where('email', 'admin@sdaumkm.com')->first();
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Customers or products not found. Please run CustomerSeeder and ProductSeeder first.');
            return;
        }

        // Generate sales for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $saleDate = Carbon::now()->subDays($i);
            
            // Generate 2-5 sales per day
            $salesCount = rand(2, 5);
            
            for ($j = 0; $j < $salesCount; $j++) {
                $customer = $customers->random();
                
                $sale = Sale::create([
                    'customer_id' => $customer->id,
                    'sale_date' => $saleDate->format('Y-m-d'),
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                    'payment_method' => collect(['cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'qris'])->random(),
                    'status' => 'completed',
                    'notes' => 'Demo sale transaction',
                    'created_by' => $admin->id,
                ]);

                // Add 1-4 products to each sale
                $productCount = rand(1, min(4, $products->count()));
                $selectedProducts = $products->random($productCount);
                $subtotal = 0;
                $totalDiscount = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, min(3, $product->stock_quantity));
                    $unitPrice = $product->price;
                    $discountPercentage = rand(0, 1) ? rand(0, 10) : 0; // 0-10% discount
                    $discountAmount = ($unitPrice * $quantity) * ($discountPercentage / 100);
                    $lineTotal = ($unitPrice * $quantity) - $discountAmount;

                    SalesDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_percentage' => $discountPercentage,
                        'discount_amount' => $discountAmount,
                        'line_total' => $lineTotal,
                    ]);

                    $subtotal += $lineTotal;
                    $totalDiscount += $discountAmount;

                    // Update product stock
                    $product->decrement('stock_quantity', $quantity);
                }

                // Update sale totals
                $taxAmount = $subtotal * 0.1; // 10% tax
                $totalAmount = $subtotal + $taxAmount;

                $sale->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $totalDiscount,
                    'total_amount' => $totalAmount,
                ]);

                // Update customer statistics
                $customer->increment('total_spent', $totalAmount);
                $customer->increment('purchase_frequency');
                $customer->update(['last_purchase_date' => $saleDate->format('Y-m-d')]);
            }
        }

        $this->command->info('✓ Sales seeded successfully');
    }
}
