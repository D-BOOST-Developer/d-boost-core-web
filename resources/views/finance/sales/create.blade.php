<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('New Sale') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create a new sales transaction</p>
            </div>
            <a href="{{ route('finance.sales.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('finance.sales.store') }}" x-data="saleForm()">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Transaction Information -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Information</h3>
                            </div>
                            
                            <div class="p-6 space-y-5">
                                <!-- Invoice Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Number</label>
                                    <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3">
                                        <span class="text-gray-700 dark:text-gray-300 font-mono text-sm">{{ $invoiceNumber ?? 'Auto Generated' }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Sale Date -->
                                    <div>
                                        <label for="sale_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Sale Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required
                                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors @error('sale_date') border-red-500 @enderror">
                                        @error('sale_date')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Customer -->
                                    <div>
                                        <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Customer <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                                        </label>
                                        <select name="customer_id" id="customer_id" x-model="selectedCustomer"
                                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors @error('customer_id') border-red-500 @enderror">
                                            <option value="">Walk-in Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} - {{ $customer->city ?? 'No City' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Payment Method <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" required class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 text-center transition-all hover:border-[#006BFF] peer-checked:border-[#006BFF] peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Cash</div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" required class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 text-center transition-all hover:border-[#006BFF] peer-checked:border-[#006BFF] peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Bank Transfer</div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" required class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 text-center transition-all hover:border-[#006BFF] peer-checked:border-[#006BFF] peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">QRIS</div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="payment_method" value="debit_card" x-model="paymentMethod" required class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 text-center transition-all hover:border-[#006BFF] peer-checked:border-[#006BFF] peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Debit Card</div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="payment_method" value="e_wallet" x-model="paymentMethod" required class="peer sr-only">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg p-3 text-center transition-all hover:border-[#006BFF] peer-checked:border-[#006BFF] peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">E-Wallet</div>
                                            </div>
                                        </label>
                                    </div>
                                    @error('payment_method')
                                        <p class="text-red-600 dark:text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Information Display -->
                                <div x-show="paymentMethod === 'bank_transfer' || paymentMethod === 'qris'" x-transition class="mt-4">
                                    @if($businessProfile)
                                        <!-- Bank Transfer Info -->
                                        <div x-show="paymentMethod === 'bank_transfer'" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Bank Account Information</h4>
                                            @if($businessProfile->bank_name)
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Bank:</span>
                                                        <span class="text-gray-900 dark:text-white font-medium">
                                                            {{ \App\Models\UserBusinessProfile::getIndonesianBanks()[$businessProfile->bank_name] ?? $businessProfile->bank_name }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Account Number:</span>
                                                        <span class="text-gray-900 dark:text-white font-mono font-semibold">{{ $businessProfile->bank_account_number }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Account Holder:</span>
                                                        <span class="text-gray-900 dark:text-white font-medium">{{ $businessProfile->bank_account_holder }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-sm text-amber-600 dark:text-amber-400">Bank account information not configured.</p>
                                            @endif
                                        </div>

                                        <!-- QRIS Info -->
                                        <div x-show="paymentMethod === 'qris'" class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">QRIS Payment Code</h4>
                                            @if($businessProfile->qris_barcode_path)
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 inline-block">
                                                    <img src="{{ asset('storage/' . $businessProfile->qris_barcode_path) }}" 
                                                         alt="QRIS Code" 
                                                         class="w-48 h-48 mx-auto rounded border border-gray-200 dark:border-gray-700">
                                                </div>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-3">Customer can scan this QR code with any e-wallet app</p>
                                            @else
                                                <p class="text-sm text-amber-600 dark:text-amber-400">QRIS code not uploaded yet.</p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                            <p class="text-sm text-red-600 dark:text-red-400">Business profile not set up. Please complete your business profile first.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" x-model="notes"
                                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors"
                                              placeholder="Additional notes...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Products</h3>
                                <button type="button" @click="addProduct()" 
                                        class="inline-flex items-center px-3 py-1.5 bg-[#006BFF] hover:bg-[#0056CC] text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>Add Product</span>
                                </button>
                            </div>

                            <div class="p-6">
                                <div class="space-y-4">
                                    <template x-for="(item, index) in products" :key="index">
                                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                                <div class="md:col-span-4">
                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Product</label>
                                                    <select x-model="item.product_id" @change="updateProductPrice(index)" required
                                                            :name="`products[${index}][product_id]`"
                                                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF]">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" 
                                                                    data-price="{{ $product->price }}" 
                                                                    data-stock="{{ $product->stock_quantity }}">
                                                                {{ $product->name }} (Stock: {{ $product->stock_quantity }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantity</label>
                                                    <input type="number" x-model="item.quantity" @input="calculateLineTotal(index)" 
                                                           :name="`products[${index}][quantity]`" min="1" required
                                                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF]">
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit Price</label>
                                                    <input type="number" x-model="item.unit_price" @input="calculateLineTotal(index)" 
                                                           :name="`products[${index}][unit_price]`" step="0.01" min="0" required
                                                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF]">
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Discount (%)</label>
                                                    <input type="number" x-model="item.discount_percentage" @input="calculateLineTotal(index)" 
                                                           :name="`products[${index}][discount_percentage]`" step="0.1" min="0" max="100"
                                                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF]"
                                                           placeholder="0">
                                                </div>

                                                <div class="md:col-span-1">
                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Total</label>
                                                    <div class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-center font-semibold text-[#006BFF]">
                                                        <span x-text="formatNumberCompact(item.line_total)"></span>
                                                    </div>
                                                </div>

                                                <div class="md:col-span-1">
                                                    <button type="button" @click="removeProduct(index)" 
                                                            class="w-full bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 py-2 px-3 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div x-show="products.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <p class="text-sm font-medium mb-1">No products added</p>
                                        <p class="text-xs">Click "Add Product" to get started</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Summary -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm sticky top-6">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Summary</h3>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">Items:</span>
                                        <span class="text-gray-900 dark:text-white font-medium" x-text="products.length"></span>
                                    </div>
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">Rp <span x-text="formatNumber(subtotal)"></span></span>
                                    </div>
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">Tax (10%):</span>
                                        <span class="text-gray-900 dark:text-white font-medium">Rp <span x-text="formatNumber(taxAmount)"></span></span>
                                    </div>
                                    <div class="pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-900 dark:text-white font-semibold">Total:</span>
                                            <span class="text-[#006BFF] dark:text-blue-400 font-bold text-lg">Rp <span x-text="formatNumber(grandTotal)"></span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3">
                                    <button type="submit" :disabled="products.length === 0 || !paymentMethod"
                                            class="w-full bg-[#006BFF] hover:bg-[#0056CC] disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm hover:shadow">
                                        Complete Sale
                                    </button>
                                    <a href="{{ route('finance.sales.index') }}" 
                                       class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function saleForm() {
            return {
                products: [],
                selectedCustomer: '',
                paymentMethod: '{{ old('payment_method', '') }}',
                notes: '',
                
                get subtotal() {
                    return this.products.reduce((sum, item) => sum + (item.line_total || 0), 0);
                },
                
                get taxAmount() {
                    return this.subtotal * 0.1;
                },
                
                get grandTotal() {
                    return this.subtotal + this.taxAmount;
                },
                
                addProduct() {
                    this.products.push({
                        product_id: '',
                        quantity: 1,
                        unit_price: 0,
                        discount_percentage: 0,
                        line_total: 0
                    });
                },
                
                removeProduct(index) {
                    this.products.splice(index, 1);
                },
                
                updateProductPrice(index) {
                    const select = document.querySelector(`select[name="products[${index}][product_id]"]`);
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption && selectedOption.dataset.price) {
                        this.products[index].unit_price = parseFloat(selectedOption.dataset.price);
                        this.calculateLineTotal(index);
                    }
                },
                
                calculateLineTotal(index) {
                    const item = this.products[index];
                    const quantity = parseFloat(item.quantity) || 0;
                    const unitPrice = parseFloat(item.unit_price) || 0;
                    const discountPercentage = parseFloat(item.discount_percentage) || 0;
                    
                    const subtotal = quantity * unitPrice;
                    const discountAmount = subtotal * (discountPercentage / 100);
                    item.line_total = subtotal - discountAmount;
                },
                
                formatNumber(number) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
                },
                
                formatNumberCompact(number) {
                    const rounded = Math.round(number || 0);
                    if (rounded >= 1000000) {
                        return (rounded / 1000000).toFixed(1) + 'M';
                    } else if (rounded >= 1000) {
                        return (rounded / 1000).toFixed(1) + 'K';
                    }
                    return new Intl.NumberFormat('id-ID').format(rounded);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
