<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Get Profit & Loss Report Data (JSON)
     */
    public function profitLoss(Request $request)
    {
        $data = $this->getProfitLossData($request);
        return response()->json($data);
    }

    /**
     * Download Profit & Loss Report (PDF)
     */
    public function downloadProfitLoss(Request $request)
    {
        $data = $this->getProfitLossData($request);
        
        // Use the same view as web, but we might need to adjust it for PDF if needed.
        // For now, we reuse the existing view 'finance.reports.profit-loss'
        // Ideally, we should have a print-specific view or ensure the existing one works well with dompdf.
        // The existing view has @media print styles, which is good.
        
        $pdf = Pdf::loadView('finance.reports.pdf.profit-loss', $data);
        return $pdf->download('profit-loss-report.pdf');
    }

    /**
     * Get Cash Flow Report Data (JSON)
     */
    public function cashFlow(Request $request)
    {
        $data = $this->getCashFlowData($request);
        return response()->json($data);
    }

    /**
     * Download Cash Flow Report (PDF)
     */
    public function downloadCashFlow(Request $request)
    {
        $data = $this->getCashFlowData($request);
        
        $pdf = Pdf::loadView('finance.reports.pdf.cash-flow', $data);
        return $pdf->download('cash-flow-report.pdf');
    }

    /**
     * Shared logic for Profit & Loss
     */
    private function getProfitLossData(Request $request)
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
        // For API, we might want a simpler format, but let's keep it consistent
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category');
        
        $totalExpenses = $expensesByCategory->sum();
        
        $netProfit = $grossProfit - $totalExpenses;
        
        // Profit margin
        $profitMargin = $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0;
        
        $businessProfile = optional(auth()->user())->businessProfile;

        return [
            'salesRevenue' => (float) $salesRevenue,
            'salesReturns' => (float) $salesReturns,
            'netRevenue' => (float) $netRevenue,
            'cogs' => (float) $cogs,
            'grossProfit' => (float) $grossProfit,
            'expensesByCategory' => $expensesByCategory->map(fn($val) => (float)$val),
            'totalExpenses' => (float) $totalExpenses,
            'netProfit' => (float) $netProfit,
            'profitMargin' => (float) $profitMargin,
            'businessProfile' => $businessProfile,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    /**
     * Shared logic for Cash Flow
     */
    private function getCashFlowData(Request $request)
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
        
        $businessProfile = optional(auth()->user())->businessProfile;

        return [
            'cashSales' => (float) $cashSales,
            'creditCardSales' => (float) $creditCardSales,
            'bankTransfers' => (float) $bankTransfers,
            'eWalletSales' => (float) $eWalletSales,
            'totalInflows' => (float) $totalInflows,
            'cashExpenses' => (float) $cashExpenses,
            'cardExpenses' => (float) $cardExpenses,
            'bankExpenses' => (float) $bankExpenses,
            'eWalletExpenses' => (float) $eWalletExpenses,
            'totalOutflows' => (float) $totalOutflows,
            'netCashFlow' => (float) $netCashFlow,
            'openingBalance' => (float) $openingBalance,
            'closingBalance' => (float) $closingBalance,
            'businessProfile' => $businessProfile,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
}
