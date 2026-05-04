<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Get list of expenses with optional filters
     */
    public function index(Request $request)
    {
        $query = Expense::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Recurring filter
        if ($request->filled('recurring')) {
            if ($request->recurring === 'yes') {
                $query->where('is_recurring', true);
            } elseif ($request->recurring === 'no') {
                $query->where('is_recurring', false);
            }
        }

        $perPage = $request->get('per_page', 20);
        $expenses = $query->orderBy('expense_date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($perPage);

        return response()->json([
            'data' => $expenses->items(),
            'meta' => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
            ]
        ]);
    }

    /**
     * Get single expense by ID
     */
    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    /**
     * Create a new expense
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|in:office_supplies,marketing,utilities,rent,salaries,transportation,meals,equipment,maintenance,other',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,e_wallet',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:daily,weekly,monthly,yearly',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            try {
                $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
            } catch (\Exception $e) {
                \Log::error('Expense receipt upload failed: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to upload receipt',
                    'error' => $e->getMessage()
                ], 422);
            }
        }

        // Use description field if title is provided (for backward compatibility)
        if (!isset($validated['description']) && isset($validated['title'])) {
            $validated['description'] = $validated['title'];
        }

        $expense = Expense::create($validated);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense->fresh()
        ], 201);
    }

    /**
     * Update expense
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|in:office_supplies,marketing,utilities,rent,salaries,transportation,meals,equipment,maintenance,other',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,e_wallet',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:daily,weekly,monthly,yearly',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }

            try {
                $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
            } catch (\Exception $e) {
                \Log::error('Expense receipt upload failed: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to upload receipt',
                    'error' => $e->getMessage()
                ], 422);
            }
        }

        // Use description field if title is provided (for backward compatibility)
        if (!isset($validated['description']) && isset($validated['title'])) {
            $validated['description'] = $validated['title'];
        }

        $expense->update($validated);

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense->fresh()
        ]);
    }

    /**
     * Delete expense
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully'
        ]);
    }

    /**
     * Get expense categories
     */
    public function categories()
    {
        $categories = Expense::getCategories();
        return response()->json($categories);
    }

    /**
     * Get expense statistics
     */
    public function stats(Request $request)
    {
        $query = Expense::query();

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $total = $query->sum('amount');
        $count = $query->count();
        $average = $count > 0 ? $total / $count : 0;

        // Category breakdown
        $categoryBreakdown = $query->clone()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'total' => (float) $item->total
                ];
            });

        return response()->json([
            'total' => (float) $total,
            'count' => $count,
            'average' => (float) $average,
            'category_breakdown' => $categoryBreakdown,
        ]);
    }
}

