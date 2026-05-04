<x-storefront.public-layout :config="$config" pageTitle="Checkout" :cartPanelItems="$items" :cartPanelTotals="$totals" navMode="solid">

    @push('head')
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        body > main { flex: 1 0 auto; }
        body > footer { flex-shrink: 0; }
    </style>
    @endpush

    {{-- Header w/ step indicator --}}
    <section class="relative bg-[var(--ink)] text-[var(--paper)] overflow-hidden mb-12">
        {{-- Decorative rings --}}
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute top-10 right-10 w-[300px] h-[300px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[400px] h-[400px] rounded-full border border-[var(--paper)]"></div>
        </div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 py-20 md:py-24 text-center">
            <p class="text-[10px] uppercase tracking-[0.4em] text-[var(--paper)]/50 mb-5">
                ○ &nbsp; Langkah 2 dari 3 &nbsp; ○
            </p>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.05] mb-5">
                <em class="text-[var(--paper)]/70">Lengkapi</em> Pesanan
            </h1>
            <p class="text-sm text-[var(--paper)]/55 mb-10">Lengkapi data pesanan Anda.</p>

            {{-- Step indicator --}}
            <div class="flex items-center justify-center gap-3 text-xs uppercase tracking-[0.18em]">
                <span class="flex items-center gap-2 opacity-60">
                    <span class="w-7 h-7 rounded-full bg-[var(--paper)] text-[var(--ink)] flex items-center justify-center font-medium">✓</span>
                    <span>Keranjang</span>
                </span>
                <span class="w-10 h-px bg-[var(--paper)]/40"></span>
                <span class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full border border-[var(--paper)] flex items-center justify-center font-medium">2</span>
                    <span>Checkout</span>
                </span>
                <span class="w-10 h-px bg-[var(--paper)]/30"></span>
                <span class="flex items-center gap-2 opacity-50">
                    <span class="w-7 h-7 rounded-full border border-[var(--paper)]/40 flex items-center justify-center">3</span>
                    <span>Selesai</span>
                </span>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('public.store.checkout.place', $config->slug) }}"
          class="grid lg:grid-cols-3 gap-10 mb-16 px-4 sm:px-6 lg:px-8">
        @csrf

        {{-- LEFT: form fields --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 01 Customer --}}
            <section class="bg-white border border-[var(--light)] p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6">
                    <span class="font-serif text-3xl text-[var(--mid)]">01</span>
                    <div>
                        <h2 class="font-serif text-xl text-[var(--ink)]">Data Pemesan</h2>
                        <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)]">Identitas Anda</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">
                            Nama Lengkap <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required maxlength="100"
                               placeholder="Nama lengkap"
                               class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none">
                        @error('customer_name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">
                            Nomor WhatsApp <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="30"
                               placeholder="08xxxxxxxxxx"
                               class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-[var(--mid)] mt-1.5">Penjual akan menghubungi via WhatsApp.</p>
                        @error('customer_phone')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- 02 Shipping --}}
            <section class="bg-white border border-[var(--light)] p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6">
                    <span class="font-serif text-3xl text-[var(--mid)]">02</span>
                    <div>
                        <h2 class="font-serif text-xl text-[var(--ink)]">Alamat Pengiriman</h2>
                        <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)]">Detail lokasi</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">
                            Alamat Lengkap <span class="text-red-600">*</span>
                        </label>
                        <textarea name="customer_address" required maxlength="1000" rows="3"
                                  placeholder="Jalan, nomor rumah, kota, provinsi…"
                                  class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none resize-none">{{ old('customer_address') }}</textarea>
                        @error('customer_address')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">Catatan</label>
                        <textarea name="notes" maxlength="500" rows="2"
                                  placeholder="Instruksi khusus (opsional)"
                                  class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </section>

            {{-- 03 Payment --}}
            <section class="bg-white border border-[var(--light)] p-6 sm:p-8"
                     x-data="{ method: '{{ old('payment_method', 'cash') }}', qrisOpen: false }">
                <div class="flex items-center gap-4 mb-6">
                    <span class="font-serif text-3xl text-[var(--mid)]">03</span>
                    <div>
                        <h2 class="font-serif text-xl text-[var(--ink)]">Metode Pembayaran</h2>
                        <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)]">Pilih cara pembayaran Anda</p>
                    </div>
                </div>

                @php
                    $methods = [
                        'cash'          => ['Cash / COD',    '◐'],
                        'bank_transfer' => ['Transfer Bank', '▤'],
                        'e_wallet'      => ['E-Wallet',      '◇'],
                        'qris'          => ['QRIS',          '▦'],
                        'debit_card'    => ['Kartu Debit',   '▥'],
                        'credit_card'   => ['Kartu Kredit',  '◈'],
                    ];
                    $banks     = \App\Models\UserBusinessProfile::getIndonesianBanks();
                    $bankLabel = $banks[$businessProfile?->bank_name ?? ''] ?? ($businessProfile?->bank_name ?? '');
                    $qrisUrl   = $businessProfile?->qris_image_url ?? null;
                    $rawPhone  = preg_replace('/\D/', '', $businessProfile?->contact_phone ?? '');
                    if (str_starts_with($rawPhone, '0')) { $rawPhone = '62' . substr($rawPhone, 1); }
                    $waBase    = $rawPhone ? 'https://wa.me/' . $rawPhone : null;
                @endphp

                {{-- Method grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-0">
                    @foreach($methods as $value => [$label, $icon])
                        <label class="relative cursor-pointer" @click="method = '{{ $value }}'">
                            <input type="radio" name="payment_method" value="{{ $value }}"
                                   class="peer sr-only" {{ old('payment_method', 'cash') === $value ? 'checked' : '' }}>
                            <div class="border border-[var(--light)] p-4 text-center transition-all peer-checked:border-[var(--ink)] peer-checked:bg-[var(--ink)] peer-checked:text-[var(--paper)] hover:border-[var(--ink)]"
                                 :class="method === '{{ $value }}' ? 'border-[var(--ink)] bg-[var(--ink)] text-[var(--paper)]' : ''">
                                <div class="text-2xl mb-2">{{ $icon }}</div>
                                <div class="text-xs uppercase tracking-[0.18em] font-medium">{{ $label }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('payment_method')<p class="text-red-600 text-xs mt-3">{{ $message }}</p>@enderror

                {{-- ── Bank Transfer detail ──────────────────── --}}
                <div x-show="method === 'bank_transfer'" x-transition class="mt-6 border border-[var(--light)] bg-[var(--paper)] p-5">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Detail Transfer Bank</p>
                    @if($businessProfile && $businessProfile->bank_name && $businessProfile->bank_account_number)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase tracking-[0.2em] text-[var(--mid)]">Bank</span>
                                <span class="font-medium text-[var(--ink)] text-sm">{{ $bankLabel }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase tracking-[0.2em] text-[var(--mid)]">No. Rekening</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[var(--ink)] text-sm tracking-wider">{{ $businessProfile->bank_account_number }}</span>
                                    <button type="button"
                                            onclick="navigator.clipboard.writeText('{{ $businessProfile->bank_account_number }}').then(()=>{ const e=this; e.textContent='✓'; setTimeout(()=>e.textContent='⧉',1500) })"
                                            class="text-[10px] text-[var(--mid)] hover:text-[var(--ink)] transition-colors px-2 py-0.5 border border-[var(--light)]">⧉</button>
                                </div>
                            </div>
                            @if($businessProfile->bank_account_holder)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs uppercase tracking-[0.2em] text-[var(--mid)]">Atas Nama</span>
                                    <span class="text-[var(--ink)] text-sm">{{ $businessProfile->bank_account_holder }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-[var(--mid)] italic">Info rekening belum tersedia. Konfirmasi via WhatsApp setelah checkout.</p>
                    @endif
                </div>

                {{-- ── QRIS ──────────────────────────────────── --}}
                <div x-show="method === 'qris'" x-transition class="mt-6 border border-[var(--light)] bg-[var(--paper)] p-5">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Scan QRIS untuk Pembayaran</p>
                    @if($qrisUrl)
                        <div class="flex flex-col items-center gap-4">
                            <button type="button" @click="qrisOpen = true"
                                    class="group relative border-4 border-[var(--ink)] p-2 inline-block hover:border-[var(--mid)] transition-colors focus:outline-none">
                                <img src="{{ $qrisUrl }}" alt="QRIS {{ $config->storefront_title }}"
                                     class="w-52 h-52 object-contain block">
                                {{-- Hover overlay --}}
                                <span class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/70 text-white text-[10px] uppercase tracking-[0.2em] px-3 py-1.5">
                                        🔍 Perbesar
                                    </span>
                                </span>
                            </button>
                            <p class="text-xs text-[var(--mid)] text-center leading-relaxed max-w-xs">
                                Scan menggunakan dompet digital atau mobile banking mana pun.
                                <span class="block mt-1 text-[10px] uppercase tracking-[0.18em] opacity-60">Ketuk gambar untuk memperbesar</span>
                            </p>
                        </div>

                        {{-- QRIS Lightbox Modal --}}
                        <div x-show="qrisOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click.self="qrisOpen = false"
                             @keydown.escape.window="qrisOpen = false"
                             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
                             style="display:none">
                            <div class="relative flex flex-col items-center gap-4">
                                {{-- Close button --}}
                                <button type="button" @click="qrisOpen = false"
                                        class="absolute -top-10 right-0 text-white/70 hover:text-white text-2xl leading-none transition-colors">
                                    ✕
                                </button>

                                {{-- Big QRIS --}}
                                <div class="border-[6px] border-white p-3 bg-white shadow-2xl">
                                    <img src="{{ $qrisUrl }}"
                                         alt="QRIS {{ $config->storefront_title }}"
                                         class="block object-contain"
                                         style="width: min(80vmin, 480px); height: min(80vmin, 480px);">
                                </div>

                                <div class="text-center">
                                    <p class="text-white font-serif text-lg mb-1">{{ $config->storefront_title }}</p>
                                    <p class="text-white/60 text-xs uppercase tracking-[0.24em]">Scan untuk membayar · QRIS</p>
                                </div>
                                <p class="text-white/40 text-[10px] uppercase tracking-[0.2em]">Ketuk di luar gambar untuk menutup</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-[var(--mid)] italic">Kode QRIS belum tersedia. Konfirmasi via WhatsApp setelah checkout.</p>
                    @endif
                </div>

                {{-- ── Cash / COD / E-Wallet / Kartu ───────── --}}
                <div x-show="['cash','e_wallet','debit_card','credit_card'].includes(method)" x-transition
                     class="mt-6 border border-[var(--light)] bg-[var(--paper)] p-5">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-2">Informasi Pembayaran</p>
                    <p class="text-sm text-[var(--ink)] leading-relaxed">
                        <span x-show="method === 'cash'">Pembayaran dilakukan saat barang diterima (Cash on Delivery).</span>
                        <span x-show="method === 'e_wallet'">Nomor e-wallet akan dikonfirmasi oleh penjual via WhatsApp.</span>
                        <span x-show="['debit_card','credit_card'].includes(method)">Detail kartu akan dikonfirmasi oleh penjual via WhatsApp.</span>
                    </p>
                </div>

                {{-- ── WhatsApp hint (always) ─────────────── --}}
                @if($waBase)
                    <p class="text-[10px] uppercase tracking-[0.18em] text-[var(--mid)] mt-4 leading-relaxed">
                        Setelah memesan, Anda dapat
                        <a href="{{ $waBase }}" target="_blank" rel="noopener"
                           class="underline hover:text-[var(--ink)] transition-colors">konfirmasi pembayaran via WhatsApp</a>
                        langsung ke penjual.
                    </p>
                @endif
            </section>
        </div>

        {{-- RIGHT: summary --}}
        <aside>
            <div class="bg-white border border-[var(--light)] p-6 sticky top-24">
                <h2 class="font-serif text-2xl text-[var(--ink)] mb-5 pb-4 border-b border-[var(--light)]">
                    Pesanan Anda
                </h2>

                <div class="space-y-3 max-h-52 overflow-y-auto pr-1 mb-4">
                    @foreach($items as $item)
                        <div class="flex justify-between items-start text-sm gap-3">
                            <div class="min-w-0">
                                <p class="text-[var(--ink)] truncate">{{ $item['product']->name }}</p>
                                <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] mt-0.5">× {{ $item['qty'] }} pcs</p>
                            </div>
                            <p class="text-[var(--ink)] whitespace-nowrap">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-3 text-sm pt-4 border-t border-[var(--light)]">
                    <div class="flex justify-between">
                        <span class="text-[var(--mid)]">Subtotal</span>
                        <span class="text-[var(--ink)]">Rp {{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[var(--mid)]">Pajak (10%)</span>
                        <span class="text-[var(--ink)]">Rp {{ number_format($totals['tax'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-[var(--light)]">
                        <span class="font-serif text-xl text-[var(--ink)]">Total</span>
                        <span class="font-serif text-xl text-[var(--ink)]">Rp {{ number_format($totals['total'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit"
                        class="mt-6 block w-full text-center bg-[var(--ink)] text-[var(--paper)] text-xs uppercase tracking-[0.2em] font-medium py-4 hover:opacity-90 transition-opacity">
                    Pesan Sekarang →
                </button>

                <a href="{{ route('public.store.cart', $config->slug) }}"
                   class="block text-center mt-3 text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] hover:text-[var(--ink)] transition-colors">
                    ← Kembali ke Keranjang
                </a>
            </div>
        </aside>
    </form>

</x-storefront.public-layout>
