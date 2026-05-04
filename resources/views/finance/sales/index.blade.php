<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Sales Management') }}
            </h2>
            <a href="{{ route('finance.sales.create') }}"
                class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-gray-100 font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Sale</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6"
                x-data="salesFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()"
                            placeholder="Invoice or customer..."
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent"
                            autocomplete="off" spellcheck="false">
                    </div>

                    <!-- Order Source Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Source</label>
                        <select x-model="filters.order_source" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Sources</option>
                            <option value="walk_in">Walk-in</option>
                            <option value="storefront">Storefront</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment</label>
                        <select x-model="filters.payment_method" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="e_wallet">E-Wallet</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                        <input type="date" x-model="filters.date_from" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent mb-2">

                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                        <input type="date" x-model="filters.date_to" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent"
                            placeholder="To date">
                    </div>
                </div>

                <!-- Loading indicator -->
                <div x-show="Alpine.store('app').loading" x-transition
                    class="mt-4 flex items-center justify-center bg-blue-50 dark:bg-gray-800/80 rounded-lg py-3 px-4 border border-blue-200 dark:border-gray-600">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-[#006BFF] border-t-transparent"></div>
                    <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium"
                        x-text="Alpine.store('app').loadingMessage || 'Loading...'">Applying filters...</span>
                </div>
            </div>

            <!-- Sales Table -->
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                @if($sales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Invoice</th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Items</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Total</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Payment</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Source</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($sales as $sale)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-200">
                                                        <!-- Invoice -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $sale->invoice_number }}</div>
                                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                                {{ $sale->created_at->format('H:i') }}</div>
                                                        </td>



                                                        <!-- Date -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900 dark:text-white">
                                                                {{ $sale->sale_date->format('M d, Y') }}</div>
                                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                                {{ $sale->sale_date->diffForHumans() }}</div>
                                                        </td>

                                                        <!-- Items -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900 dark:text-white">{{ $sale->total_items }} items
                                                            </div>
                                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                                {{ $sale->salesDetails->count() }} products</div>
                                                        </td>

                                                        <!-- Total -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-[#006BFF]">Rp
                                                                {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
                                                            @if($sale->discount_amount > 0)
                                                                <div class="text-sm text-gray-600 dark:text-gray-400">Disc: Rp
                                                                    {{ number_format($sale->discount_amount, 0, ',', '.') }}</div>
                                                            @endif
                                                        </td>

                                                        <!-- Payment Method -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30">
                                                                {{ $sale->payment_method ? ucwords(str_replace('_', ' ', $sale->payment_method)) : '-' }}
                                                            </span>
                                                        </td>

                                                        <!-- Source -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @include('finance.sales._source_badge', ['sale' => $sale])
                                                        </td>

                                                        <!-- Status -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @include('finance.sales._status_badge', ['sale' => $sale])
                                                        </td>

                                                        <!-- Actions -->
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('finance.sales.show', $sale) }}"
                                                                    class="text-gray-500 hover:text-[#006BFF] dark:text-gray-400 dark:hover:text-[#006BFF] transition-colors duration-200">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                        </path>
                                                                    </svg>
                                                                </a>
                                                                <a href="{{ route('finance.sales.print', $sale) }}" target="_blank"
                                                                    class="text-gray-500 hover:text-[#006BFF] dark:text-gray-400 dark:hover:text-[#006BFF] transition-colors duration-200"
                                                                    title="Print Invoice">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                                        </path>
                                                                    </svg>
                                                                </a>
                                                                <a href="{{ route('finance.sales.edit', $sale) }}"
                                                                    class="text-gray-400 hover:text-blue-400 transition-colors duration-200">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                        </path>
                                                                    </svg>
                                                                </a>
                                                                <button type="button"
                                                                    onclick="openDeleteModal('{{ route('finance.sales.destroy', $sale) }}', 'Delete Sale', 'Are you sure you want to delete sale {{ $sale->invoice_number }}? This action cannot be undone.')"
                                                                    class="text-gray-400 hover:text-red-400 transition-colors duration-200">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <h3 class="text-gray-900 dark:text-white text-lg font-medium mb-2">No Sales Found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first sale.</p>
                        <a href="{{ route('finance.sales.create') }}"
                            class="inline-flex items-center bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Sale
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($sales->hasPages())
                <div class="mt-6">
                    {{ $sales->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function salesFilters() {
                return {
                    filters: {
                        search: '{{ request('search') }}',
                        order_source: '{{ request('order_source') }}',
                        status: '{{ request('status') }}',
                        payment_method: '{{ request('payment_method') }}',
                        date_from: '{{ request('date_from') }}',
                        date_to: '{{ request('date_to') }}'
                    },

                    init() {
                        this.updateFromUrl();
                    },

                    updateFromUrl() {
                        const urlParams = new URLSearchParams(window.location.search);
                        this.filters.search = urlParams.get('search') || '';
                        this.filters.order_source = urlParams.get('order_source') || '';
                        this.filters.status = urlParams.get('status') || '';
                        this.filters.payment_method = urlParams.get('payment_method') || '';
                        this.filters.date_from = urlParams.get('date_from') || '';
                        this.filters.date_to = urlParams.get('date_to') || '';
                    },

                    applyFilters() {
                        // Use global loading state
                        Alpine.store('app').showLoading('Applying filters...');

                        // Small delay to show loading indicator before redirect
                        setTimeout(() => {
                            // Build query string
                            const params = new URLSearchParams();

                            Object.keys(this.filters).forEach(key => {
                                if (this.filters[key]) {
                                    params.set(key, this.filters[key]);
                                }
                            });

                            // Update URL and reload page
                            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                            window.location.href = newUrl;
                        }, 200); // 200ms delay to show loading indicator
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>