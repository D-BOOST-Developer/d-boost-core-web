<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Customer Management') }}
            </h2>
            <a href="{{ route('customers.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Add Customer</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm" 
                 x-data="customerFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" 
                               placeholder="Search customers..." 
                               class="w-full bg-white border border-gray-300 text-gray-900 placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none"
                               autocomplete="off" spellcheck="false">
                    </div>

                    <!-- City Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <select x-model="filters.city" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select x-model="filters.gender" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Genders</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div x-show="Alpine.store('app').loading" x-transition class="mt-4 flex items-center justify-center bg-gray-100 rounded-lg py-3 px-4 border border-gray-200">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-blue-500 border-t-transparent"></div>
                    <span class="ml-3 text-gray-700 font-medium" x-text="Alpine.store('app').loadingMessage || 'Loading...'">Applying filters...</span>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                @if($customers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Demographics</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Statistics</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($customers as $customer)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <!-- Customer Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-sm font-bold">{{ substr($customer->name, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                                    <div class="text-sm text-gray-500">ID: {{ substr($customer->id, 0, 8) }}...</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $customer->email ?: '-' }}</div>
                                            <div class="text-sm text-gray-500">{{ $customer->phone ?: '-' }}</div>
                                        </td>

                                        <!-- Demographics -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $customer->city ?: '-' }}
                                                @if($customer->gender)
                                                    <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                                        {{ $customer->gender === 'male' ? 'bg-blue-100 text-blue-800' : 
                                                           ($customer->gender === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ ucfirst($customer->gender) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if($customer->age)
                                                    Age: {{ $customer->age }} ({{ $customer->age_group }})
                                                @else
                                                    Age: Not specified
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Statistics -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-blue-600 font-medium">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</div>
                                            <div class="text-sm text-gray-500">{{ $customer->purchase_frequency }} orders</div>
                                            @if($customer->isVip())
                                                <span class="inline-block mt-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full border border-yellow-300">
                                                    VIP
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' }}">
                                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if($customer->last_purchase_date)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Last: {{ $customer->last_purchase_date->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('customers.show', $customer) }}" 
                                                   class="text-gray-600 hover:text-blue-600 transition-colors duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('customers.edit', $customer) }}" 
                                                   class="text-gray-600 hover:text-blue-600 transition-colors duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <button type="button" 
                                                        onclick="openDeleteModal('{{ route('customers.destroy', $customer) }}', 'Delete Customer', 'Are you sure you want to delete {{ $customer->name }}? This will also remove all purchase history.')"
                                                        class="text-gray-600 hover:text-red-600 transition-colors duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-gray-900 text-lg font-medium mb-2">No Customers Found</h3>
                        <p class="text-gray-500 mb-4">Get started by adding your first customer.</p>
                        <a href="{{ route('customers.create') }}" 
                           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Customer
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($customers->hasPages())
                <div class="mt-6">
                    {{ $customers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function customerFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    city: '{{ request('city') }}',
                    gender: '{{ request('gender') }}',
                    status: '{{ request('status') }}'
                },

                init() {
                    this.updateFromUrl();
                },

                updateFromUrl() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.filters.search = urlParams.get('search') || '';
                    this.filters.city = urlParams.get('city') || '';
                    this.filters.gender = urlParams.get('gender') || '';
                    this.filters.status = urlParams.get('status') || '';
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
