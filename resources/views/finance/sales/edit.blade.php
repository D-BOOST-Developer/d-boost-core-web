<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Edit Sale') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $sale->invoice_number }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('finance.sales.show', $sale) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>View</span>
                </a>
                <a href="{{ route('finance.sales.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('finance.sales.update', $sale) }}" x-data="editSaleForm()">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Sale Information -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sale Information</h3>
                            </div>

                            <div class="p-6 space-y-5">
                                <!-- Invoice Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Number</label>
                                    <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3">
                                        <span class="text-gray-700 dark:text-gray-300 font-mono text-sm">{{ $sale->invoice_number }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Customer -->
                                    <div>
                                        <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Customer <span class="text-red-500">*</span>
                                        </label>
                                        <select name="customer_id" id="customer_id" required
                                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors @error('customer_id') border-red-500 @enderror">
                                            <option value="">Walk-in Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} - {{ $customer->city ?? 'No City' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Sale Date -->
                                    <div>
                                        <label for="sale_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Sale Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required
                                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors @error('sale_date') border-red-500 @enderror">
                                        @error('sale_date')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="status" id="status" required
                                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors @error('status') border-red-500 @enderror">
                                            <option value="pending" {{ old('status', $sale->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="completed" {{ old('status', $sale->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ old('status', $sale->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="refunded" {{ old('status', $sale->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        </select>
                                        @error('status')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Payment Method -->
                                    <div>
                                        <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Payment Method <span class="text-red-500">*</span>
                                        </label>
                                        <select name="payment_method" id="payment_method" required x-model="paymentMethod"
                                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors @error('payment_method') border-red-500 @enderror">
                                            <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="bank_transfer" {{ old('payment_method', $sale->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="qris" {{ old('payment_method', $sale->payment_method) == 'qris' ? 'selected' : '' }}>QRIS</option>
                                            <option value="debit_card" {{ old('payment_method', $sale->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                            <option value="credit_card" {{ old('payment_method', $sale->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="e_wallet" {{ old('payment_method', $sale->payment_method) == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                        </select>
                                        @error('payment_method')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
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
                                    <textarea name="notes" id="notes" rows="4"
                                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF] focus:border-[#006BFF] transition-colors"
                                              placeholder="Additional notes...">{{ old('notes', $sale->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Items -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sale Items</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Items cannot be modified. Create a new sale to change items.</p>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($sale->salesDetails as $detail)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg">
                                            <div class="flex items-center flex-1">
                                                @if($detail->product && $detail->product->image_path)
                                                    <img src="{{ Storage::url($detail->product->image_path) }}" 
                                                         alt="{{ $detail->product->name ?? 'Product' }}" 
                                                         class="w-12 h-12 object-cover rounded-lg mr-4 border border-gray-300 dark:border-gray-600">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center mr-4">
                                                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <p class="text-gray-900 dark:text-white font-medium">{{ $detail->product->name ?? 'Deleted Product' }}</p>
                                                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                                                        Qty: {{ $detail->quantity }} × Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                                        @if($detail->discount_percentage > 0)
                                                            <span class="text-red-600 dark:text-red-400">({{ $detail->discount_percentage }}% off)</span>
                                                        @endif
                                                    </p>
                                                    @if($detail->product)
                                                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">SKU: {{ $detail->product->sku ?? 'N/A' }}</p>
                                                    @else
                                                        <p class="text-red-500 dark:text-red-400 text-xs mt-1">Product has been deleted</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-[#006BFF] dark:text-blue-400 font-semibold">Rp {{ number_format($detail->line_total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Warning -->
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.082 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <h4 class="text-amber-900 dark:text-amber-300 font-medium text-sm mb-1">Important Notice</h4>
                                    <p class="text-amber-800 dark:text-amber-400 text-xs leading-relaxed">
                                        Only basic sale information can be edited. To modify items or quantities, you need to create a new sale and cancel this one.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm sticky top-6">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Summary</h3>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    @if($sale->discount_amount > 0)
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                                            <span class="text-red-600 dark:text-red-400 font-medium">-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">Tax (10%):</span>
                                        <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-900 dark:text-white font-semibold">Total:</span>
                                            <span class="text-[#006BFF] dark:text-blue-400 font-bold text-lg">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3">
                                    <button type="submit" 
                                            class="w-full bg-[#006BFF] hover:bg-[#0056CC] text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm hover:shadow">
                                        Update Sale
                                    </button>
                                    <a href="{{ route('finance.sales.show', $sale) }}" 
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
        function editSaleForm() {
            return {
                paymentMethod: '{{ old('payment_method', $sale->payment_method) }}'
            }
        }
    </script>
    @endpush
</x-app-layout>
