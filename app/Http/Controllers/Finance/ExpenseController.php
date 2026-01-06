<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
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
            $query->where('is_recurring', $request->recurring === 'yes');
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);
        $categories = Expense::distinct()->whereNotNull('category')->pluck('category')->filter();

        return view('finance.expenses.index', compact('expenses', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|in:office_supplies,marketing,utilities,rent,salaries,transportation,meals,equipment,maintenance,other',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,e_wallet',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:daily,weekly,monthly,yearly',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Validate file
            $request->validate([
                'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Store new receipt with proper validation
            try {
                $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');

                // Verify file was stored successfully
                if (!$validated['receipt_path']) {
                    return back()->withErrors(['receipt' => 'Failed to upload receipt. Please try again.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Expense receipt upload failed: ' . $e->getMessage());
                return back()->withErrors(['receipt' => 'Failed to upload receipt. Please try again.'])->withInput();
            }
        }

        Expense::create($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('finance.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('finance.expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|in:office_supplies,marketing,utilities,rent,salaries,transportation,meals,equipment,maintenance,other',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,e_wallet',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:daily,weekly,monthly,yearly',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Validate file
            $request->validate([
                'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }

            // Store new receipt with proper validation
            try {
                $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');

                // Verify file was stored successfully
                if (!$validated['receipt_path']) {
                    return back()->withErrors(['receipt' => 'Failed to upload receipt. Please try again.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Expense receipt upload failed: ' . $e->getMessage());
                return back()->withErrors(['receipt' => 'Failed to upload receipt. Please try again.'])->withInput();
            }
        }

        $expense->update($validated);

        return redirect()->route('finance.expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}

