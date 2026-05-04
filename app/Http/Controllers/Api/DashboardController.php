<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard summary data.
     */
    public function summary()
    {
        // 1. Balance Calculation (Simplified as Month Profit for now, or total cashflow)
        // In a real app complexity, this might be from a specific account balance table.
        // Here we'll use (Total Income - Total Expenses) for simplicity.
        $totalIncome = Sale::completed()->sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $currentBalance = $totalIncome - $totalExpenses;

        // 2. Monthly Stats
        $monthSales = Sale::completed()->thisMonth()->sum('total_amount');
        $monthExpenses = Expense::thisMonth()->sum('amount');
        
        // 3. Mini Chart Data (Sales Last 7 Days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales = Sale::completed()->whereDate('sale_date', $date)->sum('total_amount');
            $chartData[] = [
                'x' => 6 - $i, // 0 to 6 index
                'y' => (float) $dailySales,
                'date' => $date->format('Y-m-d')
            ];
        }

        // 4. Spending Categories (This Month)
        $spendingCategories = Expense::thisMonth()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($expense) {
                return [
                    'category' => ucfirst(str_replace('_', ' ', $expense->category)),
                    'amount' => (float) $expense->total
                ];
            });

        // 5. Recent Transactions
        $recentTransactions = Sale::completed()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'type' => 'income', // Sales are income
                    'name' => 'Walk-in Customer',
                    'date' => $sale->sale_date->toIso8601String(),
                    'amount' => (float) $sale->total_amount,
                    'category' => 'Sale',
                ];
            });
            
        // Add recent expenses to transactions for a mixed view (optional, but good for "Recent Activity")
        $recentExpenses = Expense::orderByDesc('expense_date')
            ->limit(5)
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'type' => 'expense',
                    'name' => $expense->title ?? 'Expense',
                    'date' => $expense->expense_date->toIso8601String(),
                    'amount' => (float) $expense->amount,
                    'category' => ucfirst(str_replace('_', ' ', $expense->category)),
                ];
            });
            
        // Merge and sort transactions
        $allTransactions = collect($recentTransactions)->merge($recentExpenses)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        return response()->json([
            'balance' => $currentBalance,
            'income' => $monthSales, // This month income
            'expenses' => $monthExpenses, // This month expense
            'chart_data' => $chartData,
            'spending_categories' => $spendingCategories,
            'recent_transactions' => $allTransactions,
        ]);
    }

    /**
     * Paginated unified activity feed (sales, expenses, inventory transactions).
     *
     * Query params:
     * - page (default 1)
     * - per_page (default 20, max 100)
     * - types (optional comma-separated: sale, expense, inventory)
     */
    public function transactions(Request $request)
    {
        $page = max((int) $request->get('page', 1), 1);
        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(min($perPage, 100), 1);

        $types = $request->get('types'); // e.g. "sale,expense,inventory"
        $typeFilter = null;
        if (is_string($types) && trim($types) !== '') {
            $typeFilter = collect(explode(',', $types))
                ->map(fn ($t) => trim(strtolower($t)))
                ->filter()
                ->unique()
                ->values();
        }

        // We fetch more than needed and slice after merge-sort.
        $take = $page * $perPage;

        $items = collect();

        if (!$typeFilter || $typeFilter->contains('sale')) {
            $sales = Sale::completed()
                ->orderByDesc('created_at')
                ->limit($take)
                ->get()
                ->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'entity' => 'sale',
                        'type' => 'income',
                        'name' => 'Walk-in Customer',
                        'date' => $sale->sale_date ? $sale->sale_date->toIso8601String() : $sale->created_at->toIso8601String(),
                        'amount' => (float) $sale->total_amount,
                        'category' => 'Sale',
                    ];
                });
            $items = $items->merge($sales);
        }

        if (!$typeFilter || $typeFilter->contains('expense')) {
            $expenses = Expense::orderByDesc('expense_date')
                ->limit($take)
                ->get()
                ->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'entity' => 'expense',
                        'type' => 'expense',
                        'name' => $expense->title ?? 'Expense',
                        'date' => $expense->expense_date ? $expense->expense_date->toIso8601String() : $expense->created_at->toIso8601String(),
                        'amount' => (float) $expense->amount,
                        'category' => ucfirst(str_replace('_', ' ', $expense->category)),
                    ];
                });
            $items = $items->merge($expenses);
        }

        if (!$typeFilter || $typeFilter->contains('inventory')) {
            $inventory = InventoryTransaction::with(['product'])
                ->approved()
                ->orderByDesc('transaction_date')
                ->limit($take)
                ->get()
                ->map(function ($txn) {
                    $isIncrease = $txn->isIncrease();
                    $label = $txn->transaction_type_name ?? ucwords(str_replace('_', ' ', $txn->transaction_type));
                    $productName = $txn->product ? $txn->product->name : 'Product';
                    return [
                        'id' => $txn->id,
                        'entity' => 'inventory',
                        'type' => $isIncrease ? 'inventory_in' : 'inventory_out',
                        'name' => $label . ' - ' . $productName,
                        'date' => $txn->transaction_date ? $txn->transaction_date->toIso8601String() : $txn->created_at->toIso8601String(),
                        'amount' => (float) ($txn->total_cost ?? 0),
                        'category' => $label,
                    ];
                });
            $items = $items->merge($inventory);
        }

        $sorted = $items
            ->sortByDesc('date')
            ->values();

        $total = $sorted->count();
        $lastPage = (int) ceil($total / $perPage);
        $lastPage = max($lastPage, 1);

        $slice = $sorted
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return response()->json([
            'data' => $slice,
            'meta' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ]
        ]);
    }

    /**
     * Get comprehensive analytics data for Smart Data Analysis page.
     */
    public function analytics()
    {
        // Sales Trend (Last 30 days)
        $salesTrend = Sale::completed()
            ->where('sale_date', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'total' => (float) $item->total,
                    'transactions' => (int) $item->transactions,
                ];
            });

        // Profit Summary
        $todaySales = Sale::completed()->today()->sum('total_amount');
        $todayExpenses = Expense::today()->sum('amount');
        $todayProfit = $todaySales - $todayExpenses;
        
        $weekSales = Sale::completed()->thisWeek()->sum('total_amount');
        $weekExpenses = Expense::thisWeek()->sum('amount');
        $weekProfit = $weekSales - $weekExpenses;
        
        $monthSales = Sale::completed()->thisMonth()->sum('total_amount');
        $monthExpenses = Expense::thisMonth()->sum('amount');
        $monthProfit = $monthSales - $monthExpenses;

        // Top 5 Best Sellers
        $topProducts = DB::table('products')
            ->select('products.id', 'products.name', 
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sales_details.quantity * sales_details.unit_price), 0) as total_revenue'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'total_sold' => (int) $product->total_sold,
                    'total_revenue' => (float) $product->total_revenue,
                ];
            });

        // Dead Stock (No sales in last 30 days but has stock)
        $deadStock = DB::table('products')
            ->select('products.id', 'products.name', 'products.stock_quantity',
                DB::raw("COALESCE(MAX(sales.sale_date), '1970-01-01') as last_sale_date"))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->where('products.stock_quantity', '>', 0)
            ->groupBy('products.id', 'products.name', 'products.stock_quantity')
            ->havingRaw("MAX(sales.sale_date) < ? OR MAX(sales.sale_date) IS NULL", [Carbon::now()->subDays(30)])
            ->orderByDesc('products.stock_quantity')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                $lastSale = $product->last_sale_date && $product->last_sale_date != '1970-01-01' 
                    ? Carbon::parse($product->last_sale_date) 
                    : null;
                $daysWithoutSale = $lastSale ? Carbon::now()->diffInDays($lastSale) : 999;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => (int) $product->stock_quantity,
                    'days_without_sale' => $daysWithoutSale,
                ];
            });

        // Stock Health Summary
        $totalProducts = DB::table('products')->where('is_active', true)->count();
        $healthyStock = DB::table('products')
            ->where('is_active', true)
            ->whereRaw('stock_quantity > min_stock_level')
            ->count();
        $lowStock = DB::table('products')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', DB::raw('min_stock_level'))
            ->count();
        $outOfStock = DB::table('products')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 0)
            ->count();

        // Busiest Days of Week
        $busiestDays = Sale::completed()
            ->selectRaw("TO_CHAR(sale_date, 'Day') as day_name, COUNT(*) as total_transactions")
            ->groupBy(DB::raw("TO_CHAR(sale_date, 'Day')"))
            ->orderByDesc('total_transactions')
            ->get()
            ->map(function ($item) {
                return [
                    'day_name' => trim($item->day_name),
                    'total_transactions' => (int) $item->total_transactions,
                ];
            });

        // Busiest Hours
        $busiestHours = Sale::completed()
            ->selectRaw("EXTRACT(HOUR FROM created_at) as hour, COUNT(*) as transactions")
            ->groupBy(DB::raw("EXTRACT(HOUR FROM created_at)"))
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                $hour = (int) $item->hour;
                $label = sprintf('%02d:00', $hour);
                return [
                    'hour' => $hour,
                    'label' => $label,
                    'transactions' => (int) $item->transactions,
                ];
            });

        // Most Profitable Products
        $mostProfitable = DB::table('products')
            ->select('products.id', 'products.name',
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sales_details.quantity * (sales_details.unit_price - products.cost)), 0) as total_profit'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->groupBy('products.id', 'products.name')
            ->havingRaw('COALESCE(SUM(sales_details.quantity), 0) > 0')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'total_sold' => (int) $product->total_sold,
                    'total_profit' => (float) $product->total_profit,
                ];
            });

        // Customer Type (New vs Repeat) & Loyal Customers Features Removed
        $newCustomersThisMonth = 0;
        $totalCustomers = 0;
        $repeatCustomers = 0;
        $loyalCustomers = collect();

        // Monthly Performance (Last 12 months)
        $monthlyPerformance = Sale::completed()
            ->where('sale_date', '>=', Carbon::now()->subMonths(12))
            ->selectRaw("TO_CHAR(sale_date, 'YYYY-MM') as month_key, TO_CHAR(sale_date, 'Mon YY') as month, SUM(total_amount) as sales, COUNT(*) as transactions")
            ->groupBy(DB::raw("TO_CHAR(sale_date, 'YYYY-MM')"), DB::raw("TO_CHAR(sale_date, 'Mon YY')"))
            ->orderBy('month_key')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'sales' => (float) $item->sales,
                    'transactions' => (int) $item->transactions,
                ];
            });

        // Expense Breakdown by Category (This Month)
        $expenseByCategory = Expense::thisMonth()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($expense) {
                return [
                    'category' => ucfirst(str_replace('_', ' ', $expense->category)),
                    'total' => (float) $expense->total,
                ];
            });

        // Recent Sales Activity
        $recentSales = Sale::completed()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'customer_name' => 'Walk-in Customer',
                    'total_amount' => (float) $sale->total_amount,
                    'created_at' => $sale->created_at->toIso8601String(),
                ];
            });

        // Business Health Score Calculation
        $healthScores = [];
        
        // Profit Score (0-25 points)
        $profitScore = 0;
        if ($monthProfit > 0) {
            $profitMarginPercent = $monthSales > 0 ? ($monthProfit / $monthSales) * 100 : 0;
            if ($profitMarginPercent >= 30) $profitScore = 25;
            elseif ($profitMarginPercent >= 20) $profitScore = 20;
            elseif ($profitMarginPercent >= 10) $profitScore = 15;
            elseif ($profitMarginPercent >= 5) $profitScore = 10;
            else $profitScore = 5;
        }
        $healthScores['profit'] = ['score' => $profitScore, 'max' => 25, 'label' => 'Profitabilitas'];
        
        // Repeat Customer Score (0-20 points)
        $repeatCustomerPercentage = $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;
        $repeatScore = 0;
        if ($repeatCustomerPercentage >= 50) $repeatScore = 20;
        elseif ($repeatCustomerPercentage >= 35) $repeatScore = 15;
        elseif ($repeatCustomerPercentage >= 20) $repeatScore = 10;
        elseif ($repeatCustomerPercentage >= 10) $repeatScore = 5;
        $healthScores['repeat_customer'] = ['score' => $repeatScore, 'max' => 20, 'label' => 'Loyalitas Pelanggan'];
        
        // Stock Health Score (0-20 points)
        $stockHealthPercent = $totalProducts > 0 ? ($healthyStock / $totalProducts) * 100 : 0;
        $stockScore = 0;
        if ($stockHealthPercent >= 80) $stockScore = 20;
        elseif ($stockHealthPercent >= 60) $stockScore = 15;
        elseif ($stockHealthPercent >= 40) $stockScore = 10;
        elseif ($stockHealthPercent >= 20) $stockScore = 5;
        $healthScores['stock'] = ['score' => $stockScore, 'max' => 20, 'label' => 'Kesehatan Stok'];
        
        // Sales Trend Score (0-20 points)
        $lastMonthSales = Sale::completed()
            ->whereBetween('sale_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('total_amount');
        $growthRate = $lastMonthSales > 0 ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
        $trendScore = 0;
        if ($growthRate >= 20) $trendScore = 20;
        elseif ($growthRate >= 10) $trendScore = 17;
        elseif ($growthRate >= 0) $trendScore = 14;
        elseif ($growthRate >= -10) $trendScore = 10;
        elseif ($growthRate >= -20) $trendScore = 5;
        $healthScores['trend'] = ['score' => $trendScore, 'max' => 20, 'label' => 'Tren Penjualan'];
        
        // Average Margin Score (0-15 points)
        $avgMargin = DB::table('products')
            ->where('is_active', true)
            ->whereRaw('price > 0')
            ->avg(DB::raw('((price - cost) / price) * 100')) ?? 0;
        $marginScore = 0;
        if ($avgMargin >= 40) $marginScore = 15;
        elseif ($avgMargin >= 30) $marginScore = 12;
        elseif ($avgMargin >= 20) $marginScore = 9;
        elseif ($avgMargin >= 10) $marginScore = 6;
        else $marginScore = 3;
        $healthScores['margin'] = ['score' => $marginScore, 'max' => 15, 'label' => 'Margin Rata-rata'];
        
        $businessHealthScore = array_sum(array_column($healthScores, 'score'));
        $businessHealthStatus = '';
        $businessHealthColor = '';
        if ($businessHealthScore >= 70) {
            $businessHealthStatus = 'Bisnis Sehat';
            $businessHealthColor = 'green';
        } elseif ($businessHealthScore >= 40) {
            $businessHealthStatus = 'Butuh Perbaikan';
            $businessHealthColor = 'yellow';
        } else {
            $businessHealthStatus = 'Risiko Rugi';
            $businessHealthColor = 'red';
        }

        // Recommended Actions
        $suggestedActions = [];
        
        // Check fast-moving products that need restock
        $fastMovingProducts = DB::table('products')
            ->select('products.id', 'products.name', 'products.stock_quantity', 'products.min_stock_level',
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold_30d'),
                DB::raw('FLOOR(COALESCE(SUM(sales_details.quantity), 0) / 30) as daily_avg_sales'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed')
                    ->where('sales.sale_date', '>=', Carbon::now()->subDays(30));
            })
            ->where('products.is_active', true)
            ->groupBy('products.id', 'products.name', 'products.stock_quantity', 'products.min_stock_level')
            ->havingRaw('COALESCE(SUM(sales_details.quantity), 0) > 0')
            ->orderByDesc('total_sold_30d')
            ->limit(3)
            ->get();
        
        foreach ($fastMovingProducts as $product) {
            $dailyAvg = $product->daily_avg_sales > 0 ? $product->daily_avg_sales : 1;
            $daysUntilStockout = floor($product->stock_quantity / $dailyAvg);
            if ($daysUntilStockout < 7) {
                $suggestedActions[] = [
                    'type' => 'restock',
                    'priority' => 'high',
                    'icon' => 'package',
                    'color' => 'red',
                    'message' => "Produk {$product->name} akan habis dalam {$daysUntilStockout} hari → Segera restock!",
                    'action_text' => 'Lihat Produk',
                    'action_url' => "/inventory/products/{$product->id}",
                ];
            }
        }
        
        // Check dead stock for promo suggestion
        foreach ($deadStock as $product) {
            if ($product['days_without_sale'] > 30) {
                $suggestedActions[] = [
                    'type' => 'promo',
                    'priority' => 'medium',
                    'icon' => 'tag',
                    'color' => 'yellow',
                    'message' => "Produk {$product['name']} tidak bergerak {$product['days_without_sale']} hari → Waktu untuk promo!",
                    'action_text' => 'Buat Promo',
                    'action_url' => "/inventory/products/{$product['id']}/edit",
                ];
            }
        }
        
        // Check for declining sales
        if ($growthRate < -10) {
            $suggestedActions[] = [
                'type' => 'alert',
                'priority' => 'high',
                'icon' => 'alert-triangle',
                'color' => 'red',
                'message' => "Penjualan turun " . number_format(abs($growthRate), 1) . "% dari bulan lalu → Perlu strategi baru!",
                'action_text' => 'Lihat Analisis',
                'action_url' => '/analytics',
            ];
        }
        
        // Check low stock products
        if ($lowStock > 0) {
            $suggestedActions[] = [
                'type' => 'restock',
                'priority' => 'medium',
                'icon' => 'shopping-cart',
                'color' => 'orange',
                'message' => "Ada {$lowStock} produk dengan stok rendah → Perlu restock segera!",
                'action_text' => 'Lihat Stok',
                'action_url' => '/inventory/products',
            ];
        }
        
        // Sort by priority
        usort($suggestedActions, function($a, $b) {
            $priority = ['high' => 1, 'medium' => 2, 'low' => 3];
            return $priority[$a['priority']] <=> $priority[$b['priority']];
        });
        
        // Limit to 5 actions
        $suggestedActions = array_slice($suggestedActions, 0, 5);

        return response()->json([
            'business_health' => [
                'score' => (int) $businessHealthScore,
                'status' => $businessHealthStatus,
                'color' => $businessHealthColor,
                'scores' => $healthScores,
            ],
            'recommended_actions' => $suggestedActions,
            'sales_trend' => $salesTrend,
            'profit_summary' => [
                'today' => (float) $todayProfit,
                'this_week' => (float) $weekProfit,
                'this_month' => (float) $monthProfit,
            ],
            'top_products' => $topProducts,
            'dead_stock' => $deadStock,
            'stock_health' => [
                'total_products' => (int) $totalProducts,
                'healthy_stock' => (int) $healthyStock,
                'low_stock' => (int) $lowStock,
                'out_of_stock' => (int) $outOfStock,
            ],
            'busiest_days' => $busiestDays,
            'busiest_hours' => $busiestHours,
            'most_profitable_products' => $mostProfitable,
            'customer_insights' => [
                'new_customers_this_month' => (int) $newCustomersThisMonth,
                'repeat_customers' => (int) $repeatCustomers,
            ],
            'loyal_customers' => $loyalCustomers,
            'monthly_performance' => $monthlyPerformance,
            'expense_breakdown' => $expenseByCategory,
            'recent_sales' => $recentSales,
        ]);
    }
}
