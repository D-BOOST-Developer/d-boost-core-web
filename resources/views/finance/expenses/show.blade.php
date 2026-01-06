<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-900 dark:text-white leading-tight">
                {{ __('Expense Details') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('finance.expenses.edit', $expense) }}" 
                   class="bg-blue-600 hover:bg-blue-500 text-gray-100 dark:text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
                </a>
                <a href="{{ route('finance.expenses.index') }}" 
                   class="bg-gray-600 hover:bg-gray-500 text-gray-100 dark:text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Expenses</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Expense Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $expense->description }}</h1>
                                <p class="text-gray-900 dark:text-white">{{ $expense->expense_date->format('F d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-red-400 mb-2">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                                @if($expense->is_recurring)
                                    <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 border border-yellow-500/30">
                                        Recurring {{ ucfirst($expense->recurring_period) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-900 dark:text-white text-sm">Category</p>
                                    <span class="px-3 py-1 text-sm rounded-full bg-purple-900/50 text-purple-400 border border-purple-500/30">
                                        {{ ucwords(str_replace('_', ' ', $expense->category)) }}
                                    </span>
                                </div>

                                @if($expense->vendor)
                                    <div>
                                        <p class="text-gray-900 dark:text-white text-sm">Vendor</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $expense->vendor }}</p>
                                    </div>
                                @endif

                                <div>
                                    <p class="text-gray-900 dark:text-white text-sm">Payment Method</p>
                                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-500/30">
                                        {{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-900 dark:text-white text-sm">Expense Date</p>
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $expense->expense_date->format('M d, Y') }}</p>
                                </div>

                                <div>
                                    <p class="text-gray-900 dark:text-white text-sm">Created</p>
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $expense->created_at->format('M d, Y H:i') }}</p>
                                </div>

                                @if($expense->updated_at != $expense->created_at)
                                    <div>
                                        <p class="text-gray-900 dark:text-white text-sm">Last Updated</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $expense->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Receipt -->
                    @if($expense->receipt_path)
                        <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Receipt</h3>
                            <div class="text-center">
                                <img src="{{ Storage::url($expense->receipt_path) }}" 
                                     alt="Receipt for {{ $expense->description }}" 
                                     class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                                <div class="mt-4">
                                    <a href="{{ Storage::url($expense->receipt_path) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center bg-[#006BFF] hover:bg-[#006BFF]/80 text-gray-900 font-medium py-2 px-4 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        View Full Size
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Expense Summary -->
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Summary</h3>
                        <div class="space-y-4">
                            <div class="text-center p-4 bg-red-900/20 rounded-lg border border-red-500/30">
                                <p class="text-3xl font-bold text-red-400">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                                <p class="text-red-700 text-sm">Total Amount</p>
                            </div>

                            @if($expense->is_recurring)
                                <div class="text-center p-4 bg-yellow-900/20 rounded-lg border border-yellow-500/30">
                                    <p class="text-xl font-bold text-yellow-400">{{ ucfirst($expense->recurring_period) }}</p>
                                    <p class="text-yellow-300 text-sm">Recurring Period</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Category Information -->
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Category Details</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-gray-900 dark:text-white text-sm">Category</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ ucwords(str_replace('_', ' ', $expense->category)) }}</p>
                            </div>
                            
                            <div>
                                <p class="text-gray-900 dark:text-white text-sm">This Month's Total</p>
                                @php
                                    $monthlyTotal = \App\Models\Expense::where('category', $expense->category)
                                        ->whereYear('expense_date', $expense->expense_date->year)
                                        ->whereMonth('expense_date', $expense->expense_date->month)
                                        ->sum('amount');
                                @endphp
                                <p class="text-red-400 font-medium">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</p>
                            </div>

                            <div>
                                <p class="text-gray-900 dark:text-white text-sm">Category Percentage</p>
                                @php
                                    $totalExpenses = \App\Models\Expense::whereYear('expense_date', $expense->expense_date->year)
                                        ->whereMonth('expense_date', $expense->expense_date->month)
                                        ->sum('amount');
                                    $percentage = $totalExpenses > 0 ? ($monthlyTotal / $totalExpenses) * 100 : 0;
                                @endphp
                                <p class="text-gray-900 dark:text-white font-medium">{{ number_format($percentage, 1) }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Record Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-900 dark:text-white">Created by</p>
                                <p class="text-gray-900 dark:text-white">{{ $expense->creator->name ?? 'System' }}</p>
                                <p class="text-gray-500 text-xs">{{ $expense->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @if($expense->updated_at != $expense->created_at)
                                <div>
                                    <p class="text-gray-900 dark:text-white">Last updated by</p>
                                    <p class="text-gray-900 dark:text-white">{{ $expense->updater->name ?? 'System' }}</p>
                                    <p class="text-gray-900 dark:text-white text-xs">{{ $expense->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('finance.expenses.edit', $expense) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-500 text-gray-100 dark:text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 text-center block">
                                Edit Expense
                            </a>
                            
                            @if($expense->receipt_path)
                                <a href="{{ Storage::url($expense->receipt_path) }}" 
                                   target="_blank"
                                   class="w-full bg-gray-600 hover:bg-gray-500 text-gray-100 dark:text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 text-center block">
                                    Download Receipt
                                </a>
                            @endif

                            <button type="button" 
                                    onclick="openDeleteModal('{{ route('finance.expenses.destroy', $expense) }}', 'Delete Expense', 'Are you sure you want to delete this expense? This will permanently remove the record and any associated receipts.')"
                                    class="w-full bg-red-600 hover:bg-red-500 text-gray-100 dark:text-white font-medium py-2 px-4 rounded-lg transition-all duration-200">
                                Delete Expense
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
