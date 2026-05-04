<x-storefront.public-layout :config="$config" pageTitle="Pesanan Berhasil" navMode="solid">

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
                ○ &nbsp; Pesanan Berhasil &nbsp; ○
            </p>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.05] mb-5">
                Terima <em class="text-[var(--paper)]/70">Kasih</em>
            </h1>
            <p class="text-sm text-[var(--paper)]/55 mb-10">Pesanan Anda telah kami terima.</p>

            {{-- Step indicator --}}
            <div class="flex items-center justify-center gap-3 text-xs uppercase tracking-[0.18em]">
                <span class="flex items-center gap-2 opacity-60">
                    <span class="w-7 h-7 rounded-full bg-[var(--paper)] text-[var(--ink)] flex items-center justify-center font-medium">✓</span>
                    <span>Keranjang</span>
                </span>
                <span class="w-10 h-px bg-[var(--paper)]/40"></span>
                <span class="flex items-center gap-2 opacity-60">
                    <span class="w-7 h-7 rounded-full bg-[var(--paper)] text-[var(--ink)] flex items-center justify-center font-medium">✓</span>
                    <span>Checkout</span>
                </span>
                <span class="w-10 h-px bg-[var(--paper)]/40"></span>
                <span class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full border border-[var(--paper)] flex items-center justify-center font-medium">3</span>
                    <span>Selesai</span>
                </span>
            </div>
        </div>
    </section>

    <div class="max-w-2xl mx-auto mb-16">
        {{-- Success card --}}
        <div class="bg-white border border-[var(--light)] p-10 sm:p-12 text-center"
             x-data="{ qrisOpen: false }">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full border border-[var(--ink)] mb-6">
                <span class="font-serif text-4xl text-[var(--ink)]">✓</span>
            </div>

            <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Pesanan Berhasil</p>
            <h2 class="font-serif text-3xl md:text-4xl text-[var(--ink)] mb-4">
                Terima kasih atas <em>pesanan</em> Anda.
            </h2>
            <p class="text-[var(--mid)] leading-relaxed mb-8 max-w-md mx-auto">
                Penjual akan menghubungi Anda via WhatsApp untuk konfirmasi pesanan dan detail pembayaran.
            </p>

            {{-- Invoice block --}}
            <div class="border-t border-b border-[var(--light)] py-6 mb-6 grid grid-cols-3 gap-4 text-left">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-1">No. Invoice</p>
                    <p class="font-mono text-sm text-[var(--ink)] break-all">{{ $sale->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-1">Status</p>
                    <p class="text-sm text-[var(--ink)]">Menunggu</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-1">Total</p>
                    <p class="font-serif text-xl text-[var(--ink)]">
                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Items --}}
            <div class="text-left mb-8">
                <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Detail Pesanan</p>
                <ul class="divide-y divide-[var(--light)]">
                    @foreach($sale->salesDetails as $detail)
                        <li class="py-3 flex justify-between items-center text-sm">
                            <div>
                                <p class="text-[var(--ink)]">{{ $detail->product->name ?? 'Produk' }}</p>
                                <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] mt-0.5">
                                    × {{ $detail->quantity }} pcs
                                </p>
                            </div>
                            <p class="text-[var(--ink)]">Rp {{ number_format($detail->line_total, 0, ',', '.') }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Payment Info --}}
            @php
                $banks     = \App\Models\UserBusinessProfile::getIndonesianBanks();
                $bankLabel = $banks[$businessProfile?->bank_name ?? ''] ?? ($businessProfile?->bank_name ?? '');
                $qrisUrl   = $businessProfile?->qris_image_url ?? null;
                $rawPhone  = preg_replace('/\D/', '', $businessProfile?->contact_phone ?? '');
                if (str_starts_with($rawPhone, '0')) { $rawPhone = '62' . substr($rawPhone, 1); }
                $waMsg = 'Halo, saya ingin konfirmasi pembayaran pesanan saya. No. Invoice: ' . $sale->invoice_number . ' Total: Rp ' . number_format($sale->total_amount, 0, ',', '.');
                $waLink = $rawPhone ? 'https://wa.me/' . $rawPhone . '?text=' . rawurlencode($waMsg) : null;
                $pm = $sale->payment_method;
            @endphp

            @if($pm === 'bank_transfer')
                <div class="border border-[var(--light)] bg-[var(--paper)] p-5 mb-6 text-left">
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
                        <p class="text-sm text-[var(--mid)] italic">Info rekening belum tersedia. Hubungi penjual via WhatsApp.</p>
                    @endif
                </div>
            @elseif($pm === 'qris')
                <div class="border border-[var(--light)] bg-[var(--paper)] p-5 mb-6">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Scan QRIS untuk Membayar</p>
                    @if($qrisUrl)
                        <div class="flex flex-col items-center gap-4">
                            <button type="button" @click="qrisOpen = true"
                                    class="group relative border-4 border-[var(--ink)] p-2 inline-block hover:border-[var(--mid)] transition-colors focus:outline-none">
                                <img src="{{ $qrisUrl }}" alt="QRIS {{ $config->storefront_title }}"
                                     class="w-52 h-52 object-contain block">
                                <span class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/70 text-white text-[10px] uppercase tracking-[0.2em] px-3 py-1.5">
                                        🔍 Perbesar
                                    </span>
                                </span>
                            </button>
                            <p class="text-xs text-[var(--mid)] text-center leading-relaxed max-w-xs">
                                Scan menggunakan dompet digital atau mobile banking mana pun. Setelah membayar, konfirmasi via WhatsApp.
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
                                <button type="button" @click="qrisOpen = false"
                                        class="absolute -top-10 right-0 text-white/70 hover:text-white text-2xl leading-none transition-colors">
                                    ✕
                                </button>
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
                        <p class="text-sm text-[var(--mid)] italic">Kode QRIS belum tersedia. Hubungi penjual via WhatsApp.</p>
                    @endif
                </div>
            @elseif($pm === 'cash')
                <div class="border border-emerald-200 bg-emerald-50 p-5 mb-6 text-left">
                    <p class="text-[10px] uppercase tracking-[0.28em] text-emerald-700 mb-2">Cash on Delivery</p>
                    <p class="text-sm text-emerald-800 leading-relaxed">
                        Pembayaran dilakukan saat barang diterima. Penjual akan menghubungi Anda untuk koordinasi pengiriman.
                    </p>
                </div>
            @else
                <div class="border border-[var(--light)] bg-[var(--paper)] p-5 mb-6 text-left">
                    <p class="text-[10px] uppercase tracking-[0.28em] text-[var(--mid)] mb-2">Detail Pembayaran</p>
                    <p class="text-sm text-[var(--ink)] leading-relaxed">
                        Penjual akan menghubungi Anda via WhatsApp untuk detail pembayaran lebih lanjut.
                    </p>
                </div>
            @endif

            {{-- CTAs --}}
            <div class="flex flex-col gap-3">
                @if($waLink)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs uppercase tracking-[0.2em] font-medium px-8 py-4 hover:bg-emerald-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Konfirmasi Pembayaran via WhatsApp
                    </a>
                @endif
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('public.store.catalog', $config->slug) }}"
                       class="flex-1 text-center bg-[var(--ink)] text-[var(--paper)] text-xs uppercase tracking-[0.2em] font-medium px-8 py-3.5 hover:opacity-90 transition-opacity">
                        Kembali ke Katalog
                    </a>
                    <a href="{{ route('public.store.my-orders', $config->slug) }}"
                       class="flex-1 text-center border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] font-medium px-8 py-3.5 hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                        Lihat Pesanan Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-storefront.public-layout>
