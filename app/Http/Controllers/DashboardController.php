<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\ProfitLoss;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==================== BASIC KPI METRICS ====================
        $totalSales = Sale::completed()->sum('total_amount');
        $totalCustomers = 0; // Customer feature removed
        $totalProducts = Product::active()->count();
        
        // Today's metrics
        $todaySales = Sale::completed()->today()->sum('total_amount');
        $todayExpenses = Expense::today()->sum('amount');
        $todayProfit = $todaySales - $todayExpenses;
        
        // This week's metrics
        $weekSales = Sale::completed()->thisWeek()->sum('total_amount');
        $weekExpenses = Expense::thisWeek()->sum('amount');
        $weekProfit = $weekSales - $weekExpenses;
        
        // This month's metrics
        $monthSales = Sale::completed()->thisMonth()->sum('total_amount');
        $monthExpenses = Expense::thisMonth()->sum('amount');
        $monthProfit = $monthSales - $monthExpenses;
        
        // Total expenses (this month)
        $totalExpenses = $monthExpenses;
        $avgOrderValue = Sale::completed()->avg('total_amount') ?? 0;
        
        // Growth Rate (compared to last month)
        $lastMonthSales = Sale::completed()
            ->whereBetween('sale_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('total_amount');
        
        $growthRate = $lastMonthSales > 0 ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;

        // ==================== 1. BEST SELLER & WEAK PRODUCT ANALYTICS ====================
        
        // Top 5 Best-Selling Products
        $topProducts = Product::select('products.*', 
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sales_details.line_total), 0) as total_revenue'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Worst Selling Products (products with least sales but have stock)
        $worstProducts = Product::select('products.*', 
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->where('products.stock_quantity', '>', 0)
            ->groupBy('products.id')
            ->orderBy('total_sold')
            ->limit(5)
            ->get();

        // Stagnant Products (no sales in last 30 days but had sales before)
        $stagnantProducts = Product::select('products.*', 
                DB::raw('MAX(sales.sale_date) as last_sale_date'),
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->havingRaw('MAX(sales.sale_date) < ? OR MAX(sales.sale_date) IS NULL', [Carbon::now()->subDays(30)])
            ->havingRaw('COALESCE(SUM(sales_details.quantity), 0) > 0')
            ->limit(5)
            ->get();

        // Highest Margin Products
        $highMarginProducts = Product::select('products.*')
            ->where('is_active', true)
            ->whereRaw('price > 0')
            ->orderByRaw('((price - cost) / price) * 100 DESC')
            ->limit(5)
            ->get()
            ->map(function($product) {
                $product->margin_percentage = $product->price > 0 ? (($product->price - $product->cost) / $product->price) * 100 : 0;
                $product->profit_per_unit = $product->price - $product->cost;
                return $product;
            });

        // ==================== 2. SALES TREND ANALYTICS ====================
        
        // Sales Trend Chart Data (All Time - aggregated by month)
        $salesTrend = Sale::completed()
            ->selectRaw("TO_CHAR(sale_date, 'YYYY-MM') as month_key, TO_CHAR(sale_date, 'Mon YY') as date, SUM(total_amount) as total, COUNT(*) as transactions")
            ->groupBy(DB::raw("TO_CHAR(sale_date, 'YYYY-MM')"), DB::raw("TO_CHAR(sale_date, 'Mon YY')"))
            ->orderBy('month_key')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total,
                    'transactions' => $item->transactions
                ];
            });

        // Busiest Days of Week (PostgreSQL compatible)
        $busiestDays = Sale::completed()
            ->where('sale_date', '>=', Carbon::now()->subDays(90))
            ->selectRaw("TRIM(TO_CHAR(sale_date, 'Day')) as day_name, EXTRACT(DOW FROM sale_date) as day_of_week, COUNT(*) as total_transactions, SUM(total_amount) as total_sales")
            ->groupBy(DB::raw("TRIM(TO_CHAR(sale_date, 'Day'))"), DB::raw('EXTRACT(DOW FROM sale_date)'))
            ->orderBy('day_of_week')
            ->get();

        // Busiest Hours (based on sale creation time) - PostgreSQL compatible
        $busiestHours = Sale::completed()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('EXTRACT(HOUR FROM created_at)::integer as hour, COUNT(*) as total_transactions, SUM(total_amount) as total_sales')
            ->groupBy(DB::raw('EXTRACT(HOUR FROM created_at)'))
            ->orderBy('hour')
            ->get()
            ->map(function($item) {
                $hour = (int) $item->hour;
                $label = sprintf('%02d:00 - %02d:59', $hour, $hour);
                return [
                    'hour' => $hour,
                    'label' => $label,
                    'transactions' => $item->total_transactions,
                    'sales' => $item->total_sales
                ];
            });

        // Monthly Performance (last 12 months) - PostgreSQL compatible
        $monthlyPerformance = Sale::completed()
            ->where('sale_date', '>=', Carbon::now()->subMonths(12))
            ->selectRaw('EXTRACT(YEAR FROM sale_date)::integer as year, EXTRACT(MONTH FROM sale_date)::integer as month, SUM(total_amount) as total_sales, COUNT(*) as total_transactions')
            ->groupBy(DB::raw('EXTRACT(YEAR FROM sale_date)'), DB::raw('EXTRACT(MONTH FROM sale_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'sales' => $item->total_sales,
                    'transactions' => $item->total_transactions
                ];
            });

        // Strongest Month - PostgreSQL compatible
        $strongestMonth = Sale::completed()
            ->where('sale_date', '>=', Carbon::now()->subYear())
            ->selectRaw("TRIM(TO_CHAR(sale_date, 'Month')) as month_name, EXTRACT(MONTH FROM sale_date)::integer as month, SUM(total_amount) as total_sales")
            ->groupBy(DB::raw("TRIM(TO_CHAR(sale_date, 'Month'))"), DB::raw('EXTRACT(MONTH FROM sale_date)'))
            ->orderByDesc('total_sales')
            ->first();

        // ==================== 3. REPEAT VS NEW CUSTOMER ANALYTICS (Customer feature removed) ====================
        
        $newCustomersThisMonth = 0;
        $totalCustomersWithPurchases = 0;
        $repeatCustomers = 0;
        $repeatCustomerPercentage = 0;
        $loyalCustomers = collect();
        $loyalCustomerProducts = collect();

        // ==================== 4. PROFIT MARGIN BY PRODUCT ====================
        
        // Most Profitable Products (considering actual sales)
        $mostProfitableProducts = DB::table('products')
            ->select('products.*', 
                DB::raw('SUM(sales_details.quantity) as total_sold'),
                DB::raw('SUM((sales_details.unit_price - products.cost) * sales_details.quantity) as total_profit'),
                DB::raw('AVG((sales_details.unit_price - products.cost) / sales_details.unit_price * 100) as avg_margin'))
            ->join('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('products.id')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get();

        // Least Profitable Products (or loss-making)
        $leastProfitableProducts = DB::table('products')
            ->select('products.*', 
                DB::raw('SUM(sales_details.quantity) as total_sold'),
                DB::raw('SUM((sales_details.unit_price - products.cost) * sales_details.quantity) as total_profit'),
                DB::raw('AVG((sales_details.unit_price - products.cost) / sales_details.unit_price * 100) as avg_margin'))
            ->join('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('products.id')
            ->orderBy('total_profit')
            ->limit(5)
            ->get();

        // High Volume but Low Margin Products
        $highVolumeLowMargin = DB::table('products')
            ->select('products.*', 
                DB::raw('SUM(sales_details.quantity) as total_sold'),
                DB::raw('SUM(sales_details.line_total) as total_revenue'),
                DB::raw('((products.price - products.cost) / products.price * 100) as margin_percentage'))
            ->join('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('products.id')
            ->havingRaw('SUM(sales_details.quantity) > 10') // High volume threshold
            ->havingRaw('((products.price - products.cost) / products.price * 100) < 20') // Low margin threshold
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // ==================== 5. STOCK HEALTH INDICATOR ====================
        
        // Low Stock Products
        $lowStockProducts = Product::active()
            ->lowStock()
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        // Fast Moving Products (high turnover rate - need quick restock)
        $fastMovingProducts = Product::select('products.*',
                DB::raw('SUM(sales_details.quantity) as total_sold_30d'),
                DB::raw('FLOOR(SUM(sales_details.quantity) / 30) as daily_avg_sales'))
            ->join('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.sale_date', '>=', Carbon::now()->subDays(30))
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->orderByDesc('total_sold_30d')
            ->limit(10)
            ->get()
            ->map(function($product) {
                $product->days_until_stockout = $product->daily_avg_sales > 0 
                    ? floor($product->stock_quantity / $product->daily_avg_sales) 
                    : 999;
                return $product;
            });

        // Dead Stock Products (no movement in 60 days)
        $deadStockProducts = Product::select('products.*',
                DB::raw('MAX(sales.sale_date) as last_sale_date'),
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_ever_sold'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->where('products.stock_quantity', '>', 0)
            ->groupBy('products.id')
            ->havingRaw('MAX(sales.sale_date) < ? OR MAX(sales.sale_date) IS NULL', [Carbon::now()->subDays(60)])
            ->orderByDesc('products.stock_quantity')
            ->limit(10)
            ->get()
            ->map(function($product) {
                $product->dead_stock_value = $product->stock_quantity * $product->cost;
                $product->days_without_sale = $product->last_sale_date 
                    ? (int) Carbon::parse($product->last_sale_date)->diffInDays(now()) 
                    : 999;
                return $product;
            });

        // Stock Health Summary
        $stockHealthSummary = [
            'total_products' => Product::active()->count(),
            'healthy_stock' => Product::active()->whereRaw('stock_quantity > min_stock_level')->count(),
            'low_stock' => Product::active()->lowStock()->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::active()->where('stock_quantity', '<=', 0)->count(),
            'dead_stock_count' => $deadStockProducts->count(),
            'dead_stock_value' => $deadStockProducts->sum('dead_stock_value'),
        ];

        // ==================== 6. CASHFLOW INSIGHT SUMMARY ====================
        
        // Income vs Expenses (last 30 days)
        $cashflowData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $income = Sale::completed()->whereDate('sale_date', $date)->sum('total_amount');
            $expenses = Expense::whereDate('expense_date', $date)->sum('amount');
            $cashflowData[] = [
                'date' => $date->format('M d'),
                'income' => (float) $income,
                'expenses' => (float) $expenses,
                'profit' => (float) ($income - $expenses)
            ];
        }

        // Expense Breakdown by Category (This Month)
        $expenseByCategory = Expense::thisMonth()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($expense) {
                return [
                    'category' => ucfirst(str_replace('_', ' ', $expense->category)),
                    'total' => (float) $expense->total
                ];
            });
        
        // Biggest Expense Category
        $biggestExpenseCategory = $expenseByCategory->first();

        // Profit Summary
        $profitSummary = [
            'today' => $todayProfit,
            'this_week' => $weekProfit,
            'this_month' => $monthProfit,
            'last_month' => Sale::completed()
                ->whereBetween('sale_date', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->sum('total_amount') - Expense::whereBetween('expense_date', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->sum('amount')
        ];

        // ==================== 7. SUGGESTED ACTIONS (RULE-BASED) ====================
        
        $suggestedActions = [];

        // Check fast-moving products that need restock
        foreach ($fastMovingProducts->take(3) as $product) {
            if ($product->days_until_stockout < 7) {
                $suggestedActions[] = [
                    'type' => 'restock',
                    'priority' => 'high',
                    'icon' => 'package',
                    'color' => 'red',
                    'message' => "Produk {$product->name} akan habis dalam {$product->days_until_stockout} hari → Segera restock!",
                    'action_text' => 'Lihat Produk',
                    'action_url' => route('inventory.products.show', $product->id)
                ];
            }
        }

        // Check dead stock for promo suggestion
        foreach ($deadStockProducts->take(2) as $product) {
            $suggestedActions[] = [
                'type' => 'promo',
                'priority' => 'medium',
                'icon' => 'tag',
                'color' => 'yellow',
                'message' => "Produk {$product->name} tidak bergerak {$product->days_without_sale} hari → Waktu untuk promo!",
                'action_text' => 'Buat Promo',
                'action_url' => route('inventory.products.edit', $product->id)
            ];
        }

        // Check busiest day for bundle suggestion
        $busiestDay = $busiestDays->sortByDesc('total_transactions')->first();
        if ($busiestDay) {
            $suggestedActions[] = [
                'type' => 'bundle',
                'priority' => 'low',
                'icon' => 'gift',
                'color' => 'blue',
                'message' => "Hari {$busiestDay->day_name} adalah hari tersibuk → Tawarkan paket bundle!",
                'action_text' => 'Buat Bundle',
                'action_url' => route('inventory.products.create')
            ];
        }

        // Check low margin products
        foreach ($highVolumeLowMargin as $product) {
            $suggestedActions[] = [
                'type' => 'pricing',
                'priority' => 'medium',
                'icon' => 'trending-up',
                'color' => 'orange',
                'message' => "Margin produk {$product->name} terlalu kecil (" . number_format($product->margin_percentage, 1) . "%) → Evaluasi harga jual",
                'action_text' => 'Edit Harga',
                'action_url' => route('inventory.products.edit', $product->id)
            ];
        }

        // Check top selling products for restock reminder
        foreach ($topProducts->take(2) as $product) {
            if ($product->stock_quantity < $product->min_stock_level * 2) {
                $suggestedActions[] = [
                    'type' => 'restock',
                    'priority' => 'medium',
                    'icon' => 'shopping-cart',
                    'color' => 'green',
                    'message' => "Produk {$product->name} laris ({$product->total_sold} terjual) → Tambah stok minggu ini",
                    'action_text' => 'Tambah Stok',
                    'action_url' => route('inventory.products.edit', $product->id)
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
                'action_url' => route('analytics')
            ];
        }

        // Sort by priority first
        usort($suggestedActions, function($a, $b) {
            $priority = ['high' => 1, 'medium' => 2, 'low' => 3];
            return $priority[$a['priority']] <=> $priority[$b['priority']];
        });

        // Get 3 random suggestions, prioritizing high priority ones
        if (count($suggestedActions) > 3) {
            $highPriority = array_filter($suggestedActions, fn($a) => $a['priority'] === 'high');
            $otherPriority = array_filter($suggestedActions, fn($a) => $a['priority'] !== 'high');
            
            // Shuffle other priorities for randomness
            shuffle($otherPriority);
            
            // Combine: all high priority + random others, limit to 3
            $suggestedActions = array_merge(array_values($highPriority), array_values($otherPriority));
            $suggestedActions = array_slice($suggestedActions, 0, 3);
        }

        // ==================== 8. BUSINESS HEALTH SCORE ====================
        
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
        $repeatScore = 0;
        if ($repeatCustomerPercentage >= 50) $repeatScore = 20;
        elseif ($repeatCustomerPercentage >= 35) $repeatScore = 15;
        elseif ($repeatCustomerPercentage >= 20) $repeatScore = 10;
        elseif ($repeatCustomerPercentage >= 10) $repeatScore = 5;
        $healthScores['repeat_customer'] = ['score' => $repeatScore, 'max' => 20, 'label' => 'Loyalitas Pelanggan'];

        // Stock Health Score (0-20 points)
        // Use same calculation as API - calculate directly from products table
        $totalProductsForHealth = DB::table('products')->where('is_active', true)->count();
        $healthyStockForHealth = DB::table('products')
            ->where('is_active', true)
            ->whereRaw('stock_quantity > min_stock_level')
            ->count();
        $stockHealthPercent = $totalProductsForHealth > 0 ? ($healthyStockForHealth / $totalProductsForHealth) * 100 : 0;
        $stockScore = 0;
        if ($stockHealthPercent >= 80) $stockScore = 20;
        elseif ($stockHealthPercent >= 60) $stockScore = 15;
        elseif ($stockHealthPercent >= 40) $stockScore = 10;
        elseif ($stockHealthPercent >= 20) $stockScore = 5;
        $healthScores['stock'] = ['score' => $stockScore, 'max' => 20, 'label' => 'Kesehatan Stok'];

        // Sales Trend Score (0-20 points)
        // Recalculate to ensure exact same calculation as API
        $lastMonthSalesForTrend = Sale::completed()
            ->whereBetween('sale_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('total_amount');
        $growthRateForTrend = $lastMonthSalesForTrend > 0 ? (($monthSales - $lastMonthSalesForTrend) / $lastMonthSalesForTrend) * 100 : 0;
        $trendScore = 0;
        if ($growthRateForTrend >= 20) $trendScore = 20;
        elseif ($growthRateForTrend >= 10) $trendScore = 17;
        elseif ($growthRateForTrend >= 0) $trendScore = 14;
        elseif ($growthRateForTrend >= -10) $trendScore = 10;
        elseif ($growthRateForTrend >= -20) $trendScore = 5;
        $healthScores['trend'] = ['score' => $trendScore, 'max' => 20, 'label' => 'Tren Penjualan'];

        // Average Margin Score (0-15 points)
        // Use same calculation as API - use DB::table instead of Eloquent
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

        // ==================== 9. CATEGORY INSIGHTS ====================
        
        // Get real category performance
        $categoryPerformance = Product::select('products.category',
                DB::raw('COUNT(DISTINCT products.id) as product_count'),
                DB::raw('COALESCE(SUM(sales_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sales_details.line_total), 0) as total_revenue'),
                DB::raw('AVG((products.price - products.cost) / products.price * 100) as avg_margin'))
            ->leftJoin('sales_details', 'products.id', '=', 'sales_details.product_id')
            ->leftJoin('sales', function($join) {
                $join->on('sales_details.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed');
            })
            ->where('products.is_active', true)
            ->whereNotNull('products.category')
            ->groupBy('products.category')
            ->orderByDesc('total_revenue')
            ->get();

        // Category insights based on data patterns
        $categoryInsights = [];
        foreach ($categoryPerformance as $cat) {
            // Check weekend vs weekday performance for this category (PostgreSQL: DOW 0=Sunday, 6=Saturday)
            $weekendSales = DB::table('products')
                ->join('sales_details', 'products.id', '=', 'sales_details.product_id')
                ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
                ->where('products.category', $cat->category)
                ->where('sales.status', 'completed')
                ->whereRaw('EXTRACT(DOW FROM sales.sale_date) IN (0, 6)') // Sunday = 0, Saturday = 6
                ->sum('sales_details.quantity');

            $weekdaySales = DB::table('products')
                ->join('sales_details', 'products.id', '=', 'sales_details.product_id')
                ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
                ->where('products.category', $cat->category)
                ->where('sales.status', 'completed')
                ->whereRaw('EXTRACT(DOW FROM sales.sale_date) NOT IN (0, 6)')
                ->sum('sales_details.quantity');

            $insight = '';
            if ($cat->total_sold > 0) {
                if ($weekendSales > $weekdaySales * 0.4) { // Weekend performs well
                    $insight = "Kategori {$cat->category}: Lebih laku di weekend";
                } else {
                    $insight = "Kategori {$cat->category}: Performa stabil sepanjang minggu";
                }

                if ($cat->avg_margin >= 30) {
                    $insight .= " • Margin tinggi (" . number_format($cat->avg_margin, 0) . "%)";
                } elseif ($cat->avg_margin < 15) {
                    $insight .= " • Margin rendah, evaluasi harga";
                }
            }

            if ($insight) {
                $categoryInsights[] = [
                    'category' => $cat->category,
                    'insight' => $insight,
                    'total_revenue' => $cat->total_revenue,
                    'avg_margin' => $cat->avg_margin
                ];
            }
        }
        
        // Customer Insights (Customer feature removed - return empty collections)
        $customersByCity = collect();
        $customersByGender = collect();
        $customersByAgeGroup = collect();
        
        // Recent Sales
        $recentSales = Sale::with(['salesDetails.product'])
            ->completed()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Transaction count today
        $todayTransactions = Sale::completed()->today()->count();
        $weekTransactions = Sale::completed()->thisWeek()->count();
        $monthTransactions = Sale::completed()->thisMonth()->count();

        return view('dashboard', compact(
            // Basic KPI
            'totalSales',
            'totalCustomers', 
            'totalProducts',
            'totalExpenses',
            'avgOrderValue',
            'growthRate',
            'todaySales',
            'todayExpenses',
            'todayProfit',
            'weekSales',
            'weekProfit',
            'monthSales',
            'monthExpenses',
            'monthProfit',
            'todayTransactions',
            'weekTransactions',
            'monthTransactions',
            
            // 1. Best Seller & Weak Product
            'topProducts',
            'worstProducts',
            'stagnantProducts',
            'highMarginProducts',
            
            // 2. Sales Trend
            'salesTrend',
            'busiestDays',
            'busiestHours',
            'monthlyPerformance',
            'strongestMonth',
            
            // 3. Repeat vs New Customer
            'newCustomersThisMonth',
            'repeatCustomers',
            'repeatCustomerPercentage',
            'loyalCustomers',
            'loyalCustomerProducts',
            
            // 4. Profit Margin
            'mostProfitableProducts',
            'leastProfitableProducts',
            'highVolumeLowMargin',
            
            // 5. Stock Health
            'lowStockProducts',
            'fastMovingProducts',
            'deadStockProducts',
            'stockHealthSummary',
            
            // 6. Cashflow
            'cashflowData',
            'expenseByCategory',
            'biggestExpenseCategory',
            'profitSummary',
            
            // 7. Suggested Actions
            'suggestedActions',
            
            // 8. Business Health Score
            'businessHealthScore',
            'businessHealthStatus',
            'businessHealthColor',
            'healthScores',
            
            // 9. Category
            'categoryPerformance',
            'categoryInsights',
            
            // Customer Insights
            'customersByCity',
            'customersByGender',
            'customersByAgeGroup',
            
            // Recent Activity
            'recentSales'
        ));
    }

    public function salesChart(Request $request)
    {
        $period = (int) $request->get('period', 30); // days
        
        // For "all time" (period >= 365), aggregate by month
        if ($period >= 365) {
            $salesData = Sale::completed()
                ->selectRaw("TO_CHAR(sale_date, 'YYYY-MM') as month_key, TO_CHAR(sale_date, 'Mon YY') as date, SUM(total_amount) as total")
                ->groupBy(DB::raw("TO_CHAR(sale_date, 'YYYY-MM')"), DB::raw("TO_CHAR(sale_date, 'Mon YY')"))
                ->orderBy('month_key')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'total' => (float) $item->total
                    ];
                });
        } else {
            // For shorter periods, show daily data
        $salesData = Sale::completed()
            ->where('sale_date', '>=', Carbon::now()->subDays($period))
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'total' => (float) $item->total
                ];
            });
        }

        return response()->json($salesData);
    }

    public function profitLossChart(Request $request)
    {
        $period = $request->get('period', '30'); // days
        
        $data = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $revenue = Sale::completed()
                ->whereDate('sale_date', $date)
                ->sum('total_amount');
            
            $expenses = Expense::whereDate('expense_date', $date)
                ->sum('amount');
            
            $profit = $revenue - $expenses;
            
            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => (float) $revenue,
                'expenses' => (float) $expenses,
                'profit' => (float) $profit
            ];
        }

        return response()->json($data);
    }

    public function hourlyPattern(Request $request)
    {
        $period = $request->get('period', '30');

        $data = Sale::completed()
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->selectRaw('EXTRACT(HOUR FROM created_at)::integer as hour, COUNT(*) as transactions, SUM(total_amount) as sales')
            ->groupBy(DB::raw('EXTRACT(HOUR FROM created_at)'))
            ->orderBy('hour')
            ->get()
            ->map(function($item) {
                return [
                    'hour' => sprintf('%02d:00', (int) $item->hour),
                    'transactions' => $item->transactions,
                    'sales' => (float) $item->sales
                ];
            });

        return response()->json($data);
    }
}
