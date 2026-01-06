<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Marketplace Links
            </h2>
            <a href="{{ route('storefront.links.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Link
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6" x-data="filterData()">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()"
                               placeholder="Search links..." 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Type</label>
                        <select x-model="filters.type" @change="applyFilters()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                            <option value="">All Types</option>
                            <option value="marketplace_profile">Marketplace Profile</option>
                            <option value="product">Specific Product</option>
                        </select>
                    </div>

                    <!-- Platform Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                        <select x-model="filters.platform" @change="applyFilters()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                            <option value="">All Platforms</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform }}">{{ ucfirst($platform) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div x-show="filtering" x-transition class="mt-4 flex items-center justify-center text-sm text-gray-600">
                    <svg class="animate-spin h-4 w-4 mr-2 text-[#006BFF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Filtering...
                </div>
            </div>

            <!-- Links Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($links as $link)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                        <!-- Image -->
                        <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                            @if($link->hasImage())
                                <img src="{{ $link->image_url }}" alt="{{ $link->display_name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-6xl">{{ $link->platform_icon }}</span>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-gray-900 font-semibold text-lg truncate flex-1">{{ $link->display_name }}</h3>
                                <span class="px-2 py-1 text-xs rounded-full {{ $link->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $link->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <p class="text-gray-600 text-sm line-clamp-2">{{ $link->description }}</p>

                            <div class="flex items-center gap-2 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ ucfirst($link->platform) }}</span>
                                <span class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $link->link_type)) }}</span>
                            </div>

                            @if($link->price)
                                <p class="text-[#006BFF] font-bold">{{ $link->formatted_price }}</p>
                            @endif

                            <div class="pt-2 border-t border-gray-200">
                                <p class="text-gray-500 text-xs">{{ $link->click_count }} clicks</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2 pt-2">
                                <a href="{{ $link->url }}" target="_blank" 
                                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 text-sm font-medium py-2 px-3 rounded-lg text-center">
                                    View
                                </a>
                                <a href="{{ route('storefront.links.edit', $link) }}" 
                                   class="flex-1 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white text-sm font-medium py-2 px-3 rounded-lg text-center">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="openDeleteModal('{{ route('storefront.links.destroy', $link) }}', 'Delete Link', 'Are you sure you want to delete this link? This action cannot be undone.')"
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-3 rounded-lg">
                                    Delete
                                </button>
                            </div>

                            <form action="{{ route('storefront.links.toggle', $link) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full mt-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium py-2 px-3 rounded-lg">
                                    {{ $link->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <h3 class="text-gray-900 text-lg font-medium mb-2">No Links Yet</h3>
                            <p class="text-gray-600 mb-4">Start adding your marketplace or product links</p>
                            <a href="{{ route('storefront.links.create') }}" 
                               class="inline-flex items-center bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-4 rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add First Link
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($links->hasPages())
                <div class="mt-8">
                    {{ $links->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function filterData() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    type: '{{ request('type') }}',
                    platform: '{{ request('platform') }}',
                    status: '{{ request('status') }}'
                },
                filtering: false,

                applyFilters() {
                    this.filtering = true;

                    // Build query string
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key]) {
                            params.set(key, this.filters[key]);
                        }
                    });

                    // Small delay before redirect
                    setTimeout(() => {
                        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.location.href = newUrl;
                    }, 300);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
