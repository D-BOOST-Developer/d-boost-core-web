<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ $salesReturn->return_number }}
            </h2>
            <div class="flex space-x-3">
                @if($salesReturn->status === 'pending')
                    <a href="{{ route('finance.sales-returns.edit', $salesReturn) }}" 
                       class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Edit
                    </a>
                @endif
                <a href="{{ route('finance.sales-returns.index') }}" 
                   class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Return Info -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $salesReturn->return_number }}</h1>
                                <p class="text-gray-600 dark:text-gray-400">{{ $salesReturn->return_date->format('F d, Y') }}</p>
                            </div>
                            <span class="px-3 py-1 text-sm rounded-full 
                                {{ $salesReturn->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400' : '' }}
                                {{ $salesReturn->status === 'approved' ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : '' }}
                                {{ $salesReturn->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' : '' }}
                                {{ $salesReturn->status === 'completed' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400' : '' }}">
                                {{ ucfirst($salesReturn->status) }}
                            </span>
                        </div>


                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Original Sale</h3>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $salesReturn->sale->invoice_number }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $salesReturn->sale->sale_date->format('M d, Y') }}</p>
                            </div>

                        @if($salesReturn->notes)
                            <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700/30 rounded-lg">
                                <h4 class="text-gray-900 dark:text-white font-medium mb-2">Notes</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ $salesReturn->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Return Items -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Return Items</h3>
                        <table class="w-full">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="text-left text-gray-700 dark:text-gray-300 pb-3">Product</th>
                                    <th class="text-center text-gray-700 dark:text-gray-300 pb-3">Qty</th>
                                    <th class="text-right text-gray-700 dark:text-gray-300 pb-3">Price</th>
                                    <th class="text-right text-gray-700 dark:text-gray-300 pb-3">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($salesReturn->items as $item)
                                    <tr>
                                        <td class="py-4 text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                        <td class="py-4 text-center text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                                        <td class="py-4 text-right text-gray-900 dark:text-white">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="py-4 text-right text-[#006BFF] font-medium">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Summary -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Reason:</span>
                                <span class="text-gray-900 dark:text-white">{{ $salesReturn->getReasonLabel() }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span class="text-gray-900 dark:text-white">Total:</span>
                                    <span class="text-[#006BFF]">Rp {{ number_format($salesReturn->total_return_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                        <div class="space-y-3">
                            @if($salesReturn->status === 'pending')
                                <form action="{{ route('finance.sales-returns.approve', $salesReturn) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-medium py-2 px-4 rounded-lg transition">
                                        Approve Return
                                    </button>
                                </form>
                                <button type="button" onclick="openDeleteModal('{{ route('finance.sales-returns.destroy', $salesReturn) }}', 'Delete Return', 'Delete this return?')"
                                        class="w-full bg-red-600 hover:bg-red-500 text-white font-medium py-2 px-4 rounded-lg transition">
                                    Delete
                                </button>
                            @endif
                            @if($salesReturn->status === 'approved')
                                <form action="{{ route('finance.sales-returns.complete', $salesReturn) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-lg transition">
                                        Mark as Completed
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
