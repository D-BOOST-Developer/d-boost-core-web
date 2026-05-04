<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Financial Overview') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time financial insights for your business</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <form method="GET" action="{{ route('finance.dashboard') }}" class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
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
            <!-- Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Revenue</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($revenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 dark:text-green-400 font-medium flex items-center">
                        {{ $salesCount }} sales
                    </span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    <span class="text-gray-500 dark:text-gray-400">Avg: Rp {{ number_format($avgSaleValue, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Expenses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expenses</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($expenses, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-red-600 dark:text-red-400 font-medium">
                        {{ $expenseCount }} transactions
                    </span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    <a href="{{ route('finance.expenses.index') }}" class="text-blue-600 hover:text-blue-700 hover:underline">View details</a>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</p>
                        <h3 class="mt-2 text-2xl font-bold {{ $profit >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600' }}">
                            Rp {{ number_format($profit, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="{{ $profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                        {{ $revenue > 0 ? number_format(($profit / $revenue) * 100, 1) : 0 }}% Margin
                    </span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    <a href="{{ route('finance.reports.profit-loss') }}" class="text-blue-600 hover:text-blue-700 hover:underline">Full Report</a>
                </div>
            </div>

            <!-- Cash On Hand -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg shadow-blue-200 dark:shadow-none p-6 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-white/10 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-blue-100 uppercase tracking-wider">Est. Cash on Hand</p>
                            <h3 class="mt-2 text-2xl font-bold">Rp {{ number_format($cashOnHand, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-blue-100">
                        <a href="{{ route('finance.reports.cash-flow') }}" class="flex items-center hover:text-white transition-colors">
                            <span>View Cash Flow</span>
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
            <!-- Revenue Trend Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revenue Trend</h3>
                    <select id="revenuePeriod" class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="365">This Year</option>
                    </select>
                </div>
                <div class="h-80 relative">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Accounts Receivable -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pending Payments</h3>
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">{{ $pendingPayments->count() }} Pending</span>
                </div>
                
                @if($pendingPayments->count() > 0)
                    <div class="space-y-4 max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($pendingPayments->take(5) as $payment)
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border border-gray-100 dark:border-gray-700/50">
                                @php
                                    $payerName = $payment->customer_snapshot['name'] ?? null;
                                    if (!$payerName) {
                                        $payerName = $payment->order_source === 'storefront' ? 'Storefront Customer' : 'Walk-in Customer';
                                    }
                                @endphp
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                        {{ strtoupper(substr($payerName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[120px]">{{ $payerName }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->sale_date->format('d M') }} • {{ $payment->sale_date->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</p>
                                    <a href="{{ route('finance.sales.show', $payment) }}" class="text-xs text-blue-600 hover:text-blue-700 hover:underline">View invoice</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                        <a href="{{ route('finance.accounts-receivable') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">View All Receivables</a>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium">All caught up!</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No pending payments found.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6">
            <!-- Recent Sales -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white">Recent Sales</h3>
                    <a href="{{ route('finance.sales.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                @php
                                    $saleCustomerName = $sale->customer_snapshot['name'] ?? null;
                                    if (!$saleCustomerName) {
                                        $saleCustomerName = $sale->order_source === 'storefront' ? 'Storefront Customer' : 'Walk-in Customer';
                                    }
                                @endphp
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $saleCustomerName }}
                                    <div class="text-xs text-gray-500 font-normal">{{ $sale->invoice_number }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $sale->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No recent sales</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white">Recent Expenses</h3>
                    <a href="{{ route('finance.expenses.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentExpenses as $expense)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ Str::limit($expense->description, 25) }}
                                    <div class="text-xs text-gray-500 font-normal">{{ $expense->expense_date->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ ucfirst($expense->category) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No recent expenses</td>
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
            let revenueChart = null;

            // Function to format date labels based on period
            function formatDateLabel(date, period) {
                const d = new Date(date);
                if (period <= 7) {
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                } else if (period <= 30) {
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                } else {
                    return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
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

            // Function to fetch revenue and expenses data - optimized single request
            async function fetchRevenueData(period) {
                try {
                    const response = await fetch(`{{ route('finance.dashboard') }}?chart_period=${period}`, {
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
                    revenue: 0,
                    expenses: 0
                }));
            }

            // Function to update revenue chart
            async function updateRevenueChart(period) {
                const periodSelect = document.getElementById('revenuePeriod');
                if (periodSelect) {
                    periodSelect.disabled = true;
                    periodSelect.style.opacity = '0.6';
                }

                let revenueData;
                
                if (period === 7) {
                    // Use existing data for 7 days
                    revenueData = @json($revenueTrend);
                } else {
                    // Fetch new data for other periods
                    revenueData = await fetchRevenueData(period);
                }

                const revenueLabels = revenueData.map(item => {
                    return formatDateLabel(item.date, period);
                });
                const revenueValues = revenueData.map(item => parseFloat(item.revenue || 0));
                const expenseValues = revenueData.map(item => parseFloat(item.expenses || 0));

                // Create gradient for revenue
                const revenueCtx = document.getElementById('revenueChart');
                if (!revenueCtx) return;

                const revenueGradient = revenueCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                revenueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                revenueGradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.15)');
                revenueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

                // Create gradient for expenses
                const expenseGradient = revenueCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                expenseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
                expenseGradient.addColorStop(0.5, 'rgba(239, 68, 68, 0.12)');
                expenseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.02)');

                if (revenueChart) {
                    revenueChart.data.labels = revenueLabels;
                    revenueChart.data.datasets[0].data = revenueValues;
                    revenueChart.data.datasets[1].data = expenseValues;
                    revenueChart.update('active');
                } else {
                    // Create new chart
                    revenueChart = new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: revenueLabels,
                            datasets: [
                                {
                                    label: 'Revenue',
                                    data: revenueValues,
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: revenueGradient,
                                    borderWidth: 3.5,
                                    fill: true,
                                    tension: 0.5,
                                    pointRadius: 0,
                                    pointHoverRadius: 0,
                                    pointBackgroundColor: 'transparent',
                                    pointBorderColor: 'transparent',
                                    pointBorderWidth: 0,
                                    pointHoverBackgroundColor: 'rgb(59, 130, 246)',
                                    pointHoverBorderColor: '#ffffff',
                                    pointHoverBorderWidth: 3,
                                    pointHitRadius: 15,
                                    showLine: true,
                                    spanGaps: true,
                                },
                                {
                                    label: 'Expenses',
                                    data: expenseValues,
                                    borderColor: 'rgb(239, 68, 68)',
                                    backgroundColor: expenseGradient,
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.5,
                                    pointRadius: 0,
                                    pointHoverRadius: 0,
                                    pointBackgroundColor: 'transparent',
                                    pointBorderColor: 'transparent',
                                    pointBorderWidth: 0,
                                    pointHoverBackgroundColor: 'rgb(239, 68, 68)',
                                    pointHoverBorderColor: '#ffffff',
                                    pointHoverBorderWidth: 3,
                                    pointHitRadius: 15,
                                    showLine: true,
                                    spanGaps: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        padding: 20,
                                        font: {
                                            size: 13,
                                            weight: '600',
                                            family: "'Inter', sans-serif"
                                        },
                                        color: '#374151',
                                        generateLabels: function(chart) {
                                            const original = Chart.defaults.plugins.legend.labels.generateLabels;
                                            const labels = original.call(this, chart);
                                            labels.forEach(label => {
                                                if (label.text === 'Revenue') {
                                                    label.fillStyle = 'rgb(59, 130, 246)';
                                                    label.strokeStyle = 'rgb(59, 130, 246)';
                                                } else if (label.text === 'Expenses') {
                                                    label.fillStyle = 'rgb(239, 68, 68)';
                                                    label.strokeStyle = 'rgb(239, 68, 68)';
                                                }
                                            });
                                            return labels;
                                        }
                                    }
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                    padding: 16,
                                    titleFont: {
                                        size: 14,
                                        weight: '600',
                                        family: "'Inter', sans-serif"
                                    },
                                    bodyFont: {
                                        size: 13,
                                        weight: '500',
                                        family: "'Inter', sans-serif"
                                    },
                                    titleColor: '#F9FAFB',
                                    bodyColor: '#F9FAFB',
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    usePointStyle: true,
                                    callbacks: {
                                        title: function(context) {
                                            return context[0].label;
                                        },
                                        label: function(context) {
                                            const value = context.parsed.y;
                                            const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                            return context.dataset.label + ': ' + formatted;
                                        },
                                        labelColor: function(context) {
                                            return {
                                                borderColor: context.dataset.borderColor,
                                                backgroundColor: context.dataset.borderColor,
                                                borderWidth: 3,
                                                borderRadius: 2
                                            };
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: true,
                                        color: 'rgba(229, 231, 235, 0.5)',
                                        drawBorder: false,
                                        lineWidth: 1
                                    },
                                    ticks: {
                                        color: '#6B7280',
                                        font: {
                                            size: 11,
                                            weight: '500',
                                            family: "'Inter', sans-serif"
                                        },
                                        maxRotation: period > 30 ? 45 : 0,
                                        padding: 10
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        color: 'rgba(229, 231, 235, 0.5)',
                                        drawBorder: false,
                                        lineWidth: 1
                                    },
                                    ticks: {
                                        color: '#6B7280',
                                        font: {
                                            size: 11,
                                            weight: '500',
                                            family: "'Inter', sans-serif"
                                        },
                                        padding: 12,
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                            }
                                            return 'Rp ' + value;
                                        }
                                    },
                                    border: {
                                        display: false
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
                            elements: {
                                line: {
                                    capBezierPoints: false
                                }
                            }
                        }
                    });
                }

                if (periodSelect) {
                    periodSelect.disabled = false;
                    periodSelect.style.opacity = '1';
                }
            }

            // Initialize chart with default 7 days
            updateRevenueChart(7);

            // Update chart on period change
            document.getElementById('revenuePeriod')?.addEventListener('change', function() {
                const period = parseInt(this.value);
                updateRevenueChart(period);
            });
        });
    </script>
</x-app-layout>
