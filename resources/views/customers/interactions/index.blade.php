<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Customer Interactions') }}
            </h2>
            <a href="{{ route('customers.interactions.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Add Interaction</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Total Interactions</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $interactions->total() ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Pending</p>
                    <p class="text-yellow-600 text-2xl font-bold mt-1">{{ $interactions->where('status', 'pending')->count() ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Scheduled</p>
                    <p class="text-blue-600 text-2xl font-bold mt-1">{{ $interactions->where('status', 'scheduled')->count() ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Completed</p>
                    <p class="text-green-600 text-2xl font-bold mt-1">{{ $interactions->where('status', 'completed')->count() ?? 0 }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm" 
                 x-data="interactionFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" 
                               placeholder="Search interactions..." 
                               class="w-full bg-white border border-gray-300 text-gray-900 placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none"
                               autocomplete="off">
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select x-model="filters.type" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="meeting">Meeting</option>
                            <option value="chat">Chat</option>
                            <option value="note">Note</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select x-model="filters.priority" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Priorities</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Interactions Table -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($interactions as $interaction)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('customers.show', $interaction->customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $interaction->customer->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($interaction->interaction_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-900 text-sm">{{ $interaction->subject }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($interaction->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif($interaction->status === 'scheduled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Scheduled
                                            </span>
                                        @elseif($interaction->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($interaction->priority === 'high')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                High
                                            </span>
                                        @elseif($interaction->priority === 'medium')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Medium
                                            </span>
                                        @elseif($interaction->priority === 'urgent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Urgent
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Low
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $interaction->interaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <a href="{{ route('customers.interactions.show', $interaction) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                                        <a href="{{ route('customers.interactions.edit', $interaction) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No interactions found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($interactions->hasPages())
                <div class="mt-6">
                    {{ $interactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function interactionFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    type: '{{ request('type') }}',
                    status: '{{ request('status') }}',
                    priority: '{{ request('priority') }}',
                },
                loading: false,
                init() {
                    // Initialize from URL
                },
                applyFilters() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    
                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.type) params.append('type', this.filters.type);
                    if (this.filters.status) params.append('status', this.filters.status);
                    if (this.filters.priority) params.append('priority', this.filters.priority);
                    
                    window.location.href = `{{ route('customers.interactions.index') }}?${params.toString()}`;
                }
            }
        }
    </script>
</x-app-layout>
