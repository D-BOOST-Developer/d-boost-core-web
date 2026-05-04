<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction Details') }}
            </h2>
            <div class="flex gap-3">
                @if($transaction->status === 'pending')
                    <button type="button" x-data
                            @click="$dispatch('open-confirm-modal', {
                                title: 'Approve Transaction',
                                message: 'Are you sure you want to approve this transaction? Stock will be updated accordingly.',
                                confirmText: 'Approve Transaction',
                                action: '{{ route('inventory.transactions.approve', $transaction) }}'
                            })"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approve
                    </button>
                    <button type="button" x-data
                            @click="$dispatch('open-reject-modal', {
                                action: '{{ route('inventory.transactions.reject', $transaction) }}'
                            })"
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Reject
                    </button>
                @endif
                <a href="{{ route('inventory.transactions.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6 max-w-4xl mx-auto">
            <!-- Status Alert -->
            @if($transaction->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-yellow-800 font-medium">This transaction is pending approval. Stock has not been updated yet.</p>
                    </div>
                </div>
            @elseif($transaction->status === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-red-800 font-medium">This transaction was rejected.</p>
                            @if($transaction->rejection_reason)
                                <p class="text-red-700 text-sm mt-1"><strong>Reason:</strong> {{ $transaction->rejection_reason }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Transaction Information</h3>
                        {!! $transaction->status_badge !!}
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Product Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-gray-600">Product Name</label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->product->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">SKU</label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->product->sku }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Category</label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->product->category ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Transaction Details</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-gray-600">Transaction Type</label>
                                    <p class="text-base font-medium">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($transaction->isIncrease()) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $transaction->transaction_type_name }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Date & Time</label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->transaction_date->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Reference Type</label>
                                    <p class="text-base font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $transaction->reference_type)) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Stock Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-gray-600">Quantity</label>
                                    <p class="text-2xl font-bold
                                        @if($transaction->isIncrease()) text-green-600
                                        @else text-red-600
                                        @endif">
                                        {{ $transaction->isIncrease() ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Stock Before</label>
                                    <p class="text-base font-medium text-gray-900">{{ number_format($transaction->stock_before) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Stock After</label>
                                    <p class="text-base font-medium text-gray-900">{{ number_format($transaction->stock_after) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Information -->
                        @if($transaction->unit_cost || $transaction->total_cost)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Cost Information</h4>
                            <div class="space-y-3">
                                @if($transaction->unit_cost)
                                <div>
                                    <label class="text-sm text-gray-600">Unit Cost</label>
                                    <p class="text-base font-medium text-gray-900">Rp {{ number_format($transaction->unit_cost, 0, ',', '.') }}</p>
                                </div>
                                @endif
                                @if($transaction->total_cost)
                                <div>
                                    <label class="text-sm text-gray-600">Total Cost</label>
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Reason & Notes -->
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Additional Information</h4>
                            <div class="space-y-3">
                                @if($transaction->reason)
                                <div>
                                    <label class="text-sm text-gray-600">Reason</label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->reason_label }}</p>
                                </div>
                                @endif
                                @if($transaction->notes)
                                <div>
                                    <label class="text-sm text-gray-600">Notes</label>
                                    <p class="text-base text-gray-900">{{ $transaction->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Audit Information -->
                        <div class="md:col-span-2 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Audit Trail</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600">Created By</label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->creator->name ?? 'System' }}</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                @if($transaction->approver)
                                <div>
                                    <label class="text-sm text-gray-600">
                                        @if($transaction->status === 'approved')
                                            Approved By
                                        @elseif($transaction->status === 'rejected')
                                            Rejected By
                                        @else
                                            Reviewed By
                                        @endif
                                    </label>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->approver->name }}</p>
                                    @if($transaction->approved_at)
                                        <p class="text-sm text-gray-500">{{ $transaction->approved_at->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

