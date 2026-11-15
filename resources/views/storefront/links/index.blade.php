<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#F1F0FF] leading-tight">
                Manage Marketplace Links
            </h2>
            <a href="{{ route('storefront.links.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#A78BFA] hover:bg-[#9333EA] text-[#0A0A0F] font-bold rounded-lg transition shadow-lg shadow-[#A78BFA]/20">
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
            <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-6 mb-6 shadow-lg" x-data="filterData()">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()"
                               placeholder="Search links..." 
                               class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA] focus:border-transparent placeholder-[#7E7D9C]">
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Link Type</label>
                        <select x-model="filters.type" @change="applyFilters()"
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA]">
                            <option value="">All Types</option>
                            <option value="marketplace_profile">Marketplace Profile</option>
                            <option value="product">Specific Product</option>
                        </select>
                    </div>

                    <!-- Platform Filter -->
                    <div>
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Platform</label>
                        <select x-model="filters.platform" @change="applyFilters()"
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA]">
                            <option value="">All Platforms</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform }}">{{ ucfirst($platform) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()"
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA]">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div x-show="filtering" x-transition class="mt-4 flex items-center justify-center text-sm text-[#A9A8C3]">
                    <svg class="animate-spin h-4 w-4 mr-2 text-[#A78BFA]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Filtering...
                </div>
            </div>

            <!-- Links Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($links as $link)
                    <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-6 hover:border-[#A78BFA]/50 transition-all shadow-lg flex flex-col justify-between">
                        <div>
                            <!-- Image -->
                            <div class="w-full h-48 bg-[#111118] border border-[#2A2A3A] rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                                @if($link->hasImage())
                                    <img src="{{ $link->image_url }}" alt="{{ $link->display_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-6xl">{{ $link->platform_icon }}</span>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-[#F1F0FF] font-semibold text-lg truncate flex-1">{{ $link->display_name }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full border {{ $link->is_active ? 'bg-emerald-950/40 text-emerald-400 border-emerald-800/40' : 'bg-red-950/40 text-red-400 border-red-800/40' }}">
                                        {{ $link->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <p class="text-[#A9A8C3] text-sm line-clamp-2">{{ $link->description }}</p>

                                <div class="flex items-center gap-2 text-sm">
                                    <span class="px-2 py-1 bg-[#252535] text-[#A78BFA] border border-[#3A3A4E] rounded">{{ ucfirst($link->platform) }}</span>
                                    <span class="text-[#7E7D9C]">{{ ucfirst(str_replace('_', ' ', $link->link_type)) }}</span>
                                </div>

                                @if($link->price)
                                    <p class="text-[#A78BFA] font-bold">{{ $link->formatted_price }}</p>
                                @endif

                                <div class="pt-2 border-t border-[#2A2A3A]">
                                    <p class="text-[#7E7D9C] text-xs">{{ $link->click_count }} clicks</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-4">
                            <div class="flex space-x-2 pt-2">
                                <a href="{{ $link->url }}" target="_blank" 
                                   class="flex-1 bg-[#252535] hover:bg-[#2F2F42] text-[#F1F0FF] text-sm font-medium py-2 px-3 rounded-lg text-center border border-[#3A3A4E] transition-colors">
                                    View
                                </a>
                                <a href="{{ route('storefront.links.edit', $link) }}" 
                                   class="flex-1 bg-[#A78BFA] hover:bg-[#9333EA] text-[#0A0A0F] text-sm font-bold py-2 px-3 rounded-lg text-center transition-colors">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="openDeleteModal('{{ route('storefront.links.destroy', $link) }}', 'Delete Link', 'Are you sure you want to delete this link? This action cannot be undone.')"
                                        class="flex-1 bg-red-600/90 hover:bg-red-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                    Delete
                                </button>
                            </div>

                            <form action="{{ route('storefront.links.toggle', $link) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full mt-2 border border-[#2A2A3A] bg-[#111118] hover:bg-[#252535] text-[#A9A8C3] hover:text-[#F1F0FF] text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                    {{ $link->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-12 text-center shadow-lg">
                            <svg class="w-16 h-16 text-[#7E7D9C] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <h3 class="text-[#F1F0FF] text-lg font-medium mb-2">No Links Yet</h3>
                            <p class="text-[#A9A8C3] mb-4">Start adding your marketplace or product links</p>
                            <a href="{{ route('storefront.links.create') }}" 
                               class="inline-flex items-center bg-[#A78BFA] hover:bg-[#9333EA] text-[#0A0A0F] font-bold py-2 px-4 rounded-lg shadow-lg shadow-[#A78BFA]/20 transition-all">
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
