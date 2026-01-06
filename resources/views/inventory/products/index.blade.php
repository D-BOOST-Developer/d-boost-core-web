<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Products Management') }}
            </h2>
            <a href="{{ route('inventory.products.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Add Product</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6" 
                 x-data="productFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" 
                               placeholder="Search products..." 
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent focus:outline-none"
                               autocomplete="off" spellcheck="false">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select x-model="filters.category" @change="applyFilters()" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div x-show="Alpine.store('app').loading" x-transition class="mt-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-lg py-3 px-4 border border-gray-200 dark:border-gray-600">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-[#006BFF] border-t-transparent"></div>
                    <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium" x-text="Alpine.store('app').loadingMessage || 'Loading...'">Applying filters...</span>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" x-data>
                @forelse($products as $product)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300">
                        <!-- Product Image -->
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-gray-900 dark:text-white font-semibold text-lg truncate flex-1">{{ $product->name }}</h3>
                                <form action="{{ route('inventory.products.toggle-status', $product) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                            class="px-2.5 py-1 text-xs font-semibold rounded-full whitespace-nowrap transition-colors duration-200 {{ $product->is_active ? 'bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-400' : 'bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400' }}"
                                            title="Click to toggle status">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </div>

                            <p class="text-gray-600 dark:text-gray-400 text-sm">SKU: {{ $product->sku }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Category: {{ $product->category }}</p>
                            
                            <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <div>
                                    <p class="text-[#006BFF] dark:text-[#00FF9C] font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">Cost: Rp {{ number_format($product->cost, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-900 dark:text-white font-medium">Stock: {{ $product->stock_quantity }}</p>
                                    @if($product->isLowStock())
                                        <p class="text-red-600 dark:text-red-400 text-xs font-semibold">Low Stock!</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Stock Actions -->
                            <div class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" 
                                        @click="$dispatch('open-quick-stock', { type: 'in', productId: '{{ $product->id }}' })"
                                        class="flex-1 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/30 text-green-700 dark:text-green-400 text-sm font-medium py-2 px-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Stock In
                                </button>
                                <button type="button" 
                                        @click="$dispatch('open-quick-stock', { type: 'out', productId: '{{ $product->id }}' })"
                                        class="flex-1 bg-orange-50 hover:bg-orange-100 dark:bg-orange-900/20 dark:hover:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-sm font-medium py-2 px-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    Stock Out
                                </button>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2 pt-3">
                                <a href="{{ route('inventory.products.show', $product) }}" 
                                   class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-sm font-medium py-2 px-3 rounded-lg text-center transition-all duration-200">
                                    View
                                </a>
                                <a href="{{ route('inventory.products.edit', $product) }}" 
                                   class="flex-1 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white text-sm font-medium py-2 px-3 rounded-lg text-center transition-all duration-200">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="text-gray-900 dark:text-white text-lg font-medium mb-2">No Products Found</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by adding your first product.</p>
                            <a href="{{ route('inventory.products.create') }}" 
                               class="inline-flex items-center bg-[#006BFF] hover:bg-[#006BFF]/80 text-gray-900 font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Product
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif

            {{-- Quick Stock Modal --}}
            <x-inventory.quick-stock-modal />
        </div>
    </div>

    @push('scripts')
    <script>
        function productFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    category: '{{ request('category') }}',
                    status: '{{ request('status') }}'
                },

                init() {
                    this.updateFromUrl();
                },

                updateFromUrl() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.filters.search = urlParams.get('search') || '';
                    this.filters.category = urlParams.get('category') || '';
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
