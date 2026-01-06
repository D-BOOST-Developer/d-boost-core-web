<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-900 dark:text-white leading-tight">
                {{ __('Edit Expense') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('finance.expenses.show', $expense) }}" 
                   class="bg-blue-600 hover:bg-blue-500 text-gray-100 dark:text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>View</span>
                </a>
                <a href="{{ route('finance.expenses.index') }}" 
                   class="bg-gray-600 hover:bg-gray-500 text-gray-100 dark:text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Expenses</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <form method="POST" action="{{ route('finance.expenses.update', $expense) }}" enctype="multipart/form-data" 
                      x-data="{ receiptPreview: '{{ $expense->receipt_path ? Storage::url($expense->receipt_path) : null }}', isRecurring: {{ $expense->is_recurring ? 'true' : 'false' }} }">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Expense Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Description *</label>
                                <input type="text" name="description" id="description" value="{{ old('description', $expense->description) }}" required
                                       class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('description') border-red-500 @enderror"
                                       placeholder="Enter expense description">
                                @error('description')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Amount *</label>
                                <input type="number" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}" required min="0" step="0.01"
                                       class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('amount') border-red-500 @enderror"
                                       placeholder="0.00">
                                @error('amount')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expense Date -->
                            <div>
                                <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Expense Date *</label>
                                <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                                       class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('expense_date') border-red-500 @enderror">
                                @error('expense_date')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Category *</label>
                                <select name="category" id="category" required
                                        class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('category') border-red-500 @enderror">
                                    <option value="">Select Category</option>
                                    <option value="office_supplies" {{ old('category', $expense->category) == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                                    <option value="marketing" {{ old('category', $expense->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="utilities" {{ old('category', $expense->category) == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                    <option value="rent" {{ old('category', $expense->category) == 'rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="salaries" {{ old('category', $expense->category) == 'salaries' ? 'selected' : '' }}>Salaries</option>
                                    <option value="transportation" {{ old('category', $expense->category) == 'transportation' ? 'selected' : '' }}>Transportation</option>
                                    <option value="meals" {{ old('category', $expense->category) == 'meals' ? 'selected' : '' }}>Meals</option>
                                    <option value="equipment" {{ old('category', $expense->category) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                    <option value="maintenance" {{ old('category', $expense->category) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="other" {{ old('category', $expense->category) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Vendor -->
                            <div>
                                <label for="vendor" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Vendor</label>
                                <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $expense->vendor) }}"
                                       class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('vendor') border-red-500 @enderror"
                                       placeholder="Enter vendor name">
                                @error('vendor')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Payment Method *</label>
                                <select name="payment_method" id="payment_method" required
                                        class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('payment_method') border-red-500 @enderror">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit_card" {{ old('payment_method', $expense->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ old('payment_method', $expense->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="e_wallet" {{ old('payment_method', $expense->payment_method) == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                                @error('payment_method')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Receipt Upload -->
                            <div>
                                <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Receipt</label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                    <div x-show="!receiptPreview">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-700 dark:text-gray-400 mb-2">Click to upload receipt</p>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                    <div x-show="receiptPreview" class="relative">
                                        <img :src="receiptPreview" class="max-h-48 mx-auto rounded-lg">
                                        <button type="button" @click="receiptPreview = null; $refs.receiptInput.value = ''" 
                                                class="absolute top-2 right-2 bg-red-500 text-gray-900 dark:text-white rounded-full p-1 hover:bg-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="file" name="receipt" id="receipt" x-ref="receiptInput" accept="image/*" class="hidden"
                                           @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => receiptPreview = e.target.result; reader.readAsDataURL(file); }">
                                </div>
                                <label for="receipt" class="block w-full mt-2 bg-blue-600 hover:bg-blue-500 text-gray-900 dark:text-white text-center py-2 rounded-lg cursor-pointer transition-all duration-200">
                                    {{ $expense->receipt_path ? 'Change Receipt' : 'Choose Receipt' }}
                                </label>
                                @error('receipt')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Recurring Options -->
                            <div>
                                <label class="flex items-center mb-4">
                                    <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" {{ old('is_recurring', $expense->is_recurring) ? 'checked' : '' }}
                                           class="rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-[#006BFF] focus:ring-[#006BFF] focus:ring-offset-gray-800">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">This is a recurring expense</span>
                                </label>

                                <div x-show="isRecurring" x-transition class="mt-4">
                                    <label for="recurring_period" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">Recurring Period</label>
                                    <select name="recurring_period" id="recurring_period"
                                            class="w-full bg-white border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                                        <option value="">Select Period</option>
                                        <option value="daily" {{ old('recurring_period', $expense->recurring_period) == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('recurring_period', $expense->recurring_period) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('recurring_period', $expense->recurring_period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ old('recurring_period', $expense->recurring_period) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Current Statistics -->
                            <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                                <h4 class="text-blue-900 font-medium text-sm mb-3">Current Statistics</h4>
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <p class="text-blue-700">Original Amount</p>
                                        <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700">Category</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ ucwords(str_replace('_', ' ', $expense->category)) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700">Created</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $expense->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700">Status</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $expense->is_recurring ? 'Recurring' : 'One-time' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('finance.expenses.show', $expense) }}" 
                           class="bg-gray-600 hover:bg-gray-500 text-gray-100 dark:text-white font-medium py-2 px-6 rounded-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-gray-100 font-semibold py-2 px-6 rounded-lg transition-all duration-200">
                            Update Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
