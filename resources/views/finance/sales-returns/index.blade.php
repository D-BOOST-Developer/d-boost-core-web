<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Sales Returns') }}
            </h2>
            <a href="{{ route('finance.sales-returns.create') }}"
                class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Return</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Returns</p>
                    <p class="text-3xl font-bold text-[#006BFF] mt-2">{{ $salesReturns->total() }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold text-yellow-400 mt-2">
                        {{ $salesReturns->where('status', 'pending')->count() }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Approved</p>
                    <p class="text-3xl font-bold text-green-400 mt-2">
                        {{ $salesReturns->where('status', 'approved')->count() }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp
                        {{ number_format($salesReturns->sum('total_return_amount'), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6"
                x-data="returnFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()"
                            placeholder="Search return number..."
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent focus:outline-none"
                            autocomplete="off">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
                        <select x-model="filters.reason" @change="applyFilters()"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Reasons</option>
                            <option value="defective">Defective</option>
                            <option value="wrong_item">Wrong Item</option>
                            <option value="buyer_request" {{ request('reason') == 'buyer_request' ? 'selected' : '' }}>
                                Buyer Request</option>
                            <option value="other">Other</option>
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
            </div>

        <!-- Sales Returns Table -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Return #</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Return Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Reason</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($salesReturns as $return)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $return->return_number }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $return->return_date->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ ucfirst(str_replace('_', ' ', $return->reason)) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($return->total_return_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                                                    {{ $return->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400' : '' }}
                                                                    {{ $return->status === 'approved' ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : '' }}
                                                                    {{ $return->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' : '' }}
                                                                    {{ $return->status === 'completed' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400' : '' }}">
                                        {{ ucfirst($return->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('finance.sales-returns.show', $return) }}"
                                        class="text-[#006BFF] hover:text-[#006BFF]/80 transition-colors"
                                        title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                                    </svg>
                                    No sales returns found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $salesReturns->links() }}
        </div>
    </div>
    </div>

    <script>
        function returnFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    status: '{{ request('status') }}',
                    reason: '{{ request('reason') }}',
                    date_from: '{{ request('date_from') }}',
                    date_to: '{{ request('date_to') }}',
                },
                init() {
                    // Initialize from URL params
                },
                applyFilters() {
                    const params = new URLSearchParams();

                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.status) params.append('status', this.filters.status);
                    if (this.filters.reason) params.append('reason', this.filters.reason);
                    if (this.filters.date_from) params.append('date_from', this.filters.date_from);
                    if (this.filters.date_to) params.append('date_to', this.filters.date_to);

                    window.location.href = `{{ route('finance.sales-returns.index') }}?${params.toString()}`;
                }
            }
        }
    </script>
</x-app-layout>