<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display unified transaction listing
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'creator', 'approver'])
                                    ->latest('transaction_date');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('sku', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('transaction_date', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $transactions = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => InventoryTransaction::count(),
            'pending' => InventoryTransaction::pending()->count(),
            'approved' => InventoryTransaction::approved()->count(),
            'total_value' => InventoryTransaction::approved()->sum('total_cost') ?? 0,
        ];

        $products = Product::active()->orderBy('name')->get();

        return view('inventory.transactions.index', compact('transactions', 'stats', 'products'));
    }



    /**
     * Create new transaction
     */
    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        
        $transactionTypes = [
            'adjustment_increase' => 'Stock Increase',
            'adjustment_decrease' => 'Stock Decrease',
            'damaged' => 'Damaged Items',
            'expired' => 'Expired Items',
            'initial_stock' => 'Initial Stock',
        ];

        $reasons = [
            'correction' => 'Stock Correction',
            'inventory_count' => 'Physical Inventory Count',
            'damage' => 'Damaged',
            'loss' => 'Loss/Missing',
            'theft' => 'Theft',
            'expired' => 'Expired',
            'other' => 'Other',
        ];

        return view('inventory.transactions.create', compact('products', 'transactionTypes', 'reasons'));
    }

    /**
     * Store new transaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'transaction_type' => 'required|in:adjustment_increase,adjustment_decrease,damaged,expired,initial_stock',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'required|in:correction,inventory_count,damage,loss,theft,expired,other',
            'notes' => 'nullable|string|max:1000',
            'transaction_date' => 'required|date',
        ]);

        try {
            $transaction = $this->inventoryService->createTransaction($validated);

            return redirect()->route('inventory.transactions.show', $transaction)
                           ->with('success', 'Transaction created and stock updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                       ->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Show transaction details
     */
    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'creator', 'approver']);
        return view('inventory.transactions.show', compact('transaction'));
    }



    /**
     * Product transaction history
     */
    public function productHistory(Product $product)
    {
        $transactions = $product->inventoryTransactions()
                               ->with(['creator', 'approver'])
                               ->latest('transaction_date')
                               ->paginate(20);

        return view('inventory.transactions.product-history', compact('product', 'transactions'));
    }

    /**
     * Export transactions
     */
    public function export(Request $request)
    {
        $query = InventoryTransaction::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('transaction_date', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $transactions = $query->latest('transaction_date')->get();

        $filename = 'inventory_transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Date',
                'Product',
                'SKU',
                'Transaction Type',
                'Quantity',
                'Stock Before',
                'Stock After',
                'Unit Cost',
                'Total Cost',
                'Status',
                'Reason',
                'Notes',
                'Created By'
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->transaction_date->format('Y-m-d H:i:s'),
                    $transaction->product->name,
                    $transaction->product->sku,
                    $transaction->transaction_type_name,
                    $transaction->quantity,
                    $transaction->stock_before,
                    $transaction->stock_after,
                    $transaction->unit_cost ?? 'N/A',
                    $transaction->total_cost ?? 'N/A',
                    $transaction->status,
                    $transaction->reason_label,
                    $transaction->notes ?? '',
                    $transaction->creator->name ?? 'N/A',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

