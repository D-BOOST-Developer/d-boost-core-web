<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Customer Analytics') }}
            </h2>
            <!-- <div class="flex items-center space-x-2">
                <a href="{{ route('customers.analytics.rfm') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-sm">
                    RFM Analysis
                </a>
                <a href="{{ route('customers.analytics.churn') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-sm">
                    Churn Analysis
                </a>
            </div> -->
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 shadow-sm">
                    <p class="text-blue-700 text-sm font-medium">Total Customers</p>
                    <p class="text-gray-900 text-3xl font-bold mt-2">{{ $metrics['total_customers'] ?? 0 }}</p>
                    <p class="text-blue-600 text-xs mt-2">{{ $metrics['new_this_month'] ?? 0 }} new this month</p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 shadow-sm">
                    <p class="text-green-700 text-sm font-medium">Total Revenue</p>
                    <p class="text-gray-900 text-3xl font-bold mt-2">Rp {{ number_format($metrics['total_revenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-green-600 text-xs mt-2">Average: Rp {{ number_format($metrics['avg_lifetime_value'] ?? 0, 0, ',', '.') }}</p>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-6 shadow-sm">
                    <p class="text-yellow-700 text-sm font-medium">VIP Customers</p>
                    <p class="text-gray-900 text-3xl font-bold mt-2">{{ $metrics['vip_customers'] ?? 0 }}</p>
                    <p class="text-yellow-600 text-xs mt-2">{{ round(($metrics['vip_customers'] / $metrics['total_customers']) * 100) }}% of total</p>
                </div>

                <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-6 shadow-sm">
                    <p class="text-red-700 text-sm font-medium">At Risk</p>
                    <p class="text-gray-900 text-3xl font-bold mt-2">{{ $metrics['at_risk_customers'] ?? 0 }}</p>
                    <p class="text-red-600 text-xs mt-2">{{ round(($metrics['at_risk_customers'] / $metrics['total_customers']) * 100) }}% of total</p>
                </div>
            </div>

            <!-- Customer Value Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Value Segments -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Value Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">VIP (Score 12-15)</span>
                                <span class="text-gray-900 font-semibold">{{ $segments['vip'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ ($segments['vip'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">High Value (Score 9-11)</span>
                                <span class="text-gray-900 font-semibold">{{ $segments['high'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-green-400 h-2 rounded-full" style="width: {{ ($segments['high'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">Medium Value (Score 6-8)</span>
                                <span class="text-gray-900 font-semibold">{{ $segments['medium'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-400 h-2 rounded-full" style="width: {{ ($segments['medium'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">Low Value (Score < 6)</span>
                                <span class="text-gray-900 font-semibold">{{ $segments['low'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-red-400 h-2 rounded-full" style="width: {{ ($segments['low'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Frequency -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Frequency</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">No Purchase</span>
                                <span class="text-gray-900 font-semibold">{{ $frequency['no_purchase'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-gray-400 h-2 rounded-full" style="width: {{ ($frequency['no_purchase'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">1-3 Purchases</span>
                                <span class="text-gray-900 font-semibold">{{ $frequency['low'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-orange-400 h-2 rounded-full" style="width: {{ ($frequency['low'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">4-7 Purchases</span>
                                <span class="text-gray-900 font-semibold">{{ $frequency['medium'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ ($frequency['medium'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-700 text-sm">8+ Purchases</span>
                                <span class="text-gray-900 font-semibold">{{ $frequency['high'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-green-400 h-2 rounded-full" style="width: {{ ($frequency['high'] / $metrics['total_customers']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Customers by Revenue</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Rank</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Spent</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Purchases</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Avg Order</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Segment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($topCustomers as $index => $customer)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-gray-900 font-bold">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $customer->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 font-semibold">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $customer->purchase_frequency }}</td>
                                    <td class="px-4 py-3 text-gray-700">Rp {{ number_format($customer->average_order_value, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300">
                                            {{ $customer->customer_value_segment }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                        No customer data available
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
