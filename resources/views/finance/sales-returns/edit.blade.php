<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Edit Sales Return - {{$salesReturn->return_number}}
            </h2>
            <a href="{{ route('finance.sales-returns.show', $salesReturn) }}" 
               class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-6">
            @if($salesReturn->status !== 'pending')
                <div class="bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-500/30 text-yellow-700 dark:text-yellow-400 rounded-lg p-4 mb-6">
                    Only pending returns can be edited.
                </div>
            @endif

            <form method="POST" action="{{ route('finance.sales-returns.update', $salesReturn) }}">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Number</label>
                            <input type="text" value="{{ $salesReturn->return_number }}" readonly
                                   class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Date *</label>
                            <input type="date" name="return_date" id="return_date" value="{{ old('return_date', $salesReturn->return_date->format('Y-m-d')) }}" required
                                   class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                        </div>
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason *</label>
                            <select name="reason" id="reason" required
                                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                                <option value="defective" {{ $salesReturn->reason == 'defective' ? 'selected' : '' }}>Defective</option>
                                <option value="wrong_item" {{ $salesReturn->reason == 'wrong_item' ? 'selected' : '' }}>Wrong Item</option>
                                <option value="buyer_request" {{ $salesReturn->reason == 'buyer_request' ? 'selected' : '' }}>Buyer Request</option>
                                <option value="other" {{ $salesReturn->reason == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">{{ old('notes', $salesReturn->notes) }}</textarea>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Return Items (Read-only)</h3>
                    <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 mb-6">
                        @foreach($salesReturn->items as $item)
                            <div class="flex justify-between py-2 border-b border-gray-300 dark:border-gray-600 last:border-b-0">
                                <span class="text-gray-900 dark:text-white">{{ $item->product->name }}</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('finance.sales-returns.show', $salesReturn) }}" 
                           class="bg-gray-600 hover:bg-gray-500 text-white font-medium py-2 px-6 rounded-lg transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Update Return
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
