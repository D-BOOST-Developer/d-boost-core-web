{{-- Quick Stock Transaction Modal Component --}}
<div x-data="quickStockModal()" 
     x-show="isOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape.window="close()">
    
    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
         @click="close()">
    </div>

    {{-- Modal --}}
    <div class="flex min-h-screen items-center justify-center p-4">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl"
             @click.stop>
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="title"></h3>
                <button @click="close()" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form @submit.prevent="submitTransaction()" class="p-6 space-y-5">
                {{-- Product Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Product <span class="text-red-500">*</span>
                    </label>
                    <select x-model="formData.product_id" 
                            required
                            :disabled="preselectedProduct !== null"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all">
                        <option value="">Select a product...</option>
                        @foreach(\App\Models\Product::active()->orderBy('name')->get() as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->stock_quantity }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           x-model="formData.quantity" 
                           required
                           min="1"
                           step="1"
                           placeholder="Enter quantity"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all">
                </div>

                {{-- Transaction Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Transaction Type <span class="text-red-500">*</span>
                    </label>
                    <select x-model="formData.transaction_type" 
                            required
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all">
                        <template x-if="type === 'in'">
                            <optgroup label="Stock In">
                                <option value="purchase">Purchase</option>
                                <option value="return_from_customer">Return from Customer</option>
                                <option value="adjustment_increase">Stock Adjustment (Increase)</option>
                                <option value="initial_stock">Initial Stock</option>
                            </optgroup>
                        </template>
                        <template x-if="type === 'out'">
                            <optgroup label="Stock Out">
                                <option value="sale">Sale</option>
                                <option value="return_to_supplier">Return to Supplier</option>
                                <option value="adjustment_decrease">Stock Adjustment (Decrease)</option>
                                <option value="damaged">Damaged Items</option>
                                <option value="expired">Expired Items</option>
                            </optgroup>
                        </template>
                    </select>
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason
                    </label>
                    <select x-model="formData.reason" 
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all">
                        <option value="">Select reason (optional)</option>
                        <option value="correction">Stock Correction</option>
                        <option value="inventory_count">Physical Inventory Count</option>
                        <option value="damage">Damaged</option>
                        <option value="loss">Loss/Missing</option>
                        <option value="theft">Theft</option>
                        <option value="expired">Expired</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                {{-- Unit Cost (optional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Unit Cost (Optional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>
                        <input type="number" 
                               x-model="formData.unit_cost" 
                               min="0"
                               step="0.01"
                               placeholder="0"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg pl-12 pr-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea x-model="formData.notes" 
                              rows="3"
                              placeholder="Add any additional notes..."
                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all resize-none"></textarea>
                </div>

                {{-- Transaction Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Transaction Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           x-model="formData.transaction_date" 
                           required
                           :max="new Date().toISOString().split('T')[0]"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all">
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-4">
                    <button type="button" 
                            @click="close()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="flex-1 px-4 py-2.5 bg-[#006BFF] hover:bg-[#0056cc] disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <svg x-show="isSubmitting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Saving...' : 'Save Transaction'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Component data function
function quickStockModal() {
    return {
        isOpen: false,
        isSubmitting: false,
        title: 'Stock In',
        preselectedProduct: null,
        
        // Data model
        formData: {
            product_id: '',
            quantity: '',
            transaction_type: 'purchase',
            reason: '',
            unit_cost: '',
            notes: '',
            transaction_date: new Date().toISOString().split('T')[0]
        },

        init() {
            // Listen for window event to open modal
            window.addEventListener('open-quick-stock', (event) => {
                this.open(event.detail.type, event.detail.productId);
            });
            
            // Watch for type changes
            this.$watch('transaction_type', (value) => {
                this.formData.transaction_type = value;
            });
        },

        open(type = 'in', productId = null) {
            this.title = type === 'in' ? 'Stock In' : 'Stock Out';
            this.isOpen = true;
            
            // Reset form first
            this.resetForm();
            
            // Set initial type
            if (type === 'in') {
                this.formData.transaction_type = 'purchase';
            } else {
                this.formData.transaction_type = 'sale';
            }
            
            // Set product if provided
            if (productId) {
                this.preselectedProduct = productId;
                this.formData.product_id = productId;
            }
            
            // Store type for template conditionals
            this.type = type;
        },

        close() {
            this.isOpen = false;
            this.preselectedProduct = null;
        },

        resetForm() {
            this.formData = {
                product_id: '',
                quantity: '',
                transaction_type: 'purchase', // Default
                reason: '',
                unit_cost: '',
                notes: '',
                transaction_date: new Date().toISOString().split('T')[0]
            };
            this.isSubmitting = false;
        },

        async submitTransaction() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const response = await fetch('{{ route("inventory.transactions.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.ok) {
                    Alpine.store('app').showNotification('success', 'Transaction created and stock updated successfully!');
                    this.close();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    const errorMessage = data.message || 'Failed to create transaction';
                    Alpine.store('app').showNotification('error', errorMessage);
                    this.isSubmitting = false;
                }
            } catch (error) {
                console.error('Error:', error);
                Alpine.store('app').showNotification('error', 'An error occurred. Please try again.');
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush
