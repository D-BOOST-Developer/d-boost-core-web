<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#F1F0FF] leading-tight">
                Client Reviews
            </h2>
            <a href="{{ route('storefront.index') }}" class="px-4 py-2 bg-[#1A1A24] hover:bg-[#252535] text-[#A9A8C3] hover:text-[#F1F0FF] border border-[#2A2A3A] rounded-lg transition-colors text-sm font-medium">
                ← Back to Storefront
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-[#1A1A24] p-5 rounded-xl shadow-lg border border-[#2A2A3A]">
                <p class="text-sm font-medium text-[#A9A8C3] mb-1">Total Reviews</p>
                <p class="text-3xl font-bold text-[#F1F0FF]">{{ $reviews->total() }}</p>
            </div>
            <div class="bg-emerald-950/40 p-5 rounded-xl shadow-lg border border-emerald-800/40">
                <p class="text-sm font-medium text-emerald-400 mb-1">Approved</p>
                <p class="text-3xl font-bold text-emerald-300">{{ $config->reviews()->where('is_approved', true)->count() }}</p>
            </div>
            <div class="bg-amber-950/40 p-5 rounded-xl shadow-lg border border-amber-800/40">
                <p class="text-sm font-medium text-amber-400 mb-1">Pending</p>
                <p class="text-3xl font-bold text-amber-300">{{ $config->reviews()->where('is_approved', false)->count() }}</p>
            </div>
        </div>

        <!-- Reviews List -->
        @if($reviews->count() > 0)
            <div class="bg-[#1A1A24] rounded-xl shadow-lg border border-[#2A2A3A] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#111118] border-b border-[#2A2A3A]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#7E7D9C] uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#7E7D9C] uppercase tracking-wider">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#7E7D9C] uppercase tracking-wider">Review</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#7E7D9C] uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#7E7D9C] uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#7E7D9C] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2A2A3A]">
                            @foreach($reviews as $review)
                                <tr id="review-{{ $review->id }}" class="hover:bg-[#252535]/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-medium text-[#F1F0FF]">{{ $review->client_name }}</p>
                                            @if($review->client_email)
                                                <p class="text-xs text-[#7E7D9C]">{{ $review->client_email }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1 text-amber-400">
                                            @for($i = 0; $i < $review->rating; $i++)
                                                <span>★</span>
                                            @endfor
                                            @for($i = $review->rating; $i < 5; $i++)
                                                <span class="text-[#3A3A4E]">★</span>
                                            @endfor
                                            <span class="ml-1 text-xs text-[#A9A8C3]">({{ $review->rating }}/5)</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 max-w-md">
                                        <p class="text-sm text-[#A9A8C3] line-clamp-2">{{ $review->review_text }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($review->is_approved)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-950/50 text-emerald-300 border border-emerald-800/50">
                                                ✓ Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-950/50 text-amber-300 border border-amber-800/50">
                                                ⏳ Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-[#7E7D9C]">
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
                                                        class="px-3 py-1 text-xs font-medium text-amber-300 bg-amber-950/40 hover:bg-amber-900/50 border border-amber-800/40 rounded-lg transition-colors">
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
                                                        class="px-3 py-1 text-xs font-medium text-emerald-300 bg-emerald-950/40 hover:bg-emerald-900/50 border border-emerald-800/40 rounded-lg transition-colors">
                                                    Approve
                                                </button>
                                            @endif
                                            <button @click="$dispatch('open-delete-modal', {
                                                        title: 'Delete Review',
                                                        message: 'Are you sure you want to permanently delete this review? This action cannot be undone.',
                                                        action: '{{ route('storefront.reviews.destroy', $review->id) }}'
                                                    })" 
                                                    class="px-3 py-1 text-xs font-medium text-red-300 bg-red-950/40 hover:bg-red-900/50 border border-red-800/40 rounded-lg transition-colors">
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
            <div class="bg-[#1A1A24] rounded-xl shadow-lg border border-[#2A2A3A] p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-[#7E7D9C] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <h3 class="text-lg font-medium text-[#F1F0FF] mb-2">No Reviews Yet</h3>
                <p class="text-[#A9A8C3]">Client reviews will appear here once submitted through your storefront.</p>
            </div>
        @endif
    </div>

    <!-- Modals -->
    <x-confirm-modal />
    <x-delete-modal />
</x-app-layout>
