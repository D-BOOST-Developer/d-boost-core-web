<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Customer Segments') }}
            </h2>
            <a href="{{ route('customers.segments.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Create Segment</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Total Segments</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $stats['total_segments'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Auto Segments</p>
                    <p class="text-blue-600 text-2xl font-bold mt-1">{{ $stats['auto_segments'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Manual Segments</p>
                    <p class="text-green-600 text-2xl font-bold mt-1">{{ $stats['manual_segments'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-600 text-sm">Total Customers</p>
                    <p class="text-purple-600 text-2xl font-bold mt-1">{{ $stats['total_customers'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Segments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($segments as $segment)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300 transition-all duration-200 shadow-sm">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $segment->name }}</h3>
                                <p class="text-gray-500 text-sm mt-1">{{ ucfirst($segment->segment_type) }} Segment</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($segment->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4">{{ $segment->description ?? 'No description' }}</p>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-gray-600 text-xs">Customers</p>
                                <p class="text-gray-900 text-lg font-bold">{{ $segment->customers_count ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-xs">Last Updated</p>
                                <p class="text-gray-900 text-sm">{{ $segment->last_refreshed_at?->format('M d') ?? 'Never' }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('customers.segments.show', $segment) }}" class="flex-1 text-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition-colors text-sm font-medium">
                                View
                            </a>
                            <a href="{{ route('customers.segments.edit', $segment) }}" class="flex-1 text-center px-3 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition-colors text-sm font-medium">
                                Edit
                            </a>
                            @if($segment->segment_type === 'auto')
                                <form action="{{ route('customers.segments.refresh', $segment) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg transition-colors text-sm font-medium">
                                        Refresh
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-700">No segments</h3>
                        <p class="mt-1 text-sm text-gray-500">Create your first customer segment to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
