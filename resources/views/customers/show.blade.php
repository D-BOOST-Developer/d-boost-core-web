<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ $customer->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('customers.edit', $customer) }}" 
                   class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
                </a>
                <a href="{{ route('customers.index') }}" 
                   class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Customers</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Customer Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Avatar & Info -->
                            <div>
                                <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-white text-4xl font-bold">{{ substr($customer->name, 0, 1) }}</span>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $customer->name }}</h3>
                                    <div class="flex justify-center items-center space-x-2 mb-4">
                                        <span class="px-3 py-1 text-sm rounded-full {{ $customer->is_active ? 'bg-green-900/50 text-green-200 border border-green-500/30' : 'bg-red-900/50 text-red-400 border border-red-500/30' }}">
                                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($customer->isVip())
                                            <span class="px-3 py-1 text-sm rounded-full bg-yellow-900/50 text-yellow-200 border border-yellow-500/30">
                                                VIP Customer
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Details -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">Customer ID</p>
                                        <p class="text-gray-900 font-medium">{{ substr($customer->id, 0, 8) }}...</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Email</p>
                                        <p class="text-gray-900 font-medium">{{ $customer->email ?: 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Phone</p>
                                        <p class="text-gray-900 font-medium">{{ $customer->phone ?: 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Gender</p>
                                        <p class="text-gray-900 font-medium">{{ $customer->gender ? ucfirst($customer->gender) : 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Age</p>
                                        <p class="text-gray-900 font-medium">
                                            @if($customer->age)
                                                {{ $customer->age }} years old ({{ $customer->age_group }})
                                            @else
                                                Not specified
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Date of Birth</p>
                                        <p class="text-gray-900 font-medium">{{ $customer->date_of_birth ? $customer->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">City</p>
                                        <p class="text-gray-900 font-medium">{{ $customer->city ?: 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Member Since</p>
                                        <p class="text-gray-900 font-medium">{{ $customer->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>

                                @if($customer->address)
                                    <div>
                                        <p class="text-gray-600 text-sm mb-2">Address</p>
                                        <p class="text-gray-700">{{ $customer->address }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Purchase History -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase History</h3>
                        @if($customer->sales->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left text-gray-700 pb-3">Invoice</th>
                                            <th class="text-left text-gray-700 pb-3">Date</th>
                                            <th class="text-left text-gray-700 pb-3">Items</th>
                                            <th class="text-left text-gray-700 pb-3">Total</th>
                                            <th class="text-left text-gray-700 pb-3">Status</th>
                                            <th class="text-left text-gray-700 pb-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="space-y-2">
                                        @foreach($customer->sales->take(10) as $sale)
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                <td class="py-3 text-gray-900 font-medium">{{ $sale->invoice_number }}</td>
                                                <td class="py-3 text-gray-900">{{ $sale->sale_date->format('M d, Y') }}</td>
                                                <td class="py-3 text-gray-900">{{ $sale->total_items }} items</td>
                                                <td class="py-3 text-green-600 font-medium">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                                <td class="py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $sale->status === 'completed' ? 'bg-green-900/50 text-green-200' : 'bg-yellow-900/50 text-yellow-400' }}">
                                                        {{ ucfirst($sale->status) }}
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <a href="{{ route('finance.sales.show', $sale) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($customer->sales->count() > 10)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('finance.sales.index', ['customer' => $customer->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View All Orders ({{ $customer->sales->count() }} total)
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-400">No purchase history yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics Sidebar -->
                <div class="space-y-6">
                    <!-- Purchase Statistics -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Statistics</h3>
                        <div class="space-y-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600">{{ $totalOrders }}</p>
                                <p class="text-gray-600 text-sm">Total Orders</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                                <p class="text-gray-600 text-sm">Total Spent</p>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</p>
                                <p class="text-gray-600 text-sm">Avg Order Value</p>
                            </div>
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <p class="text-2xl font-bold text-orange-600">{{ $customer->purchase_frequency }}</p>
                                <p class="text-gray-600 text-sm">Purchase Frequency</p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Insights -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Insights</h3>
                        <div class="space-y-4">
                            @if($lastOrderDate)
                                <div>
                                    <p class="text-gray-600 text-sm">Last Purchase</p>
                                    <p class="text-gray-900 font-medium">{{ $lastOrderDate->format('M d, Y') }}</p>
                                    <p class="text-gray-500 text-xs">{{ $lastOrderDate->diffForHumans() }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <p class="text-gray-600 text-sm">Customer Lifetime Value</p>
                                <p class="text-green-600 font-bold text-lg">Rp {{ number_format($customer->lifetime_value, 0, ',', '.') }}</p>
                            </div>

                            @if($customer->isVip())
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-yellow-700 dark:text-yellow-400 font-medium text-sm">VIP Customer</p>
                                            <p class="text-yellow-600 dark:text-yellow-500 text-xs">High-value customer with premium status</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600">Created by</p>
                                <p class="text-gray-900">{{ $customer->creator->name ?? 'System' }}</p>
                                <p class="text-gray-500 text-xs">{{ $customer->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @if($customer->updated_at != $customer->created_at)
                                <div>
                                    <p class="text-gray-600">Last updated by</p>
                                    <p class="text-gray-900">{{ $customer->updater->name ?? 'System' }}</p>
                                    <p class="text-gray-500 text-xs">{{ $customer->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
