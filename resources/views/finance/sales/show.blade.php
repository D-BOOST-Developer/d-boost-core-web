<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Sale Details') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $sale->invoice_number }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('finance.sales.edit', $sale) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Invoice Header -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $sale->invoice_number }}</h1>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $sale->sale_date->format('F d, Y') }}</p>
                                </div>
                                <div>
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            'refunded' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                        ];
                                        $statusColor = $statusColors[$sale->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Customer & Payment Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Customer Info -->
                                <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Customer Information</h3>
                                    @if($sale->customer)
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-blue-700 dark:text-blue-300 text-sm font-semibold">{{ substr($sale->customer->name, 0, 1) }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-gray-900 dark:text-white font-medium">{{ $sale->customer->name }}</p>
                                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                                    {{ $sale->customer->email ?: $sale->customer->phone }}
                                                </p>
                                                @if($sale->customer->city)
                                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $sale->customer->city }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-gray-900 dark:text-white font-medium">Walk-in Customer</p>
                                                <p class="text-gray-600 dark:text-gray-400 text-sm">No customer information recorded</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Payment Method Info -->
                                <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Payment Information</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Method:</span>
                                            <span class="text-gray-900 dark:text-white font-medium">{{ ucwords(str_replace('_', ' ', $sale->payment_method)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Date:</span>
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $sale->sale_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Time:</span>
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $sale->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Details (Bank/QRIS) -->
                            @if(in_array($sale->payment_method, ['bank_transfer', 'qris']) && $businessProfile)
                                <div class="mb-6">
                                    @if($sale->payment_method === 'bank_transfer' && $businessProfile->bank_name)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Bank Account Information</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Bank Name</p>
                                                    <p class="text-gray-900 dark:text-white font-medium">
                                                        {{ \App\Models\UserBusinessProfile::getIndonesianBanks()[$businessProfile->bank_name] ?? $businessProfile->bank_name }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Account Number</p>
                                                    <p class="text-gray-900 dark:text-white font-mono font-semibold">{{ $businessProfile->bank_account_number }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Account Holder</p>
                                                    <p class="text-gray-900 dark:text-white font-medium">{{ $businessProfile->bank_account_holder }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($sale->payment_method === 'qris' && $businessProfile->qris_barcode_path)
                                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">QRIS Payment Code</h4>
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 inline-block">
                                                <img src="{{ asset('storage/' . $businessProfile->qris_barcode_path) }}" 
                                                     alt="QRIS Code" 
                                                     class="w-48 h-48 mx-auto rounded border border-gray-200 dark:border-gray-700">
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-3">Customer can scan this QR code with any e-wallet app</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Notes -->
                            @if($sale->notes)
                                <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 dark:border-amber-600 rounded-lg p-4">
                                    <h4 class="text-amber-900 dark:text-amber-300 font-medium text-sm mb-2">Notes</h4>
                                    <p class="text-amber-800 dark:text-amber-400 text-sm leading-relaxed">{{ $sale->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sale Items</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $sale->salesDetails->count() }} {{ $sale->salesDetails->count() > 1 ? 'items' : 'item' }}</p>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($sale->salesDetails as $detail)
                                    <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1">
                                                @if($detail->product && $detail->product->image_path)
                                                    <img src="{{ Storage::url($detail->product->image_path) }}" 
                                                         alt="{{ $detail->product->name ?? 'Product' }}" 
                                                         class="w-14 h-14 object-cover rounded-lg mr-4 border border-gray-300 dark:border-gray-600">
                                                @else
                                                    <div class="w-14 h-14 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center mr-4">
                                                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <p class="text-gray-900 dark:text-white font-medium">{{ $detail->product->name ?? 'Deleted Product' }}</p>
                                                    @if($detail->product)
                                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">SKU: {{ $detail->product->sku ?? 'N/A' }}</p>
                                                    @else
                                                        <p class="text-red-500 dark:text-red-400 text-sm mb-1">Product has been deleted</p>
                                                    @endif
                                                    <div class="flex items-center space-x-4 text-sm">
                                                        <span class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">Qty:</span> {{ $detail->quantity }}
                                                        </span>
                                                        <span class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">Unit Price:</span> Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                                        </span>
                                                        @if($detail->discount_percentage > 0)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                                                -{{ $detail->discount_percentage }}% OFF
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-[#006BFF] dark:text-blue-400 font-semibold text-lg">Rp {{ number_format($detail->line_total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Summary -->
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
                                        <span class="text-[#006BFF] dark:text-blue-400 font-bold text-xl">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Statistics</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-2xl font-bold text-[#006BFF] dark:text-blue-400">{{ $sale->salesDetails->count() }}</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium mt-1">Product Types</p>
                                </div>
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-2xl font-bold text-[#006BFF] dark:text-blue-400">{{ $sale->total_items }}</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium mt-1">Total Items</p>
                                </div>
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-2xl font-bold text-[#006BFF] dark:text-blue-400">{{ number_format($sale->profit_margin, 1) }}%</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-xs font-medium mt-1">Profit Margin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Audit Trail</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4 text-sm">
                                <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Created by</p>
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $sale->creator->name ?? 'System' }}</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $sale->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                @if($sale->updated_at != $sale->created_at)
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Last updated by</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $sale->updater->name ?? 'System' }}</p>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $sale->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('finance.sales.edit', $sale) }}" 
                                   class="w-full bg-[#006BFF] hover:bg-[#0056CC] text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-center block shadow-sm hover:shadow">
                                    Edit Sale
                                </a>
                                <a href="{{ route('finance.sales.print', $sale) }}" 
                                   target="_blank"
                                   class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-2.5 px-4 rounded-lg transition-colors text-center block">
                                    Print Invoice
                                </a>
                                @if($sale->status !== 'cancelled')
                                    <button type="button" 
                                            onclick="openDeleteModal('{{ route('finance.sales.destroy', $sale) }}', 'Delete Sale', 'Are you sure you want to delete sale {{ $sale->invoice_number }}? This will permanently remove all transaction data.')"
                                            class="w-full bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 font-medium py-2.5 px-4 rounded-lg transition-colors">
                                        Delete Sale
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
