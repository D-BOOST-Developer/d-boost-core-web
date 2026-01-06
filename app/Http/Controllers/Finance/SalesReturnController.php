<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of sales returns
     */
    public function index(Request $request)
    {
        $query = SalesReturn::with(['sale.customer', 'creator'])
                           ->latest('return_date');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('sale.customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reason
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('return_date', [$request->date_from, $request->date_to]);
        }

        $salesReturns = $query->paginate(20);

        return view('finance.sales-returns.index', compact('salesReturns'));
    }

    /**
     * Show the form for creating a new return
     */
    public function create(Request $request)
    {
        $saleId = $request->query('sale_id');
        $sale = $saleId ? Sale::with('salesDetails.product', 'customer')->find($saleId) : null;

        return view('finance.sales-returns.create', compact('sale'));
    }

    /**
     * Store a newly created return
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'reason' => 'required|in:defective,wrong_item,customer_request,other',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals
        $totalReturnAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalReturnAmount += $item['quantity'] * $item['unit_price'];
        }

        $return = SalesReturn::create([
            'sale_id' => $validated['sale_id'],
            'return_date' => $validated['return_date'],
            'reason' => $validated['reason'],
            'total_return_amount' => $totalReturnAmount,
            'refund_amount' => $totalReturnAmount,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Create items
        foreach ($validated['items'] as $item) {
            SalesReturnItem::create([
                'sales_return_id' => $return->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('finance.sales-returns.show', $return)
                       ->with('success', 'Sales return created successfully');
    }

    /**
     * Show return details
     */
    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['sale.customer', 'items.product', 'creator']);
        return view('finance.sales-returns.show', compact('salesReturn'));
    }

    /**
     * Show the form for editing
     */
    public function edit(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be edited');
        }

        $salesReturn->load('items.product');
        return view('finance.sales-returns.edit', compact('salesReturn'));
    }

    /**
     * Update the return
     */
    public function update(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be updated');
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
            'reason' => 'required|in:defective,wrong_item,customer_request,other',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals
        $totalReturnAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalReturnAmount += $item['quantity'] * $item['unit_price'];
        }

        $salesReturn->update([
            'return_date' => $validated['return_date'],
            'reason' => $validated['reason'],
            'total_return_amount' => $totalReturnAmount,
            'refund_amount' => $totalReturnAmount,
            'notes' => $validated['notes'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        // Update items
        $salesReturn->items()->delete();
        foreach ($validated['items'] as $item) {
            SalesReturnItem::create([
                'sales_return_id' => $salesReturn->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('finance.sales-returns.show', $salesReturn)
                       ->with('success', 'Sales return updated successfully');
    }

    /**
     * Complete the return (instant process)
     * Simplified workflow: directly complete and restock from pending
     */
    public function complete(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be completed');
        }

        $salesReturn->complete();

        return redirect()->route('finance.sales-returns.show', $salesReturn)
                       ->with('success', 'Sales return completed and inventory restocked successfully');
    }
    
    /**
     * Cancel the return
     */
    public function cancel(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be cancelled');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string',
        ]);

        $salesReturn->cancel($validated['cancellation_reason'] ?? null);

        return redirect()->route('finance.sales-returns.show', $salesReturn)
                       ->with('success', 'Sales return cancelled successfully');
    }

    /**
     * Get sales return report
     */
    public function report(Request $request)
    {
        $query = SalesReturn::with(['sale.customer', 'items.product']);

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('return_date', [$request->date_from, $request->date_to]);
        }

        $salesReturns = $query->latest('return_date')->get();

        // Calculate summary
        $summary = [
            'total_returns' => $salesReturns->count(),
            'total_return_amount' => $salesReturns->sum('total_return_amount'),
            'total_refund_amount' => $salesReturns->sum('refund_amount'),
            'completed_returns' => $salesReturns->where('status', 'completed')->count(),
            'pending_returns' => $salesReturns->where('status', 'pending')->count(),
            'cancelled_returns' => $salesReturns->where('status', 'cancelled')->count(),
        ];

        // Group by reason
        $byReason = $salesReturns->groupBy('reason')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('total_return_amount'),
            ];
        });

        return view('finance.sales-returns.report', compact('salesReturns', 'summary', 'byReason'));
    }

    /**
     * Delete the return
     */
    public function destroy(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be deleted');
        }

        $salesReturn->delete();

        return redirect()->route('finance.sales-returns.index')
                       ->with('success', 'Sales return deleted successfully');
    }
}
