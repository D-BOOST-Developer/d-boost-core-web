<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryDashboardController extends Controller
{
    /**
     * Display the main inventory dashboard
     */
    public function index(Request $request)
    {
        // Handle AJAX request for chart data - optimized for period
        if ($request->ajax() && $request->has('chart_period')) {
            $period = (int) $request->input('chart_period', 7);
            $endDate = now()->format('Y-m-d');
            $startDate = now()->subDays($period - 1)->format('Y-m-d');
            
            $increaseTypes = ['purchase', 'adjustment_increase', 'return_from_customer', 'transfer_in', 'initial_stock'];
            $decreaseTypes = ['sale', 'adjustment_decrease', 'return_to_supplier', 'transfer_out', 'damaged', 'expired'];
            
            // Fetch all stock in data in one query
            $stockInData = InventoryTransaction::approved()
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->whereIn('transaction_type', $increaseTypes)
                ->select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('SUM(quantity) as stock_in')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            // Fetch all stock out data in one query
            $stockOutData = InventoryTransaction::approved()
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->whereIn('transaction_type', $decreaseTypes)
                ->select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('SUM(quantity) as stock_out')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            // Generate complete date range
            $data = collect(range($period - 1, 0))->map(function ($daysAgo) use ($stockInData, $stockOutData) {
                $date = now()->subDays($daysAgo)->format('Y-m-d');
                return [
                    'date' => $date,
                    'stock_in' => (int) ($stockInData[$date]->stock_in ?? 0),
                    'stock_out' => (int) ($stockOutData[$date]->stock_out ?? 0)
                ];
            });
            
            return response()->json($data->values());
        }

        // Default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Total Stock Value (current inventory value)
        $totalStockValue = Product::active()
            ->sum(DB::raw('stock_quantity * cost'));
        
        // Total Stock Quantity
        $totalStockQuantity = Product::active()
            ->sum('stock_quantity');
        
        // Total Products
        $totalProducts = Product::active()->count();
        
        // Low Stock Products (below min_stock_level)
        $lowStockProducts = Product::active()
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->orderBy('stock_quantity')
            ->get();
        
        $lowStockCount = $lowStockProducts->count();
        $lowStockValue = $lowStockProducts->sum(function ($product) {
            return $product->stock_quantity * $product->cost;
        });
        
        // Out of Stock Products
        $outOfStockProducts = Product::active()
            ->where('stock_quantity', 0)
            ->count();
        
        // Stock Movements (In/Out) for the period
        $stockIn = InventoryTransaction::approved()
            ->whereIn('transaction_type', [
                'purchase',
                'adjustment_increase',
                'return_from_customer',
                'transfer_in',
                'initial_stock'
            ])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('quantity');
        
        $stockOut = InventoryTransaction::approved()
            ->whereIn('transaction_type', [
                'sale',
                'adjustment_decrease',
                'return_to_supplier',
                'transfer_out',
                'damaged',
                'expired'
            ])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('quantity');
        
        $netStockMovement = $stockIn - $stockOut;
        
        // Total Transaction Value (cost of inventory movements)
        $totalTransactionValue = InventoryTransaction::approved()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereNotNull('total_cost')
            ->sum('total_cost');
        
        // Transaction Count
        $transactionCount = InventoryTransaction::approved()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();
        
        // Top Products by Stock Value
        $topProductsByValue = Product::active()
            ->select('products.*', DB::raw('(stock_quantity * cost) as stock_value'))
            ->orderByDesc('stock_value')
            ->limit(5)
            ->get();
        
        // Top Products by Quantity
        $topProductsByQuantity = Product::active()
            ->orderByDesc('stock_quantity')
            ->limit(5)
            ->get();
        
        // Products by Category
        $productsByCategory = Product::active()
            ->select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(stock_quantity) as total_quantity'), DB::raw('SUM(stock_quantity * cost) as total_value'))
            ->groupBy('category')
            ->orderByDesc('total_value')
            ->get();
        
        // Recent Transactions (last 10)
        $recentTransactions = InventoryTransaction::with(['product', 'creator'])
            ->latest('transaction_date')
            ->limit(10)
            ->get();
        
        // Stock Movements Trend (last 7 days) - simplified
        $stockTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $increaseTypes = ['purchase', 'adjustment_increase', 'return_from_customer', 'transfer_in', 'initial_stock'];
            $decreaseTypes = ['sale', 'adjustment_decrease', 'return_to_supplier', 'transfer_out', 'damaged', 'expired'];
            
            return (object) [
                'date' => $date,
                'stock_in' => InventoryTransaction::approved()
                    ->whereDate('transaction_date', $date)
                    ->whereIn('transaction_type', $increaseTypes)
                    ->sum('quantity'),
                'stock_out' => InventoryTransaction::approved()
                    ->whereDate('transaction_date', $date)
                    ->whereIn('transaction_type', $decreaseTypes)
                    ->sum('quantity'),
            ];
        });
        
        // Transaction Types Breakdown
        $transactionsByType = InventoryTransaction::approved()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select('transaction_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('transaction_type')
            ->get();
        
        // Average Stock Value per Product
        $avgStockValuePerProduct = $totalProducts > 0 ? $totalStockValue / $totalProducts : 0;
        
        // Stock Turnover Rate (simplified: total sales quantity / average stock)
        $totalSalesQuantity = DB::table('sales_details')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->sum('sales_details.quantity');
        
        $avgStockQuantity = $totalStockQuantity > 0 ? $totalStockQuantity : 1;
        $stockTurnoverRate = $avgStockQuantity > 0 ? ($totalSalesQuantity / $avgStockQuantity) : 0;
        
        return view('inventory.dashboard', compact(
            'totalStockValue',
            'totalStockQuantity',
            'totalProducts',
            'lowStockProducts',
            'lowStockCount',
            'lowStockValue',
            'outOfStockProducts',
            'stockIn',
            'stockOut',
            'netStockMovement',
            'totalTransactionValue',
            'transactionCount',
            'topProductsByValue',
            'topProductsByQuantity',
            'productsByCategory',
            'recentTransactions',
            'stockTrend',
            'transactionsByType',
            'avgStockValuePerProduct',
            'stockTurnoverRate',
            'startDate',
            'endDate'
        ));
    }
}

