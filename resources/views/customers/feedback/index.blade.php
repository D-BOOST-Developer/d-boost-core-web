<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Customer Feedback') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-700 text-sm">Total Feedback</p>
                    <p class="text-gray-900 text-2xl font-bold mt-1">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-700 text-sm">Average Rating</p>
                    <p class="text-yellow-600 text-2xl font-bold mt-1">{{ number_format($stats['avg_rating'] ?? 0, 1) }}/5</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-700 text-sm">Positive</p>
                    <p class="text-green-600 text-2xl font-bold mt-1">{{ $stats['positive'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-700 text-sm">Negative</p>
                    <p class="text-red-600 text-2xl font-bold mt-1">{{ $stats['negative'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-gray-700 text-sm">Pending Response</p>
                    <p class="text-orange-600 text-2xl font-bold mt-1">{{ $stats['pending'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm"
                 x-data="feedbackFilters()" x-init="init()">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()"
                               placeholder="Search feedback..."
                               class="w-full bg-white border border-gray-300 text-gray-900 placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none"
                               autocomplete="off">
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <select x-model="filters.rating" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select x-model="filters.type" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="complaint">Complaint</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="praise">Praise</option>
                            <option value="question">Question</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending">Pending Response</option>
                            <option value="responded">Responded</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Feedback List -->
            <div class="space-y-4">
                @forelse($feedbacks as $feedback)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-400 transition-all duration-200 shadow-sm">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <a href="{{ route('customers.show', $feedback->customer) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                        {{ $feedback->customer->name }}
                                    </a>
                                    <span class="text-gray-600 text-sm">{{ $feedback->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Rating Stars -->
                                    <div class="flex items-center space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $feedback->rating)
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-gray-700 text-sm">{{ $feedback->rating }}/5</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300">
                                    {{ $feedback->feedback_type_label }}
                                </span>
                                @if($feedback->hasResponse())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300">
                                        Responded
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Comment -->
                        <p class="text-gray-700 mb-4">{{ $feedback->comment }}</p>

                        <!-- Product Info -->
                        @if($feedback->product)
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-gray-700 text-sm">Product: <span class="text-gray-900 font-medium">{{ $feedback->product->name }}</span></p>
                            </div>
                        @endif

                        <!-- Admin Response -->
                        @if($feedback->hasResponse())
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-green-700 text-sm font-medium mb-2">Admin Response:</p>
                                <p class="text-gray-700 text-sm">{{ $feedback->admin_response }}</p>
                                <p class="text-gray-500 text-xs mt-2">{{ $feedback->responded_at?->format('M d, Y H:i') }}</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('customers.feedback.show', $feedback) }}" class="px-3 py-2 bg-blue-900/50 hover:bg-blue-800/50 text-blue-300 rounded-lg transition-colors text-sm font-medium">
                                View
                            </a>
                            @if(!$feedback->hasResponse())
                                <a href="{{ route('customers.feedback.edit', $feedback) }}" class="px-3 py-2 bg-yellow-900/50 hover:bg-yellow-800/50 text-yellow-300 rounded-lg transition-colors text-sm font-medium">
                                    Respond
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-400">No feedback</h3>
                        <p class="mt-1 text-sm text-gray-500">No customer feedback found.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($feedbacks->hasPages())
                <div class="mt-6">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function feedbackFilters() {
            return {
                filters: {
                    search: '{{ request('search') }}',
                    rating: '{{ request('rating') }}',
                    type: '{{ request('type') }}',
                    status: '{{ request('status') }}',
                },
                loading: false,
                init() {
                    // Initialize from URL
                },
                applyFilters() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    
                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.rating) params.append('rating', this.filters.rating);
                    if (this.filters.type) params.append('type', this.filters.type);
                    if (this.filters.status) params.append('status', this.filters.status);
                    
                    window.location.href = `{{ route('customers.feedback.index') }}?${params.toString()}`;
                }
            }
        }
    </script>
</x-app-layout>
