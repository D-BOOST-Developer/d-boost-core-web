<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Create Sales Return') }}
            </h2>
            <a href="{{ route('finance.sales-returns.index') }}" 
               class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            <form method="POST" action="{{ route('finance.sales-returns.store') }}" x-data="salesReturnForm()">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Return Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Return Information -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Return Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sale Selection -->
                                <div class="md:col-span-2">
                                    <label for="sale_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Original Sale *</label>
                                    <select name="sale_id" id="sale_id" required x-model="saleId"
                                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('sale_id') border-red-500 @enderror">
                                        <option value="">Select a sale to return</option>
                                        @php
                                            $sales = \App\Models\Sale::with('customer')->where('status', 'completed')->latest()->limit(50)->get();
                                        @endphp
                                        @foreach($sales as $s)
                                            <option value="{{ $s->id }}" {{ old('sale_id', $sale?->id) == $s->id ? 'selected' : '' }}>
                                                {{ $s->invoice_number }} - {{ $s->customer->name }} - Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sale_id')
                                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Return Date -->
                                <div>
                                    <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Date *</label>
                                    <input type="date" name="return_date" id="return_date" value="{{ old('return_date', date('Y-m-d')) }}" required
                                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('return_date') border-red-500 @enderror">
                                    @error('return_date')
                                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reason -->
                                <div>
                                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason *</label>
                                    <select name="reason" id="reason" required
                                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('reason') border-red-500 @enderror">
                                        <option value="">Select reason</option>
                                        <option value="defective" {{ old('reason') == 'defective' ? 'selected' : '' }}>Defective Product</option>
                                        <option value="wrong_item" {{ old('reason') == 'wrong_item' ? 'selected' : '' }}>Wrong Item</option>
                                        <option value="customer_request" {{ old('reason') == 'customer_request' ? 'selected' : '' }}>Customer Request</option>
                                        <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('reason')
                                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent"
                                              placeholder="Additional notes...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Return Items -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Return Items</h3>
                                <button type="button" @click="addItem()" 
                                        class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>Add Item</span>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                            <!-- Product -->
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product</label>
                                                <select x-model="item.product_id" @change="updateProductPrice(index)" required
                                                        :name="`items[${index}][product_id]`"
                                                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                                                    <option value="">Select Product</option>
                                                    @php
                                                        $products = \App\Models\Product::active()->get();
                                                    @endphp
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                            {{ $product->name }} ({{ $product->sku }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Quantity -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Qty</label>
                                                <input type="number" x-model="item.quantity" @input="calculateTotal()" 
                                                       :name="`items[${index}][quantity]`" min="1" required
                                                       class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                                            </div>

                                            <!-- Price -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price</label>
                                                <input type="number" x-model="item.unit_price" @input="calculateTotal()" 
                                                       :name="`items[${index}][unit_price]`" step="0.01" min="0" required
                                                       class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                                            </div>

                                            <!-- Remove -->
                                            <div>
                                                <button type="button" @click="removeItem(index)" 
                                                        class="w-full bg-red-600 hover:bg-red-500 text-white py-2 px-3 rounded-lg transition-all duration-200">
                                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="items.length === 0" class="text-center py-8 text-gray-600 dark:text-gray-400">
                                    <p>No items added yet. Click "Add Item" to get started.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Summary -->
                    <div>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 sticky top-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Return Summary</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Items:</span>
                                    <span class="text-gray-900 dark:text-white" x-text="items.length">0</span>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-900 dark:text-white">Total Amount:</span>
                                        <span class="text-[#006BFF]">Rp <span x-text="formatNumber(totalAmount)">0</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <button type="submit" :disabled="items.length === 0"
                                        class="w-full bg-[#006BFF] hover:bg-[#006BFF]/80 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200">
                                    Create Return
                                </button>
                                <a href="{{ route('finance.sales-returns.index') }}" 
                                   class="w-full bg-gray-600 hover:bg-gray-500 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 text-center block">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function salesReturnForm() {
            return {
                saleId: '{{ old("sale_id", $sale?->id ?? "") }}',
                items: [],
                
                get totalAmount() {
                    return this.items.reduce((sum, item) => {
                        const qty = parseFloat(item.quantity) || 0;
                        const price = parseFloat(item.unit_price) || 0;
                        return sum + (qty * price);
                    }, 0);
                },
                
                addItem() {
                    this.items.push({
                        product_id: '',
                        quantity: 1,
                        unit_price: 0
                    });
                },
                
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                
                updateProductPrice(index) {
                    const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption && selectedOption.dataset.price) {
                        this.items[index].unit_price = parseFloat(selectedOption.dataset.price);
                    }
                },
                
                calculateTotal() {
                    // Trigger reactivity
                },
                
                formatNumber(number) {
                    return new Intl.NumberFormat('id-ID').format(number || 0);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
