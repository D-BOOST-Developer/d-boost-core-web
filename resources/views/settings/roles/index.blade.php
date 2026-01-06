<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Role Management') }}
            </h2>
            <a href="{{ route('settings.roles.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Role
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto sm:px-4 lg:px-6 xl:px-8 2xl:px-10">
            <!-- Filters -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6" 
                 x-data="roleFilters()">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-5">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                               x-model="filters.search"
                               @input.debounce.300ms="applyFilters()"
                               placeholder="Search roles..."
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
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

                    <!-- System Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select x-model="filters.system" 
                                @change="applyFilters()"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
                            <option value="">All Types</option>
                            <option value="system">System</option>
                            <option value="custom">Custom</option>
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

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-3 gap-8">
                @forelse($roles as $role)
                    <div class="bg-white rounded-xl border border-gray-200 hover:border-gray-300 transition-all duration-300 overflow-hidden">
                        <!-- Role Header -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $role->display_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $role->name }}</p>
                                </div>

                                <!-- Status Badge -->
                                <div class="flex items-center space-x-2">
                                    @if($role->is_system)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">System</span>
                                    @endif
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $role->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($role->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                    {{ $role->description }}
                                </p>
                            @endif

                            <!-- Role Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ $role->userRoles->where('is_active', true)->count() }}</div>
                                    <div class="text-xs text-gray-600">Users</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ $role->roleMenus->where('is_active', true)->count() }}</div>
                                    <div class="text-xs text-gray-600">Menus</div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Actions -->
                        <div class="px-6 pb-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('settings.roles.show', $role) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    
                                    <a href="{{ route('settings.roles.edit', $role) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </div>

                                <!-- Permissions Link -->
                                <a href="{{ route('settings.roles.permissions', $role) }}" 
                                   class="inline-flex items-center px-3 py-2 text-sm bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Permissions
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No roles found</h3>
                            <p class="text-gray-600 mb-4">Get started by creating your first role.</p>
                            <a href="{{ route('settings.roles.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Role
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($roles->hasPages())
                <div class="mt-8">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function roleFilters() {
            return {
                filters: {
                    search: '{{ request("search") }}',
                    status: '{{ request("status") }}',
                    system: '{{ request("system") }}'
                },
                loading: false,

                init() {
                    this.updateFromUrl();
                },

                updateFromUrl() {
                    const params = new URLSearchParams(window.location.search);
                    this.filters.search = params.get('search') || '';
                    this.filters.status = params.get('status') || '';
                    this.filters.system = params.get('system') || '';
                },

                applyFilters() {
                    this.loading = true;
                    
                    const params = new URLSearchParams();
                    
                    if (this.filters.search) params.set('search', this.filters.search);
                    if (this.filters.status) params.set('status', this.filters.status);
                    if (this.filters.system) params.set('system', this.filters.system);
                    
                    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.location.href = url;
                }
            }
        }
    </script>
</x-app-layout>

