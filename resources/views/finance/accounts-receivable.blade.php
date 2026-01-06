<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Accounts Receivable') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Aging Analysis Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Receivable -->
                <div class="bg-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <p class="text-xs uppercase tracking-wider font-semibold opacity-75">Total Outstanding</p>
                    <h3 class="mt-2 text-2xl font-bold">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</h3>
                    <p class="mt-2 text-xs opacity-75">{{ $pendingPayments->count() }} invoices pending</p>
                </div>

                <!-- 0-30 Days -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">Current (0-30 Days)</p>
                    <h3 class="mt-2 text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($aging['0-30'], 0, ',', '.') }}</h3>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-3">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $totalReceivable > 0 ? ($aging['0-30'] / $totalReceivable) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- 31-60 Days -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">Overdue (31-60 Days)</p>
                    <h3 class="mt-2 text-xl font-bold text-yellow-600 dark:text-yellow-500">Rp {{ number_format($aging['31-60'], 0, ',', '.') }}</h3>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-3">
                        <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $totalReceivable > 0 ? ($aging['31-60'] / $totalReceivable) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- 60+ Days -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">Critical (60+ Days)</p>
                    <h3 class="mt-2 text-xl font-bold text-red-600 dark:text-red-500">Rp {{ number_format($aging['60+'], 0, ',', '.') }}</h3>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-3">
                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $totalReceivable > 0 ? ($aging['60+'] / $totalReceivable) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Invoice List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Outstanding Invoices</h3>
                    <!-- Customer Filter could go here -->
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Invoice</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Due</th>
                                <th class="px-6 py-3">Age</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($pendingPayments as $sale)
                                @php
                                    $daysOverdue = $sale->sale_date->diffInDays(now());
                                    $ageClass = 'text-green-600';
                                    if($daysOverdue > 30) $ageClass = 'text-yellow-600';
                                    if($daysOverdue > 60) $ageClass = 'text-red-600 font-bold';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                        {{ $sale->customer->name }}
                                        <div class="text-xs text-gray-500">{{ $sale->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $sale->sale_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{-- Assuming 30 day terms if not specified --}}
                                        {{ $sale->sale_date->addDays(30)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 {{ $ageClass }}">
                                        {{ $daysOverdue }} days
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('finance.sales.show', $sale) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs uppercase tracking-wide">
                                            Process Payment
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">All Clear!</h3>
                                        <p class="text-gray-500 mt-1">No outstanding payments found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
