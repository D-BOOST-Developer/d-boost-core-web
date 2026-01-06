<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Audit Logs') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full border border-red-200">
                    Admin Only
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6" 
                 x-data="auditLogFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" 
                               placeholder="Table, action, user..." 
                               class="w-full bg-white border border-gray-300 text-gray-900 placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent focus:outline-none"
                               autocomplete="off" spellcheck="false">
                    </div>

                    <!-- Table Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Table</label>
                        <select x-model="filters.table" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Tables</option>
                            @foreach($tables as $table)
                                <option value="{{ $table }}">{{ ucfirst($table) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select x-model="filters.action" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Actions</option>
                            <option value="create">Created</option>
                            <option value="update">Updated</option>
                            <option value="delete">Deleted</option>
                        </select>
                    </div>

                    <!-- User Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                        <select x-model="filters.user" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <input type="date" x-model="filters.date_from" @change="applyFilters()" 
                               class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent mb-2"
                               placeholder="From date">
                        <input type="date" x-model="filters.date_to" @change="applyFilters()" 
                               class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent"
                               placeholder="To date">
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div x-show="Alpine.store('app').loading" x-transition class="mt-4 flex items-center justify-center bg-white rounded-lg py-3 px-4 border border-gray-300">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-[#006BFF] border-t-transparent"></div>
                    <span class="ml-3 text-gray-600 font-medium" x-text="Alpine.store('app').loadingMessage || 'Loading...'">Applying filters...</span>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                @if($auditLogs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Table</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Record ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">IP Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($auditLogs as $log)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <!-- Timestamp -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $log->performed_at->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $log->performed_at->format('H:i:s') }}</div>
                                        </td>

                                        <!-- User -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-800 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-xs font-bold">{{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}</span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $log->user->role ?? 'system' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Action -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $log->action === 'create' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                                   ($log->action === 'update' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                                    'bg-red-100 text-red-700 border border-red-200') }}">
                                                {{ ucfirst($log->action) }}
                                        </td>

                                        <!-- Table -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 border border-purple-200">
                                                {{ ucfirst($log->table_name) }}
                                            </span>
                                        </td>

                                        <!-- Record ID -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-mono">{{ substr($log->record_id, 0, 8) }}...</div>
                                        </td>

                                        <!-- IP Address -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $log->ip_address ?: 'N/A' }}</div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('audit-logs.show', $log) }}" 
                                               class="text-gray-500 hover:text-[#006BFF] transition-colors duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-gray-900 text-lg font-medium mb-2">No Audit Logs Found</h3>
                        <p class="text-gray-600">No audit logs match your current filters.</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($auditLogs->hasPages())
                <div class="mt-6">
                    {{ $auditLogs->withQueryString()->links() }}
                </div>
            @endif

            <!-- Statistics -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-green-600">{{ $auditLogs->where('action', 'create')->count() }}</p>
                            <p class="text-gray-600 text-sm">Created</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-blue-600">{{ $auditLogs->where('action', 'update')->count() }}</p>
                            <p class="text-gray-600 text-sm">Updated</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-red-600">{{ $auditLogs->where('action', 'delete')->count() }}</p>
                            <p class="text-gray-600 text-sm">Deleted</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-purple-600">{{ $auditLogs->total() }}</p>
                            <p class="text-gray-600 text-sm">Total Logs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function auditLogFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    table: '{{ request('table') }}',
                    action: '{{ request('action') }}',
                    user: '{{ request('user') }}',
                    date_from: '{{ request('date_from') }}',
                    date_to: '{{ request('date_to') }}'
                },

                init() {
                    // Initialize filters from URL params
                    this.updateFromUrl();
                },

                updateFromUrl() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.filters.search = urlParams.get('search') || '';
                    this.filters.table = urlParams.get('table') || '';
                    this.filters.action = urlParams.get('action') || '';
                    this.filters.user = urlParams.get('user') || '';
                    this.filters.date_from = urlParams.get('date_from') || '';
                    this.filters.date_to = urlParams.get('date_to') || '';
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
