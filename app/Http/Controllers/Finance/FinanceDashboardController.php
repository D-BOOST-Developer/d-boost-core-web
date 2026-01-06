<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\SalesReturn;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceDashboardController extends Controller
{
    /**
     * Display the main financial dashboard
     */
    public function index(Request $request)
    {
        // Handle AJAX request for chart data - optimized for period
        if ($request->ajax() && $request->has('chart_period')) {
            $period = (int) $request->input('chart_period', 7);
            $endDate = now()->format('Y-m-d');
            $startDate = now()->subDays($period - 1)->format('Y-m-d');
            
            // Fetch all data in one query
            $revenueData = Sale::completed()
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(sale_date) as date'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            $expenseData = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(expense_date) as date'),
                    DB::raw('SUM(amount) as expenses')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            // Generate complete date range
            $data = collect(range($period - 1, 0))->map(function ($daysAgo) use ($revenueData, $expenseData) {
                $date = now()->subDays($daysAgo)->format('Y-m-d');
                return [
                    'date' => $date,
                    'revenue' => (float) ($revenueData[$date]->revenue ?? 0),
                    'expenses' => (float) ($expenseData[$date]->expenses ?? 0)
                ];
            });
            
            return response()->json($data->values());
        }

        // Default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Revenue (completed sales)
        $revenue = Sale::completed()
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        // Profit
        $profit = $revenue - $expenses;
        
        // Accounts Receivable (pending sales)
        $accountsReceivable = Sale::where('status', 'pending')
            ->sum('total_amount');
        
        // Cash on Hand (all completed sales - all expenses)
        $cashOnHand = Sale::completed()->sum('total_amount') - Expense::sum('amount');
        
        // Sales count
        $salesCount = Sale::completed()
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->count();
        
        // Expense count
        $expenseCount = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->count();
        
        // Average sale value
        $avgSaleValue = $salesCount > 0 ? $revenue / $salesCount : 0;
        
        // Top selling products
        $topProducts = DB::table('sales_details')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->select(
                'products.name',
                DB::raw('SUM(sales_details.quantity) as total_quantity'),
                DB::raw('SUM(sales_details.quantity * sales_details.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        // Revenue and Expenses trend (last 7 days) - ensure all days are included
        $revenueTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return (object) [
                'date' => $date,
                'revenue' => Sale::completed()
                    ->whereDate('sale_date', $date)
                    ->sum('total_amount'),
                'expenses' => Expense::whereDate('expense_date', $date)
                    ->sum('amount')
            ];
        });
        
        // Expense breakdown by category
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
        
        // Recent transactions (last 10)
        $recentSales = Sale::with('customer')
            ->latest('sale_date')
            ->limit(5)
            ->get();
        
        $recentExpenses = Expense::latest('expense_date')
            ->limit(5)
            ->get();
        
        // Pending payments (accounts receivable details)
        $pendingPayments = Sale::with('customer')
            ->where('status', 'pending')
            ->orderBy('sale_date')
            ->get();
        
        return view('finance.dashboard', compact(
            'revenue',
            'expenses',
            'profit',
            'accountsReceivable',
            'cashOnHand',
            'salesCount',
            'expenseCount',
            'avgSaleValue',
            'topProducts',
            'revenueTrend',
            'expensesByCategory',
            'recentSales',
            'recentExpenses',
            'pendingPayments',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Generate real-time Profit & Loss report
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Revenue
        $salesRevenue = Sale::completed()
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        // Sales Returns
        $salesReturns = SalesReturn::completed()
            ->whereBetween('return_date', [$startDate, $endDate])
            ->sum('refund_amount');
        
        $netRevenue = $salesRevenue - $salesReturns;
        
        // Cost of Goods Sold (from sales details)
        $cogs = DB::table('sales_details')
            ->join('sales', 'sales_details.sale_id', '=', 'sales.id')
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->sum(DB::raw('sales_details.quantity * products.cost'));
        
        $grossProfit = $netRevenue - $cogs;
        
        // Operating Expenses by category
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category');
        
        $totalExpenses = $expensesByCategory->sum();
        
        $netProfit = $grossProfit - $totalExpenses;
        
        // Profit margin
        $profitMargin = $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0;
        
        $businessProfile = auth()->user()->businessProfile;

        return view('finance.reports.profit-loss', compact(
            'salesRevenue',
            'salesReturns',
            'netRevenue',
            'cogs',
            'grossProfit',
            'expensesByCategory',
            'totalExpenses',
            'netProfit',
            'profitMargin',
            'businessProfile',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Generate real-time Cash Flow report
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Cash Inflows
        $cashSales = Sale::completed()
            ->where('payment_method', 'cash')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        $creditCardSales = Sale::completed()
            ->whereIn('payment_method', ['credit_card', 'debit_card'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        $bankTransfers = Sale::completed()
            ->where('payment_method', 'bank_transfer')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        $eWalletSales = Sale::completed()
            ->where('payment_method', 'e_wallet')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        $totalInflows = $cashSales + $creditCardSales + $bankTransfers + $eWalletSales;
        
        // Cash Outflows (expenses by payment method)
        $cashExpenses = Expense::where('payment_method', 'cash')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $cardExpenses = Expense::whereIn('payment_method', ['credit_card', 'debit_card'])
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $bankExpenses = Expense::where('payment_method', 'bank_transfer')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $eWalletExpenses = Expense::where('payment_method', 'e_wallet')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $totalOutflows = $cashExpenses + $cardExpenses + $bankExpenses + $eWalletExpenses;
        
        $netCashFlow = $totalInflows - $totalOutflows;
        
        // Opening balance (cash flow before start date)
        $openingBalance = Sale::completed()
            ->where('sale_date', '<', $startDate)
            ->sum('total_amount') - Expense::where('expense_date', '<', $startDate)->sum('amount');
        
        $closingBalance = $openingBalance + $netCashFlow;
        
        $businessProfile = auth()->user()->businessProfile;

        return view('finance.reports.cash-flow', compact(
            'cashSales',
            'creditCardSales',
            'bankTransfers',
            'eWalletSales',
            'totalInflows',
            'cashExpenses',
            'cardExpenses',
            'bankExpenses',
            'eWalletExpenses',
            'totalOutflows',
            'netCashFlow',
            'openingBalance',
            'closingBalance',
            'businessProfile',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Display accounts receivable (pending payments)
     */
    public function accountsReceivable(Request $request)
    {
        $query = Sale::with('customer')
            ->where('status', 'pending')
            ->orderBy('sale_date');
        
        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        $pendingPayments = $query->get();
        
        // Aging analysis
        $aging = [
            '0-30' => $pendingPayments->filter(function ($sale) {
                return $sale->sale_date->diffInDays(now()) <= 30;
            })->sum('total_amount'),
            '31-60' => $pendingPayments->filter(function ($sale) {
                $days = $sale->sale_date->diffInDays(now());
                return $days > 30 && $days <= 60;
            })->sum('total_amount'),
            '60+' => $pendingPayments->filter(function ($sale) {
                return $sale->sale_date->diffInDays(now()) > 60;
            })->sum('total_amount'),
        ];
        
        $totalReceivable = $pendingPayments->sum('total_amount');
        
        return view('finance.accounts-receivable', compact(
            'pendingPayments',
            'aging',
            'totalReceivable'
        ));
    }
}
