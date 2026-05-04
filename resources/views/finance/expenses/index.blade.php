<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-900 dark:text-white leading-tight">
                {{ __('Expense Management') }}
            </h2>
            <a href="{{ route('finance.expenses.create') }}"
                class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-gray-100 font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Add Expense</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6"
                x-data="expenseFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()"
                            placeholder="Description, vendor..."
                            class="w-full bg-white border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent focus:outline-none"
                            autocomplete="off" spellcheck="false">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Category</label>
                        <select x-model="filters.category" @change="applyFilters()"
                            class="w-full bg-white border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Categories</option>
                            <option value="office_supplies">Office Supplies</option>
                            <option value="marketing">Marketing</option>
                            <option value="utilities">Utilities</option>
                            <option value="rent">Rent</option>
                            <option value="salaries">Salaries</option>
                            <option value="transportation">Transportation</option>
                            <option value="meals">Meals</option>
                            <option value="equipment">Equipment</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Payment</label>
                        <select x-model="filters.payment_method" @change="applyFilters()"
                            class="w-full bg-white border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="e_wallet">E-Wallet</option>
                        </select>
                    </div>

                    <!-- Recurring Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Recurring</label>
                        <select x-model="filters.recurring" @change="applyFilters()"
                            class="w-full bg-white border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Expenses</option>
                            <option value="yes">Recurring Only</option>
                            <option value="no">One-time Only</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Date
                            Range</label>
                        <input type="date" x-model="filters.date_from" @change="applyFilters()"
                            class="w-full bg-white border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent mb-2"
                            placeholder="From date">
                        <input type="date" x-model="filters.date_to" @change="applyFilters()"
                            class="w-full bg-white border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent"
                            placeholder="To date">
                    </div>
                </div>

                <!-- Loading indicator -->
                <div x-show="Alpine.store('app').loading" x-transition
                    class="mt-4 flex items-center justify-center bg-gray-100 dark:bg-gray-800/80 backdrop-blur-sm rounded-lg py-3 px-4 border border-gray-300 dark:border-gray-600">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-[#006BFF] border-t-transparent"></div>
                    <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium"
                        x-text="Alpine.store('app').loadingMessage || 'Loading...'">Applying filters...</span>
                </div>
            </div>

            <!-- Expenses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($expenses as $expense)
                    <div
                        class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-all duration-300">
                        <!-- Expense Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-gray-900 dark:text-white font-semibold text-lg mb-1">
                                    {{ $expense->description }}</h3>
                                <p class="text-gray-800 text-sm">{{ $expense->expense_date->format('M d, Y') }}</p>
                            </div>
                            @if($expense->receipt_path)
                                <div class="ml-2">
                                    <svg class="w-5 h-5 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <p class="text-2xl font-bold text-red-400">-Rp
                                {{ number_format($expense->amount, 0, ',', '.') }}</p>
                        </div>

                        <!-- Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-800">Category:</span>
                                <span
                                    class="px-2 py-1 text-xs rounded-full bg-purple-900/50 text-purple-100 border border-purple-500/30">
                                    {{ ucwords(str_replace('_', ' ', $expense->category)) }}
                                </span>
                            </div>

                            @if($expense->vendor)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-800">Vendor:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $expense->vendor }}</span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-800">Payment:</span>
                                <span
                                    class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-500/30">
                                    {{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}
                                </span>
                            </div>

                            @if($expense->is_recurring)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-800">Recurring:</span>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 border border-yellow-500/30">
                                        {{ ucfirst($expense->recurring_period) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('finance.expenses.show', $expense) }}"
                                class="flex-1 bg-gray-600 hover:bg-gray-500 text-gray-100 dark:text-white text-sm font-medium py-2 px-3 rounded-lg text-center transition-all duration-200">
                                View
                            </a>
                            <a href="{{ route('finance.expenses.edit', $expense) }}"
                                class="flex-1 bg-blue-600 hover:bg-blue-500 text-gray-100 dark:text-white text-sm font-medium py-2 px-3 rounded-lg text-center transition-all duration-200">
                                Edit
                            </a>
                            <button type="button"
                                onclick="openDeleteModal('{{ route('finance.expenses.destroy', $expense) }}', 'Delete Expense', 'Are you sure you want to delete this expense? This will permanently remove the record and any associated receipts.')"
                                class="flex-1 bg-red-600 hover:bg-red-500 text-gray-100 dark:text-white text-sm font-medium py-2 px-3 rounded-lg transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div
                            class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z">
                                </path>
                            </svg>
                            <h3 class="text-gray-900 dark:text-gray-900 dark:text-white text-lg font-medium mb-2">No
                                Expenses Found</h3>
                            <p class="text-gray-400 mb-4">Get started by adding your first expense.</p>
                            <a href="{{ route('finance.expenses.create') }}"
                                class="inline-flex items-center bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Expense
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($expenses->hasPages())
                <div class="mt-8">
                    {{ $expenses->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function expenseFilters() {
                return {
                    filters: {
                        search: '{{ request('search') }}',
                        category: '{{ request('category') }}',
                        payment_method: '{{ request('payment_method') }}',
                        recurring: '{{ request('recurring') }}',
                        date_from: '{{ request('date_from') }}',
                        date_to: '{{ request('date_to') }}'
                    },

                    init() {
                        this.updateFromUrl();
                    },

                    updateFromUrl() {
                        const urlParams = new URLSearchParams(window.location.search);
                        this.filters.search = urlParams.get('search') || '';
                        this.filters.category = urlParams.get('category') || '';
                        this.filters.payment_method = urlParams.get('payment_method') || '';
                        this.filters.recurring = urlParams.get('recurring') || '';
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