<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Create a new inventory transaction
     * All transactions are auto-approved for simplicity
     */
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $product = Product::withoutGlobalScope('user')->findOrFail($data['product_id']);

            // Determine if this is an increase or decrease
            $isIncrease = $this->isIncreaseType($data['transaction_type']);

            // Validate stock availability for decrease transactions
            if (!$isIncrease) {
                $this->validateStockAvailability($data['product_id'], $data['quantity']);
            }

            // Calculate stock before/after
            $stockBefore = $product->stock_quantity;
            $stockAfter = $isIncrease 
                ? $stockBefore + $data['quantity']
                : $stockBefore - $data['quantity'];

            // Create transaction (always auto-approved)
            $transaction = InventoryTransaction::create([
                'id' => Str::uuid(),
                'product_id' => $data['product_id'],
                'transaction_type' => $data['transaction_type'],
                'quantity' => $data['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => $data['unit_cost'] ?? null,
                'total_cost' => isset($data['unit_cost']) 
                    ? $data['unit_cost'] * $data['quantity'] 
                    : null,
                'reference_type' => $data['reference_type'] ?? 'manual',
                'reference_id' => $data['reference_id'] ?? null,
                'status' => 'auto_approved', // Always auto-approve
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
            ]);

            // Update product stock immediately
            if ($isIncrease) {
                $product->increment('stock_quantity', $data['quantity']);
            } else {
                $product->decrement('stock_quantity', $data['quantity']);
            }

            return $transaction;
        });
    }

    /**
     * Check if transaction type increases stock
     */
    protected function isIncreaseType($transactionType)
    {
        return in_array($transactionType, [
            'purchase',
            'return_from_customer',
            'adjustment_increase',
            'transfer_in',
            'initial_stock',
        ]);
    }

    /**
     * Get stock summary
     */
    public function getStockSummary()
    {
        return [
            'total_products' => Product::count(),
            'total_stock_value' => Product::sum(DB::raw('stock_quantity * cost')),
            'low_stock_products' => Product::lowStock()->count(),
            'out_of_stock_products' => Product::whereRaw('stock_quantity <= 0')->count(),
            'pending_approvals' => InventoryTransaction::pending()->count(),
        ];
    }

    /**
     * Get stock movement report
     */
    public function getStockMovementReport($dateFrom, $dateTo, $productId = null)
    {
        $query = InventoryTransaction::approved()
                                    ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->with('product')
                    ->orderBy('transaction_date')
                    ->get()
                    ->groupBy('transaction_type')
                    ->map(function ($transactions, $type) {
                        return [
                            'type' => $type,
                            'count' => $transactions->count(),
                            'quantity' => $transactions->sum('quantity'),
                            'value' => $transactions->sum('total_cost'),
                        ];
                    });
    }

    /**
     * Validate stock availability
     */
    public function validateStockAvailability($productId, $quantity)
    {
        $product = Product::withoutGlobalScope('user')->findOrFail($productId);
        
        if ($product->stock_quantity < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$product->stock_quantity}, Required: {$quantity}");
        }

        return true;
    }

    /**
     * Get transaction statistics for dashboard
     */
    public function getTransactionStats($dateFrom = null, $dateTo = null)
    {
        $query = InventoryTransaction::query();

        if ($dateFrom && $dateTo) {
            $query->whereBetween('transaction_date', [$dateFrom, $dateTo]);
        }

        $stats = [
            'total_transactions' => $query->count(),
            'pending_approvals' => $query->where('status', 'pending')->count(),
            'total_stock_in' => $query->whereIn('transaction_type', ['purchase', 'return_from_customer', 'adjustment_increase', 'transfer_in', 'initial_stock'])->sum('quantity'),
            'total_stock_out' => $query->whereIn('transaction_type', ['sale', 'return_to_supplier', 'adjustment_decrease', 'transfer_out', 'damaged', 'expired'])->sum('quantity'),
            'total_value' => $query->approved()->sum('total_cost'),
        ];

        return $stats;
    }
}

