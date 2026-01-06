<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('User Management') }}
            </h2>
            <a href="{{ route('settings.users.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto sm:px-4 lg:px-6 xl:px-8 2xl:px-10">
            <!-- Filters -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6" 
                 x-data="userFilters()">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                               x-model="filters.search"
                               @input.debounce.300ms="applyFilters()"
                               placeholder="Search users..."
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <select x-model="filters.role" 
                                @change="applyFilters()"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent text-base">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
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

            <!-- Users Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-[#006BFF] to-cyan-400 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-sm font-bold">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($user->userRoles->where('is_active', true)->take(3) as $userRole)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                    {{ $userRole->role->display_name }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-600">No roles</span>
                                            @endforelse
                                            @if($user->userRoles->where('is_active', true)->count() > 3)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                    +{{ $user->userRoles->where('is_active', true)->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('settings.users.show', $user) }}" 
                                               class="text-blue-600 hover:text-blue-700 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('settings.users.edit', $user) }}" 
                                               class="text-yellow-600 hover:text-yellow-700 transition-colors">
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
                                        <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                                        <p class="text-gray-600 mb-4">Get started by creating your first user.</p>
                                        <a href="{{ route('settings.users.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create User
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-8">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function userFilters() {
            return {
                filters: {
                    search: '{{ request("search") }}',
                    role: '{{ request("role") }}'
                },
                loading: false,

                init() {
                    this.updateFromUrl();
                },

                updateFromUrl() {
                    const params = new URLSearchParams(window.location.search);
                    this.filters.search = params.get('search') || '';
                    this.filters.role = params.get('role') || '';
                },

                applyFilters() {
                    this.loading = true;
                    
                    const params = new URLSearchParams();
                    
                    if (this.filters.search) params.set('search', this.filters.search);
                    if (this.filters.role) params.set('role', this.filters.role);
                    
                    const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.location.href = url;
                }
            }
        }
    </script>
</x-app-layout>

