<x-storefront.public-layout :config="$config" pageTitle="Pesanan Saya" navMode="solid">

    @push('head')
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        body > main { flex: 1 0 auto; }
        body > footer { flex-shrink: 0; }
    </style>
    @endpush

    {{-- ════════════════════════════════════════════════
         HERO HEADER — full-bleed, dark, editorial
    ═══════════════════════════════════════════════════ --}}
    <section class="relative bg-[var(--ink)] text-[var(--paper)] overflow-hidden">
        {{-- Decorative rings --}}
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute top-10 right-10 w-[300px] h-[300px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[400px] h-[400px] rounded-full border border-[var(--paper)]"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center">
            <p class="text-[10px] uppercase tracking-[0.4em] text-[var(--paper)]/50 mb-5">
                ○ &nbsp; Riwayat Sesi Ini &nbsp; ○
            </p>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.05]">
                Pesanan <em class="text-[var(--paper)]/70">Saya</em>
            </h1>
            <p class="max-w-lg mx-auto text-sm md:text-base text-[var(--paper)]/55 leading-relaxed mt-6">
                Pantau status pesanan Anda secara langsung. Data pesanan tersimpan selama sesi browser ini aktif.
            </p>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════
         CONTENT — padded container
    ═══════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">

    @if($orders->isEmpty())
        <div class="bg-white border border-[var(--light)] py-24 text-center">
            <span class="font-serif text-7xl text-[var(--mid)]">○</span>
            <h2 class="font-serif text-3xl md:text-4xl text-[var(--ink)] mt-6 mb-3">Belum Ada Pesanan</h2>
            <p class="text-[var(--mid)] mb-10 max-w-md mx-auto leading-relaxed">
                Anda belum melakukan pesanan apa pun pada sesi ini. Jelajahi katalog dan temukan produk yang Anda butuhkan.
            </p>
            <a href="{{ route('public.store.catalog', $slug) }}"
               class="inline-block border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] px-10 py-3.5 hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                Lihat Katalog
            </a>
        </div>
    @else
        <div class="space-y-6 mb-16 max-w-7xl mx-auto">
            @foreach($orders as $order)
                @php
                    $statusKey = $order->status;
                    $statusText = match($statusKey) {
                        'pending'                => 'Menunggu',
                        'confirmed', 'completed' => 'Dikonfirmasi',
                        'cancelled'              => 'Dibatalkan',
                        default                  => ucfirst($statusKey),
                    };
                    $statusBg = match($statusKey) {
                        'confirmed', 'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'cancelled'              => 'bg-red-50 text-red-700 border-red-200',
                        default                  => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                @endphp

                <article class="bg-white border border-[var(--light)]" x-data="{ qrisOpen: false }">
                    {{-- Header strip --}}
                    <header class="px-6 py-4 border-b border-[var(--light)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[var(--paper)]/40">
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono text-sm text-[var(--ink)]">#{{ $order->invoice_number }}</span>
                                <span class="text-[10px] uppercase tracking-[0.2em] px-2.5 py-1 border {{ $statusBg }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] mt-1.5">
                                {{ $order->created_at->format('d M Y · H:i') }}
                            </p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)]">Total</p>
                            <p class="font-serif text-xl text-[var(--ink)]">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </header>

                    {{-- Items --}}
                    <ul class="divide-y divide-[var(--light)] px-6">
                        @foreach($order->salesDetails as $detail)
                            <li class="py-4 flex items-center gap-4">
                                <div class="w-14 h-14 bg-[var(--paper)] flex-shrink-0 overflow-hidden">
                                    @if($detail->product && $detail->product->image_path)
                                        <img src="{{ asset('storage/' . $detail->product->image_path) }}"
                                             alt="{{ $detail->product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-serif text-2xl text-[var(--mid)]">○</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[var(--ink)] truncate">{{ $detail->product->name ?? 'Produk Dihapus' }}</p>
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] mt-0.5">
                                        {{ $detail->quantity }} × Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="text-sm text-[var(--ink)] whitespace-nowrap">
                                    Rp {{ number_format($detail->line_total, 0, ',', '.') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>

                    @if($order->notes)
                        <div class="px-6 py-4 border-t border-[var(--light)] bg-[var(--paper)]/40">
                            <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-1.5">Catatan Pembeli</p>
                            <p class="text-sm text-[var(--ink)]">{{ $order->notes }}</p>
                        </div>
                    @endif

                    {{-- Rejection Reason (if cancelled) --}}
                    @if($order->status === 'cancelled' && $order->rejection_reason)
                        <div class="px-6 py-4 border-t border-red-200 bg-red-50 text-left">
                            <p class="text-[10px] uppercase tracking-[0.24em] text-red-600 font-bold mb-1.5">Alasan Pembatalan</p>
                            <p class="text-sm text-red-800">{{ $order->rejection_reason }}</p>
                        </div>
                    @endif

                    {{-- Payment info + WA button (only for pending/confirmed, not cancelled) --}}
                    @if($order->status !== 'cancelled')
                        @php
                            $orderPm   = $order->payment_method;
                            $bp        = $businessProfile ?? null;
                            $bpBanks   = \App\Models\UserBusinessProfile::getIndonesianBanks();
                            $bpBankLbl = $bpBanks[$bp?->bank_name ?? ''] ?? ($bp?->bank_name ?? '');
                            $bpQris    = $bp?->qris_image_url ?? null;
                            $bpPhone   = preg_replace('/\D/', '', $bp?->contact_phone ?? '');
                            if (str_starts_with($bpPhone, '0')) { $bpPhone = '62' . substr($bpPhone, 1); }
                            $bpWaMsg   = 'Halo, saya ingin konfirmasi pembayaran pesanan. No. Invoice: ' . $order->invoice_number . ' | Total: Rp ' . number_format($order->total_amount, 0, ',', '.');
                            $bpWaLink  = $bpPhone ? 'https://wa.me/' . $bpPhone . '?text=' . rawurlencode($bpWaMsg) : null;
                        @endphp

                        <div class="px-6 py-5 border-t border-[var(--light)]">
                            <p class="text-[10px] uppercase tracking-[0.28em] text-[var(--mid)] mb-3">Informasi Pembayaran</p>

                            @if($orderPm === 'bank_transfer')
                                @if($bp && $bp->bank_name && $bp->bank_account_number)
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-[var(--mid)] text-xs uppercase tracking-[0.18em]">Bank</span>
                                            <span class="text-[var(--ink)] font-medium">{{ $bpBankLbl }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-[var(--mid)] text-xs uppercase tracking-[0.18em]">No. Rekening</span>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-[var(--ink)] tracking-wider">{{ $bp->bank_account_number }}</span>
                                                <button type="button"
                                                        onclick="navigator.clipboard.writeText('{{ $bp->bank_account_number }}').then(()=>{ const e=this; e.textContent='✓'; setTimeout(()=>e.textContent='⧉',1500) })"
                                                        class="text-[10px] text-[var(--mid)] hover:text-[var(--ink)] px-1.5 py-0.5 border border-[var(--light)] transition-colors">⧉</button>
                                            </div>
                                        </div>
                                        @if($bp->bank_account_holder)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-[var(--mid)] text-xs uppercase tracking-[0.18em]">Atas Nama</span>
                                                <span class="text-[var(--ink)]">{{ $bp->bank_account_holder }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-sm text-[var(--mid)] italic mb-3">Info rekening belum tersedia. Hubungi penjual via WhatsApp.</p>
                                @endif

                            @elseif($orderPm === 'qris')
                                @if($bpQris)
                                    <div class="flex flex-col items-center gap-3 mb-4">
                                        <button type="button" @click="qrisOpen = true"
                                                class="group relative border-4 border-[var(--ink)] p-1.5 inline-block hover:border-[var(--mid)] transition-colors focus:outline-none">
                                            <img src="{{ $bpQris }}" alt="QRIS"
                                                 class="w-40 h-40 object-contain block">
                                            <span class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                                <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/70 text-white text-[10px] uppercase tracking-[0.18em] px-2 py-1">
                                                    🔍 Perbesar
                                                </span>
                                            </span>
                                        </button>
                                        <p class="text-xs text-[var(--mid)] text-center">
                                            Scan QRIS untuk membayar pesanan ini.
                                            <span class="block mt-0.5 text-[10px] uppercase tracking-[0.15em] opacity-60">Ketuk gambar untuk memperbesar</span>
                                        </p>
                                    </div>

                                    {{-- QRIS Lightbox --}}
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
                                                <img src="{{ $bpQris }}"
                                                     alt="QRIS"
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
                                    <p class="text-sm text-[var(--mid)] italic mb-3">Kode QRIS belum tersedia. Hubungi penjual via WhatsApp.</p>
                                @endif

                            @elseif($orderPm === 'cash')
                                <p class="text-sm text-[var(--ink)] mb-4">Pembayaran Cash on Delivery — lakukan saat barang diterima.</p>

                            @else
                                <p class="text-sm text-[var(--ink)] mb-4">Hubungi penjual via WhatsApp untuk detail pembayaran.</p>
                            @endif

                            @if($bpWaLink)
                                <a href="{{ $bpWaLink }}" target="_blank" rel="noopener"
                                   class="flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs uppercase tracking-[0.18em] font-medium py-3 hover:bg-emerald-700 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Konfirmasi Pembayaran via WhatsApp
                                </a>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif

    </div>{{-- /max-w-7xl --}}

</x-storefront.public-layout>
