<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Client Reviews
            </h2>
            <a href="{{ route('storefront.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                ← Back to Storefront
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Reviews</p>
            <p class="text-2xl font-bold text-gray-900">{{ $reviews->total() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
            <p class="text-sm text-green-600">Approved</p>
            <p class="text-2xl font-bold text-green-700">{{ $config->reviews()->where('is_approved', true)->count() }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
            <p class="text-sm text-yellow-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $config->reviews()->where('is_approved', false)->count() }}</p>
        </div>
    </div>

    <!-- Reviews List -->
    @if($reviews->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($reviews as $review)
                            <tr id="review-{{ $review->id }}" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $review->client_name }}</p>
                                        @if($review->client_email)
                                            <p class="text-sm text-gray-500">{{ $review->client_email }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 text-yellow-400">
                                        @for($i = 0; $i < $review->rating; $i++)
                                            <span>★</span>
                                        @endfor
                                        @for($i = $review->rating; $i < 5; $i++)
                                            <span class="text-gray-300">★</span>
                                        @endfor
                                        <span class="ml-1 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-md">
                                    <p class="text-sm text-gray-700 line-clamp-2">{{ $review->review_text }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($review->is_approved)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $review->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2" x-data>
                                        @if($review->is_approved)
                                            <button @click="$dispatch('open-confirm-modal', {
                                                        title: 'Unapprove Review',
                                                        message: 'Are you sure you want to unapprove this review? It will be hidden from your storefront.',
                                                        confirmText: 'Unapprove',
                                                        confirmColor: 'yellow',
                                                        icon: 'warning',
                                                        action: '{{ route('storefront.reviews.reject', $review->id) }}'
                                                    })" 
                                                    class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 hover:bg-yellow-200 rounded transition-colors">
                                                Unapprove
                                            </button>
                                        @else
                                            <button @click="$dispatch('open-confirm-modal', {
                                                        title: 'Approve Review',
                                                        message: 'Are you sure you want to approve this review? It will be visible on your storefront.',
                                                        confirmText: 'Approve',
                                                        confirmColor: 'green',
                                                        icon: 'check',
                                                        action: '{{ route('storefront.reviews.approve', $review->id) }}'
                                                    })" 
                                                    class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded transition-colors">
                                                Approve
                                            </button>
                                        @endif
                                        <button @click="$dispatch('open-delete-modal', {
                                                    title: 'Delete Review',
                                                    message: 'Are you sure you want to permanently delete this review? This action cannot be undone.',
                                                    action: '{{ route('storefront.reviews.destroy', $review->id) }}'
                                                })" 
                                                class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded transition-colors">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Reviews Yet</h3>
            <p class="text-gray-500">Client reviews will appear here once submitted through your storefront.</p>
            </div>
        @endif
    </div>

    <!-- Modals -->
    <x-confirm-modal />
    <x-delete-modal />
</x-app-layout>
