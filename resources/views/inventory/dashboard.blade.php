<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Inventory Overview') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time inventory tracking and insights</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <form method="GET" action="{{ route('inventory.dashboard') }}" class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm border-0 focus:ring-0 bg-transparent text-gray-700 dark:text-gray-300 rounded-md p-1.5">
                    <span class="text-gray-400">-</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm border-0 focus:ring-0 bg-transparent text-gray-700 dark:text-gray-300 rounded-md p-1.5">
                    <button type="submit" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-6">
            <!-- Total Stock Value -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Stock Value</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400 font-medium">
                        {{ number_format($totalStockQuantity, 0, ',', '.') }} units
                    </span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    <span class="text-gray-500 dark:text-gray-400">Avg: Rp {{ number_format($avgStockValuePerProduct, 0, ',', '.') }}/product</span>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Low Stock Alerts</p>
                        <h3 class="mt-2 text-2xl font-bold {{ $lowStockCount > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $lowStockCount }}
                        </h3>
                    </div>
                    <div class="p-2 {{ $lowStockCount > 0 ? 'bg-orange-50 dark:bg-orange-900/20' : 'bg-green-50 dark:bg-green-900/20' }} rounded-lg">
                        <svg class="w-6 h-6 {{ $lowStockCount > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="{{ $lowStockCount > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }} font-medium">
                        {{ $outOfStockProducts }} out of stock
                    </span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    <a href="{{ route('inventory.products.index') }}" class="text-blue-600 hover:text-blue-700 hover:underline">View products</a>
                </div>
            </div>

            <!-- Stock Movements -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Movements</p>
                        <h3 class="mt-2 text-2xl font-bold {{ $netStockMovement >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $netStockMovement >= 0 ? '+' : '' }}{{ number_format($netStockMovement, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 dark:text-green-400 font-medium">
                        In: {{ number_format($stockIn, 0, ',', '.') }}
                    </span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    <span class="text-red-600 dark:text-red-400 font-medium">
                        Out: {{ number_format($stockOut, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none p-6 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-white/10 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Total Products</p>
                            <h3 class="mt-2 text-2xl font-bold">{{ $totalProducts }}</h3>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-indigo-100">
                        <span>Stock Turnover: {{ number_format($stockTurnoverRate, 2) }}x</span>
                        <span class="mx-2">|</span>
                        <a href="{{ route('inventory.products.index') }}" class="flex items-center hover:text-white transition-colors">
                            <span>View All</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-6">
            <!-- Stock Movements Trend Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Stock Movements Trend</h3>
                    <select id="stockPeriod" class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 3 Months</option>
                    </select>
                </div>
                <div class="h-80 relative">
                    <canvas id="stockMovementChart"></canvas>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Low Stock Products</h3>
                    <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-medium">{{ $lowStockCount }} Items</span>
                </div>
                
                @if($lowStockProducts->count() > 0)
                    <div class="space-y-4 max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($lowStockProducts->take(5) as $product)
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border border-gray-100 dark:border-gray-700/50">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ substr($product->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            Stock: <span class="font-semibold {{ $product->stock_quantity == 0 ? 'text-red-600' : 'text-orange-600' }}">{{ $product->stock_quantity }}</span> / Min: {{ $product->min_stock_level }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right ml-2">
                                    <a href="{{ route('inventory.products.show', $product) }}" class="text-xs text-blue-600 hover:text-blue-700 hover:underline">View</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($lowStockProducts->count() > 5)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                            <a href="{{ route('inventory.products.index', ['filter' => 'low_stock']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">View All Low Stock</a>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium">All good!</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No low stock products found.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6">
            <!-- Top Products by Value -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white">Top Products by Stock Value</h3>
                    <a href="{{ route('inventory.products.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Stock</th>
                                <th class="px-6 py-3 text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($topProductsByValue as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $product->name }}
                                    <div class="text-xs text-gray-500 font-normal">{{ $product->sku }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ number_format($product->stock_quantity, 0, ',', '.') }} {{ $product->unit ?? 'pcs' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($product->stock_quantity * $product->cost, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No products found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white">Recent Transactions</h3>
                    <a href="{{ route('inventory.transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3 text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $transaction->product->name ?? 'Deleted Product' }}
                                    <div class="text-xs text-gray-500 font-normal">{{ $transaction->transaction_date->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ in_array($transaction->transaction_type, ['purchase', 'adjustment_increase', 'return_from_customer', 'transfer_in', 'initial_stock']) ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                        {{ $transaction->transaction_type_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-medium {{ in_array($transaction->transaction_type, ['purchase', 'adjustment_increase', 'return_from_customer', 'transfer_in', 'initial_stock']) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ in_array($transaction->transaction_type, ['purchase', 'adjustment_increase', 'return_from_customer', 'transfer_in', 'initial_stock']) ? '+' : '-' }}{{ number_format($transaction->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No recent transactions</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products by Category -->
        <div class="px-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white">Inventory by Category</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3 text-center">Products</th>
                                <th class="px-6 py-3 text-right">Total Quantity</th>
                                <th class="px-6 py-3 text-right">Total Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($productsByCategory as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst($category->category) }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $category->count }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    {{ number_format($category->total_quantity, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($category->total_value, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let stockChart = null;

            // Function to format date labels based on period
            function formatDateLabel(date, period) {
                const d = new Date(date);
                if (period <= 7) {
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                } else if (period <= 30) {
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                } else {
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }
            }

            // Function to generate date range based on period
            function generateDateRange(days) {
                const dates = [];
                for (let i = days - 1; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    dates.push(date.toISOString().split('T')[0]);
                }
                return dates;
            }

            // Function to fetch stock movement data - optimized single request
            async function fetchStockData(period) {
                try {
                    const response = await fetch(`{{ route('inventory.dashboard') }}?chart_period=${period}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        return data;
                    }
                } catch (error) {
                    console.error('Error fetching chart data:', error);
                }
                
                // Fallback: return empty data
                return generateDateRange(period).map(date => ({
                    date: date,
                    stock_in: 0,
                    stock_out: 0
                }));
            }

            // Function to update stock movement chart
            async function updateStockChart(period) {
                const periodSelect = document.getElementById('stockPeriod');
                if (periodSelect) {
                    periodSelect.disabled = true;
                    periodSelect.style.opacity = '0.6';
                }

                let stockData;
                
                if (period === 7) {
                    // Use existing data for 7 days
                    stockData = @json($stockTrend);
                } else {
                    // Fetch new data for other periods
                    stockData = await fetchStockData(period);
                }

                const stockLabels = stockData.map(item => {
                    return formatDateLabel(item.date, period);
                });
                const stockInValues = stockData.map(item => parseInt(item.stock_in || 0));
                const stockOutValues = stockData.map(item => parseInt(item.stock_out || 0));

                if (stockChart) {
                    stockChart.data.labels = stockLabels;
                    stockChart.data.datasets[0].data = stockInValues;
                    stockChart.data.datasets[1].data = stockOutValues;
                    stockChart.update('active');
                } else {
                    // Create new chart
                    const stockCtx = document.getElementById('stockMovementChart');
                    if (stockCtx) {
                        stockChart = new Chart(stockCtx, {
                            type: 'bar',
                            data: {
                                labels: stockLabels,
                                datasets: [
                                    {
                                        label: 'Stock In',
                                        data: stockInValues,
                                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                        borderColor: 'rgb(34, 197, 94)',
                                        borderWidth: 2.5,
                                        borderRadius: {
                                            topLeft: 6,
                                            topRight: 6,
                                            bottomLeft: 0,
                                            bottomRight: 0
                                        },
                                        borderSkipped: false,
                                        maxBarThickness: 50,
                                    },
                                    {
                                        label: 'Stock Out',
                                        data: stockOutValues,
                                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                        borderColor: 'rgb(239, 68, 68)',
                                        borderWidth: 2.5,
                                        borderRadius: {
                                            topLeft: 6,
                                            topRight: 6,
                                            bottomLeft: 0,
                                            bottomRight: 0
                                        },
                                        borderSkipped: false,
                                        maxBarThickness: 50,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 600,
                                    easing: 'easeOutQuart'
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15,
                                            font: {
                                                size: 12,
                                                weight: '500'
                                            },
                                            color: '#6B7280'
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        padding: 12,
                                        titleFont: {
                                            size: 14,
                                            weight: 'bold'
                                        },
                                        bodyFont: {
                                            size: 13
                                        },
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' units';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: false,
                                        grid: {
                                            display: true,
                                            color: 'rgba(0, 0, 0, 0.05)',
                                            drawBorder: false
                                        },
                                        ticks: {
                                            color: '#6B7280',
                                            font: {
                                                size: 11
                                            },
                                            maxRotation: period > 30 ? 45 : 0
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        stacked: false,
                                        grid: {
                                            display: true,
                                            color: 'rgba(0, 0, 0, 0.05)',
                                            drawBorder: false
                                        },
                                        ticks: {
                                            color: '#6B7280',
                                            font: {
                                                size: 11
                                            },
                                            callback: function(value) {
                                                if (value >= 1000) {
                                                    return (value / 1000).toFixed(1) + 'K';
                                                }
                                                return value;
                                            }
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index',
                                    axis: 'x'
                                },
                                onHover: (event, activeElements) => {
                                    event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                                },
                            }
                        });
                    }
                }

                if (periodSelect) {
                    periodSelect.disabled = false;
                    periodSelect.style.opacity = '1';
                }
            }

            // Initialize chart with default 7 days
            updateStockChart(7);

            // Update chart on period change
            document.getElementById('stockPeriod')?.addEventListener('change', function() {
                const period = parseInt(this.value);
                updateStockChart(period);
            });
        });
    </script>
</x-app-layout>

