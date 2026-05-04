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
                @if($sale->status === 'pending' && $sale->order_source === 'storefront')
                    <button type="button" x-data
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Order Confirmation',
                            message: 'Are you sure you want to confirm this order? Product stock will be automatically deducted.',
                            confirmText: 'Confirm Order',
                            action: '{{ route('finance.sales.confirm', $sale) }}'
                        })"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Confirm Order</span>
                    </button>
                    <button type="button" x-data
                        @click="$dispatch('open-reject-modal', {
                            action: '{{ route('finance.sales.cancel', $sale) }}'
                        })"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Cancel Order</span>
                    </button>
                @endif
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
                                <div class="flex items-center gap-2">
                                    @include('finance.sales._source_badge', ['sale' => $sale])
                                    @include('finance.sales._status_badge', ['sale' => $sale])
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Customer & Payment Info -->
                            <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-6">

                                @if($sale->order_source === 'storefront' && $sale->customer_snapshot)
                                    @php $cs = $sale->customer_snapshot; @endphp
                                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-200 mb-3 uppercase tracking-wide flex items-center justify-between">
                                            <span>Customer (Storefront)</span>
                                            @if(!empty($cs['phone']))
                                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $cs['phone']) }}" target="_blank"
                                                    class="text-xs font-medium text-green-700 dark:text-green-400 hover:underline">Chat WhatsApp →</a>
                                            @endif
                                        </h3>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between gap-4">
                                                <span class="text-gray-600 dark:text-gray-400">Nama:</span>
                                                <span class="text-gray-900 dark:text-white font-medium text-right">{{ $cs['name'] ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between gap-4">
                                                <span class="text-gray-600 dark:text-gray-400">WhatsApp:</span>
                                                <span class="text-gray-900 dark:text-white font-medium text-right">{{ $cs['phone'] ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between gap-4">
                                                <span class="text-gray-600 dark:text-gray-400">Alamat:</span>
                                                <span class="text-gray-900 dark:text-white font-medium text-right whitespace-pre-line">{{ $cs['address'] ?? '-' }}</span>
                                            </div>
                                            @if(!empty($cs['notes']))
                                                <div class="flex justify-between gap-4">
                                                    <span class="text-gray-600 dark:text-gray-400">Catatan:</span>
                                                    <span class="text-gray-900 dark:text-white font-medium text-right whitespace-pre-line">{{ $cs['notes'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Payment Method Info -->
                                <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Payment Information</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Method:</span>
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $sale->payment_method ? ucwords(str_replace('_', ' ', $sale->payment_method)) : '-' }}</span>
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
                                <div class="mb-6" x-data="{ qrisOpen: false }">
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
                                        @php $qrisUrl = asset('storage/' . $businessProfile->qris_barcode_path); @endphp

                                        {{-- QRIS card: centered, clickable --}}
                                        <div class="bg-gradient-to-b from-red-50 to-white dark:from-red-900/20 dark:to-gray-800 border border-red-200 dark:border-red-800 rounded-xl p-6">
                                            <div class="flex flex-col items-center gap-4">
                                                {{-- Label --}}
                                                <div class="text-center">
                                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/40 px-3 py-1 rounded-full">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                        QRIS Payment Code
                                                    </span>
                                                </div>

                                                {{-- Clickable QRIS image --}}
                                                <button type="button" @click="qrisOpen = true"
                                                        class="group relative focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 rounded-xl">
                                                    {{-- White frame like a physical QRIS printout --}}
                                                    <div class="bg-white dark:bg-white rounded-xl shadow-lg border-4 border-white ring-1 ring-gray-200 p-4 transition-transform group-hover:scale-[1.02] duration-200">
                                                        <img src="{{ $qrisUrl }}"
                                                             alt="QRIS Code"
                                                             class="w-52 h-52 object-contain block">
                                                    </div>
                                                    {{-- Hover badge --}}
                                                    <span class="absolute inset-0 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                        <span class="flex items-center gap-1.5 bg-black/70 text-white text-xs font-medium px-3 py-1.5 rounded-full backdrop-blur-sm">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                            Perbesar untuk scan
                                                        </span>
                                                    </span>
                                                </button>

                                                {{-- Caption --}}
                                                <div class="text-center">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Pelanggan dapat scan kode ini dengan aplikasi e-wallet mana pun.
                                                    </p>
                                                    <p class="text-xs text-red-500 dark:text-red-400 mt-1 flex items-center justify-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        Klik gambar untuk memperbesar
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- QRIS Lightbox Modal --}}
                                        <div x-show="qrisOpen"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             @click.self="qrisOpen = false"
                                             @keydown.escape.window="qrisOpen = false"
                                             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
                                             style="display:none">
                                            <div class="relative flex flex-col items-center gap-5 max-w-lg w-full">
                                                {{-- Close button --}}
                                                <button type="button" @click="qrisOpen = false"
                                                        class="absolute -top-12 right-0 flex items-center gap-1.5 text-white/60 hover:text-white text-sm transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Tutup (Esc)
                                                </button>

                                                {{-- Full-size QRIS frame --}}
                                                <div class="bg-white rounded-2xl shadow-2xl ring-4 ring-red-400/40 p-6 w-full flex flex-col items-center gap-4">
                                                    {{-- Store name header --}}
                                                    <div class="text-center pb-3 border-b border-gray-100 w-full">
                                                        <p class="font-semibold text-gray-900 text-base">{{ $businessProfile->business_name ?? auth()->user()?->name }}</p>
                                                        <p class="text-xs text-red-600 font-medium uppercase tracking-widest mt-0.5">Pembayaran QRIS</p>
                                                    </div>

                                                    {{-- Large QR --}}
                                                    <img src="{{ $qrisUrl }}"
                                                         alt="QRIS Code"
                                                         class="block object-contain"
                                                         style="width: min(75vmin, 420px); height: min(75vmin, 420px);">

                                                    {{-- Footer note --}}
                                                    <p class="text-xs text-gray-400 text-center">Scan dengan GoPay · OVO · Dana · ShopeePay · M-Banking</p>
                                                </div>

                                                <p class="text-white/30 text-xs">Klik di luar kotak atau tekan Esc untuk menutup</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Notes -->
                            @if($sale->notes)
                                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 mb-4">
                                    <h4 class="text-amber-800 dark:text-amber-900 font-medium text-sm mb-2">Notes</h4>
                                    <p class="text-yellow-500 dark:text-yellow-800 text-sm leading-relaxed">{{ $sale->notes }}</p>
                                </div>
                            @endif

                            <!-- Rejection Reason -->
                            @if($sale->status === 'cancelled' && $sale->rejection_reason)
                                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                                    <h4 class="text-red-800 dark:text-red-900 font-medium text-sm mb-2">Rejection Reason</h4>
                                    <p class="text-red-600 dark:text-red-400 text-sm leading-relaxed">{{ $sale->rejection_reason }}</p>
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
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm top-6">
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
