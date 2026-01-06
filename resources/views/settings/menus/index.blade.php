<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Menus Management') }}
            </h2>
            <a href="{{ route('settings.menus.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Menu
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto sm:px-4 lg:px-6 xl:px-8 2xl:px-10" x-data="menuFilters()">
            <!-- Filters -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-5">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                               x-model="filters.search"
                               @input.debounce.300ms="applyFilters()"
                               placeholder="Search menus..."
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
                    </div>

                    <!-- Module Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
                        <select x-model="filters.module"
                                @change="applyFilters()"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}">{{ $module->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="filters.status" 
                                @change="applyFilters()"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Loading Indicator -->
                    <div class="flex items-end">
                        <div x-show="loading" class="flex items-center text-[#006BFF]">
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Filtering...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menus Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Menu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $menu->display_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $menu->description }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700">{{ $menu->module?->display_name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700">{{ $menu->route ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700">{{ $menu->sort_order }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('settings.menus.show', $menu) }}" 
                                               class="text-blue-600 hover:text-blue-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('settings.menus.edit', $menu) }}" 
                                               class="text-yellow-600 hover:text-yellow-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No menus found</h3>
                                        <p class="text-gray-600 mb-4">Get started by creating your first menu.</p>
                                        <a href="{{ route('settings.menus.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Menu
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($menus->hasPages())
                <div class="mt-8">
                    {{ $menus->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function menuFilters() {
            return {
                filters: {
                    search: '{{ request("search") }}',
                    module: '{{ request("module") }}',
                    status: '{{ request("status") }}'
                },
                loading: false,

                applyFilters() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    if (this.filters.search) params.set('search', this.filters.search);
                    if (this.filters.module) params.set('module', this.filters.module);
                    if (this.filters.status) params.set('status', this.filters.status);
                    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.location.href = url;
                }
            }
        }
    </script>
</x-app-layout>
