<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Inventory Transactions') }}
            </h2>
            <a href="{{ route('inventory.transactions.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#0056cc] text-white font-semibold rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Transaction</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Transactions -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-600">Total Transactions</div>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
                </div>

                <!-- Stock In -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock In</div>
                        <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
                </div>

                <!-- Stock Out -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock Out</div>
                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['approved']) }}</div>
                </div>

                <!-- Total Value -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-600">Total Value</div>
                        <div class="p-2 bg-purple-50 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Filters</h3>
                    <div class="flex gap-3">
                        @if(request()->hasAny(['search', 'transaction_type', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('inventory.transactions.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('inventory.transactions.export', request()->all()) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
                
                <form method="GET" action="{{ route('inventory.transactions.index') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Product name or SKU..." 
                               class="filter-input w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                    </div>

                    <!-- Transaction Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                        <select name="transaction_type" class="filter-select w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="purchase" {{ request('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="sale" {{ request('transaction_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="adjustment_increase" {{ request('transaction_type') == 'adjustment_increase' ? 'selected' : '' }}>Stock Increase</option>
                            <option value="adjustment_decrease" {{ request('transaction_type') == 'adjustment_decrease' ? 'selected' : '' }}>Stock Decrease</option>
                            <option value="damaged" {{ request('transaction_type') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="expired" {{ request('transaction_type') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="filter-select w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="auto_approved" {{ request('status') == 'auto_approved' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="filter-date w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="filter-date w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                    </div>
                </form>
            </div>

            <script>
                // Auto-filter functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('filterForm');
                    let searchTimeout;

                    // Auto-submit for select/date inputs
                    document.querySelectorAll('.filter-select, .filter-date').forEach(element => {
                        element.addEventListener('change', function() {
                            form.submit();
                        });
                    });

                    // Debounced search for text input
                    document.querySelectorAll('.filter-input').forEach(element => {
                        element.addEventListener('input', function() {
                            clearTimeout(searchTimeout);
                            searchTimeout = setTimeout(() => {
                                form.submit();
                            }, 500); // Wait 500ms after user stops typing
                        });
                    });
                });
            </script>

            <!-- Transactions Table -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->transaction_date->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $transaction->product->sku }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if(in_array($transaction->transaction_type, ['purchase', 'return_from_customer', 'adjustment_increase', 'initial_stock'])) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $transaction->transaction_type_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                                        @if(in_array($transaction->transaction_type, ['purchase', 'return_from_customer', 'adjustment_increase', 'initial_stock'])) text-green-600
                                        @else text-red-600
                                        @endif">
                                        {{ $transaction->isIncrease() ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($transaction->stock_before) }} → {{ number_format($transaction->stock_after) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $transaction->status_badge !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->creator->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('inventory.transactions.show', $transaction) }}" 
                                            class="text-[#006BFF] hover:text-[#006BFF]/80 font-medium transition-colors"
                                            title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">No transactions found</p>
                                        <p class="mt-1">Try adjusting your filters or create a new transaction.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

