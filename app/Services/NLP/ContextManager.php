<?php

namespace App\Services\NLP;

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class ContextManager
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get comprehensive context data for today
     */
    public function getTodayContext(): array
    {
        $today = date('Y-m-d');

        return [
            'date' => $today,
            'revenue' => $this->getTodayRevenue(),
            'expenses' => $this->getTodayExpenses(),
            'profit' => $this->getTodayProfit(),
            'transactions' => $this->getTodayTransactions(),
            'customers' => $this->getTodayCustomers(),
            'stock_summary' => $this->getStockSummary(),
            'top_products' => $this->getTopProductsToday(),
            'low_stock_products' => $this->getLowStockProducts(),
            'total_products' => $this->getTotalProducts(),
        ];
    }

    /**
     * Get today's revenue from sales table
     */
    public function getTodayRevenue(): array
    {
        $today = date('Y-m-d');
        $revenue = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $transactions = Sale::whereDate('sale_date', $today)->count();
        $avgTransaction = $transactions > 0 ? $revenue / $transactions : 0;

        return [
            'total' => $revenue,
            'transactions' => $transactions,
            'average' => $avgTransaction,
            'formatted_total' => 'Rp ' . number_format($revenue, 0),
            'formatted_average' => 'Rp ' . number_format($avgTransaction, 0),
        ];
    }

    /**
     * Get today's expenses
     */
    public function getTodayExpenses(): array
    {
        $today = date('Y-m-d');
        $expenses = Expense::whereDate('created_at', $today)->sum('amount');
        $count = Expense::whereDate('created_at', $today)->count();

        return [
            'total' => $expenses,
            'count' => $count,
            'formatted_total' => 'Rp ' . number_format($expenses, 0),
        ];
    }

    /**
     * Get today's profit
     */
    public function getTodayProfit(): array
    {
        $revenue = $this->getTodayRevenue()['total'];
        $expenses = $this->getTodayExpenses()['total'];
        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'total' => $profit,
            'margin' => $margin,
            'formatted_total' => 'Rp ' . number_format($profit, 0),
            'formatted_margin' => number_format($margin, 1) . '%',
            'status' => $profit > 0 ? 'Menguntungkan' : 'Rugi',
        ];
    }

    /**
     * Get today's transactions from sales table
     */
    public function getTodayTransactions(): array
    {
        $today = date('Y-m-d');
        $sales = Sale::with('customer')
            ->whereDate('sale_date', $today)
            ->orderBy('sale_date', 'desc')
            ->limit(20)
            ->get();

        return [
            'count' => $sales->count(),
            'data' => $sales->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'customer' => $sale->customer->name ?? 'Guest',
                    'amount' => $sale->total_amount,
                    'formatted_amount' => 'Rp ' . number_format($sale->total_amount, 0),
                    'time' => $sale->sale_date->format('H:i'),
                ];
            })->toArray(),
        ];
    }

    /**
     * Get today's customers from customers table
     */
    public function getTodayCustomers(): array
    {
        $today = date('Y-m-d');
        $newCustomers = Customer::whereDate('created_at', $today)->count();
        $totalCustomers = Customer::count();
        $todayTransactingCustomers = Sale::whereDate('sale_date', $today)
            ->distinct('customer_id')
            ->count();

        return [
            'new_today' => $newCustomers,
            'total' => $totalCustomers,
            'transacting_today' => $todayTransactingCustomers,
        ];
    }

    /**
     * Get stock summary from products table
     */
    public function getStockSummary(): array
    {
        $totalProducts = Product::count();
        $goodStock = Product::where('stock_quantity', '>', 30)->count();
        $limitedStock = Product::whereBetween('stock_quantity', [11, 30])->count();
        $lowStock = Product::where('stock_quantity', '<=', 10)->count();

        return [
            'total' => $totalProducts,
            'good' => $goodStock,
            'limited' => $limitedStock,
            'low' => $lowStock,
        ];
    }

    /**
     * Get top products today from sales table
     */
    public function getTopProductsToday(): array
    {
        $today = date('Y-m-d');
        $topProducts = Sale::whereDate('sale_date', $today)
            ->selectRaw('product_name, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('product_name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        return $topProducts->map(function ($product) {
            return [
                'name' => $product->product_name,
                'count' => $product->count,
                'revenue' => $product->revenue,
                'formatted_revenue' => 'Rp ' . number_format($product->revenue, 0),
            ];
        })->toArray();
    }

    /**
     * Get low stock products from products table
     */
    public function getLowStockProducts(): array
    {
        $products = Product::where('stock_quantity', '<', 10)
            ->select('id', 'name', 'stock_quantity', 'price')
            ->limit(10)
            ->get();

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock_quantity ?? 0,
                'price' => $product->price,
                'formatted_price' => 'Rp ' . number_format($product->price, 0),
            ];
        })->toArray();
    }

    /**
     * Get total products
     */
    public function getTotalProducts(): int
    {
        return Product::count();
    }

    /**
     * Get all products with stock from products table
     */
    public function getAllProducts(): array
    {
        $products = Product::select('id', 'name', 'stock_quantity', 'price', 'category')
            ->orderBy('name')
            ->get();

        return $products->map(function ($product) {
            $stock = $product->stock_quantity ?? 0;
            $status = $stock > 30 ? 'Baik' : 
                     ($stock > 10 ? 'Terbatas' : 'Rendah');
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $stock,
                'price' => $product->price,
                'category' => $product->category,
                'formatted_price' => 'Rp ' . number_format($product->price, 0),
                'status' => $status,
            ];
        })->toArray();
    }

    /**
     * Get all customers
     */
    public function getAllCustomers(): array
    {
        $customers = Customer::select('id', 'name', 'email', 'phone', 'city', 'gender', 'total_spent', 'purchase_frequency', 'last_purchase_date', 'is_active')
            ->orderBy('total_spent', 'desc')
            ->get();

        return $customers->map(function ($customer) {
            $totalPurchase = Sale::where('customer_id', $customer->id)->sum('total_amount');
            $transactions = Sale::where('customer_id', $customer->id)->count();
            $lastPurchase = Sale::where('customer_id', $customer->id)->orderBy('sale_date', 'desc')->first();

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'city' => $customer->city,
                'gender' => $customer->gender,
                'total_purchase' => $totalPurchase,
                'transactions' => $transactions,
                'last_purchase_date' => $lastPurchase ? $lastPurchase->sale_date->format('d/m/Y') : '-',
                'status' => $customer->is_active ? 'Aktif' : 'Tidak Aktif',
                'formatted_purchase' => 'Rp ' . number_format($totalPurchase, 0),
            ];
        })->toArray();
    }

    /**
     * Get all expenses today
     */
    public function getAllExpensesToday(): array
    {
        $today = date('Y-m-d');
        $expenses = Expense::whereDate('expense_date', $today)
            ->select('id', 'amount', 'category', 'title', 'expense_date')
            ->orderBy('expense_date', 'desc')
            ->get();

        return $expenses->map(function ($expense) {
            return [
                'id' => $expense->id,
                'amount' => $expense->amount,
                'category' => $expense->category,
                'description' => $expense->title,
                'formatted_amount' => 'Rp ' . number_format($expense->amount, 0),
                'time' => $expense->expense_date->format('H:i'),
            ];
        })->toArray();
    }

    /**
     * Get context for specific intent
     */
    public function getContextForIntent(string $intent): array
    {
        return match ($intent) {
            'analytics.daily_sales' => [
                'type' => 'revenue',
                'data' => $this->getTodayRevenue(),
            ],
            'finance.check_revenue' => [
                'type' => 'revenue',
                'data' => $this->getTodayRevenue(),
            ],
            'finance.log_expense' => [
                'type' => 'expenses',
                'data' => $this->getAllExpensesToday(),
            ],
            'finance.calculate_profit_loss' => [
                'type' => 'profit',
                'data' => $this->getTodayProfit(),
            ],
            'inventory.check_stock' => [
                'type' => 'stock',
                'data' => $this->getStockSummary(),
            ],
            'inventory.product_list' => [
                'type' => 'products',
                'data' => $this->getAllProducts(),
            ],
            'crm.customer_list' => [
                'type' => 'customers',
                'data' => $this->getAllCustomers(),
            ],
            'analytics.top_products' => [
                'type' => 'top_products',
                'data' => $this->getTopProductsToday(),
            ],
            default => [
                'type' => 'summary',
                'data' => $this->getTodayContext(),
            ],
        };
    }
}
