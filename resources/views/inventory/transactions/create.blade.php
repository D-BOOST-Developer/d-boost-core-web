<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Transaction') }}
            </h2>
            <a href="{{ route('inventory.transactions.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Transactions</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6 max-w-4xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form method="POST" action="{{ route('inventory.transactions.store') }}" x-data="transactionForm()">
                    @csrf

                    <!-- Product Selection -->
                    <div class="mb-6">
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Product <span class="text-red-500">*</span>
                        </label>
                        <select id="product_id" name="product_id" required 
                                x-model="productId" @change="updateProductInfo()"
                                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-stock="{{ $product->stock_quantity }}"
                                        data-name="{{ $product->name }}"
                                        data-cost="{{ $product->cost }}">
                                    {{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->stock_quantity }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Product Info Display -->
                        <div x-show="productId" class="mt-3 p-3 bg-blue-50 rounded-lg">
                            <div class="text-sm">
                                <strong>Current Stock:</strong> <span x-text="currentStock"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Type -->
                    <div class="mb-6">
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Transaction Type <span class="text-red-500">*</span>
                        </label>
                        <select id="transaction_type" name="transaction_type" required 
                                x-model="transactionType"
                                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">Select Type</option>
                            @foreach ($transactionTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('transaction_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Info Badge -->
                        <div x-show="transactionType" class="mt-3 p-3 rounded-lg"
                             :class="{
                                'bg-green-50 text-green-800': transactionType === 'adjustment_increase' || transactionType === 'initial_stock',
                                'bg-red-50 text-red-800': transactionType === 'adjustment_decrease' || transactionType === 'damaged' || transactionType === 'expired'
                             }">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm" x-show="transactionType === 'adjustment_increase' || transactionType === 'initial_stock'">
                                    This will <strong>increase</strong> the stock quantity.
                                </p>
                                <p class="text-sm" x-show="transactionType === 'adjustment_decrease' || transactionType === 'damaged' || transactionType === 'expired'">
                                    This will <strong>decrease</strong> the stock quantity. Transaction requires approval.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-6">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" min="1" required 
                               x-model="quantity"
                               value="{{ old('quantity') }}"
                               class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Warning for insufficient stock -->
                        <div x-show="showWarning" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-yellow-800">
                                    Warning: Quantity exceeds current stock. This may result in negative stock.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Unit Cost (Optional) -->
                    <div class="mb-6">
                        <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Unit Cost (Optional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="number" id="unit_cost" name="unit_cost" min="0" step="0.01"
                                   value="{{ old('unit_cost') }}"
                                   class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                        </div>
                        @error('unit_cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reason -->
                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason <span class="text-red-500">*</span>
                        </label>
                        <select id="reason" name="reason" required 
                                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            <option value="">Select Reason</option>
                            @foreach ($reasons as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Date -->
                    <div class="mb-6">
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Transaction Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="transaction_date" name="transaction_date" required 
                               value="{{ old('transaction_date', date('Y-m-d')) }}"
                               class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                        @error('transaction_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea id="notes" name="notes" rows="4" 
                                  class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent"
                                  placeholder="Additional notes or comments...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('inventory.transactions.index') }}" 
                           class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#006BFF] hover:bg-[#0056cc] text-white font-semibold rounded-lg transition-all duration-200">
                            Create Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function transactionForm() {
            return {
                productId: '',
                currentStock: 0,
                transactionType: '',
                quantity: 0,
                
                get showWarning() {
                    if (!this.transactionType || !this.quantity || !this.productId) return false;
                    
                    const isDecrease = ['adjustment_decrease', 'damaged', 'expired'].includes(this.transactionType);
                    return isDecrease && parseInt(this.quantity) > parseInt(this.currentStock);
                },
                
                updateProductInfo() {
                    const select = document.getElementById('product_id');
                    const option = select.options[select.selectedIndex];
                    
                    if (option && option.value) {
                        this.currentStock = option.dataset.stock || 0;
                    }
                }
            }
        }
    </script>
</x-app-layout>

