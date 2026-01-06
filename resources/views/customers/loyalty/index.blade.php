<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Loyalty Program') }}
            </h2>
            <a href="{{ route('customers.loyalty.leaderboard') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Leaderboard</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Tier Distribution -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-orange-600 text-sm font-medium">Bronze</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $stats['bronze_members'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm font-medium">Silver</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $stats['silver_members'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-yellow-600 text-sm font-medium">Gold</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $stats['gold_members'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-blue-600 text-sm font-medium">Platinum</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $stats['platinum_members'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-cyan-600 text-sm font-medium">Diamond</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">0</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm"
                 x-data="loyaltyFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()"
                               placeholder="Search by name or email..."
                               class="w-full bg-white border border-gray-300 text-gray-900 placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none"
                               autocomplete="off">
                    </div>

                    <!-- Tier Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tier</label>
                        <select x-model="filters.tier" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Tiers</option>
                            <option value="bronze">Bronze</option>
                            <option value="silver">Silver</option>
                            <option value="gold">Gold</option>
                            <option value="platinum">Platinum</option>
                            <option value="diamond">Diamond</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select x-model="filters.sort" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="points_desc">Points (High to Low)</option>
                            <option value="points_asc">Points (Low to High)</option>
                            <option value="name">Name (A-Z)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Loyalty Points Table -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Current Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Earned</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Redeemed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($loyaltyPoints as $loyalty)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('customers.show', $loyalty->customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $loyalty->customer->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($loyalty->tier_label) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-semibold">
                                        {{ number_format($loyalty->current_balance, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-600 font-medium">
                                        {{ number_format($loyalty->points_earned, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-red-600 font-medium">
                                        {{ number_format($loyalty->points_redeemed, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $loyalty->points_progress }}%"></div>
                                        </div>
                                        <p class="text-gray-600 text-xs mt-1">{{ round($loyalty->points_progress) }}%</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <a href="{{ route('customers.loyalty.points', $loyalty->customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No loyalty data found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($loyaltyPoints->hasPages())
                <div class="mt-6">
                    {{ $loyaltyPoints->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function loyaltyFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    tier: '{{ request('tier') }}',
                    sort: '{{ request('sort', 'points_desc') }}',
                },
                loading: false,
                init() {
                    // Initialize from URL
                },
                applyFilters() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    
                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.tier) params.append('tier', this.filters.tier);
                    if (this.filters.sort) params.append('sort', this.filters.sort);
                    
                    window.location.href = `{{ route('customers.loyalty.index') }}?${params.toString()}`;
                }
            }
        }
    </script>
</x-app-layout>
