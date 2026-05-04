<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ $product->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('inventory.products.edit', $product) }}" 
                   class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
                </a>
                <a href="{{ route('inventory.products.index') }}" 
                   class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Products</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Image -->
                            <div>
                                <div class="w-full h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center overflow-hidden">
                                    @if($product->image_path)
                                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 text-sm rounded-full {{ $product->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($product->isLowStock())
                                            <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                                Low Stock
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">SKU</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $product->sku }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Category</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $product->category }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Selling Price</p>
                                        <p class="text-[#006BFF] dark:text-[#00FF9C] font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Cost Price</p>
                                        <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($product->cost, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Current Stock</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $product->stock_quantity }} units</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Min Stock Level</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $product->min_stock_level }} units</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Profit Margin</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ number_format($product->profit_margin, 1) }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Created</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $product->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>

                                @if($product->description)
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Description</p>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $product->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sales History -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Sales</h3>
                        @if($product->salesDetails->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Invoice</th>
                                            <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Customer</th>
                                            <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Quantity</th>
                                            <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Unit Price</th>
                                            <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Total</th>
                                            <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($product->salesDetails->take(10) as $detail)
                                            <tr>
                                                <td class="py-3 text-gray-900 dark:text-white">{{ $detail->sale->invoice_number }}</td>
                                                <td class="py-3 text-gray-900 dark:text-white">{{ $detail->sale->customer->name ?? $detail->sale->customer_snapshot['name'] ?? ($detail->sale->order_source === 'storefront' ? 'Storefront Customer' : 'Walk-in Customer') }}</td>
                                                <td class="py-3 text-gray-900 dark:text-white">{{ $detail->quantity }}</td>
                                                <td class="py-3 text-gray-900 dark:text-white">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                                                <td class="py-3 text-[#006BFF] dark:text-[#00FF9C] font-medium">Rp {{ number_format($detail->line_total, 0, ',', '.') }}</td>
                                                <td class="py-3 text-gray-600 dark:text-gray-400">{{ $detail->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No sales recorded for this product yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics Sidebar -->
                <div class="space-y-6">
                    <!-- Sales Statistics -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sales Statistics</h3>
                        <div class="space-y-4">
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <p class="text-2xl font-bold text-[#006BFF] dark:text-[#00FF9C]">{{ $totalSold }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Total Units Sold</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Total Revenue</p>
                            </div>
                            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($avgOrderQuantity, 1) }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Avg Order Quantity</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Stock Status</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Current Stock</span>
                                    <span class="text-gray-900 dark:text-white">{{ $product->stock_quantity }}/{{ $product->stock_quantity + $totalSold }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-[#006BFF] dark:bg-[#006BFF] h-2 rounded-full" 
                                         style="width: {{ $product->stock_quantity > 0 ? ($product->stock_quantity / ($product->stock_quantity + $totalSold)) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            @if($product->isLowStock())
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-yellow-700 dark:text-yellow-400 font-medium text-sm">Low Stock Alert</p>
                                            <p class="text-yellow-600 dark:text-yellow-500 text-xs">Stock is below minimum level</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Audit Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Created by</p>
                                <p class="text-gray-900 dark:text-white">{{ $product->creator->name ?? 'System' }}</p>
                                <p class="text-gray-500 dark:text-gray-500 text-xs">{{ $product->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @if($product->updated_at != $product->created_at)
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Last updated by</p>
                                    <p class="text-gray-900 dark:text-white">{{ $product->updater->name ?? 'System' }}</p>
                                    <p class="text-gray-500 dark:text-gray-500 text-xs">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
