<x-storefront.public-layout :config="$config" pageTitle="Keranjang" :cartPanelItems="$items" :cartPanelTotals="$totals" navMode="solid">

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
                ○ &nbsp; Langkah 1 dari 3 &nbsp; ○
            </p>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.05] mb-5">
                Keranjang <em class="text-[var(--paper)]/70">Belanja</em>
            </h1>
            <p class="text-sm text-[var(--paper)]/55 mb-10">Tinjau pesanan Anda sebelum checkout.</p>

            {{-- Step indicator --}}
            <div class="flex items-center justify-center gap-3 text-xs uppercase tracking-[0.18em]">
                <span class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full border border-[var(--paper)] flex items-center justify-center font-medium">1</span>
                    <span>Keranjang</span>
                </span>
                <span class="w-10 h-px bg-[var(--paper)]/30"></span>
                <span class="flex items-center gap-2 opacity-50">
                    <span class="w-7 h-7 rounded-full border border-[var(--paper)]/40 flex items-center justify-center">2</span>
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

    @if(empty($items))
        <div class="bg-white border border-[var(--light)] py-20 text-center mb-12">
            <span class="font-serif text-6xl text-[var(--mid)]">○</span>
            <h2 class="font-serif text-3xl text-[var(--ink)] mt-4 mb-2">Keranjang Kosong</h2>
            <p class="text-[var(--mid)] mb-8">Anda belum menambahkan produk apa pun.</p>
            <a href="{{ route('public.store.catalog', $config->slug) }}"
               class="inline-block border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] px-8 py-3 hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                Lihat Katalog
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-10 mb-16 px-4 sm:px-6 lg:px-8">
            {{-- Items --}}
            <div class="lg:col-span-2">
                <div class="flex items-end justify-between mb-6">
                    <h2 class="font-serif text-2xl text-[var(--ink)]">{{ count($items) }} Item</h2>
                    <a href="{{ route('public.store.catalog', $config->slug) }}"
                       class="text-xs uppercase tracking-[0.2em] text-[var(--mid)] hover:text-[var(--ink)] transition-colors">
                        ← Lanjut Belanja
                    </a>
                </div>

                <div class="bg-white border border-[var(--light)] divide-y divide-[var(--light)]">
                    @foreach($items as $item)
                        <div class="p-5 flex gap-4 items-center">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-[var(--paper)] flex-shrink-0 overflow-hidden">
                                @if($item['product']->image_path)
                                    <img src="{{ asset('storage/' . $item['product']->image_path) }}"
                                         alt="{{ $item['product']->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-serif text-3xl text-[var(--mid)]">○</div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-serif text-lg text-[var(--ink)] truncate">{{ $item['product']->name }}</h3>
                                @if($item['product']->category)
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] mt-1">{{ $item['product']->category }}</p>
                                @endif
                                <p class="text-sm text-[var(--mid)] mt-1">Rp {{ number_format($item['unit_price'], 0, ',', '.') }} / pcs</p>

                                {{-- qty controls --}}
                                <form method="POST" action="{{ route('public.store.cart.update', [$config->slug, $item['product']->id]) }}"
                                      class="mt-3 flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <div class="flex items-center border border-[var(--ink)]">
                                        <button type="submit" name="quantity" value="{{ max($item['qty'] - 1, 1) }}"
                                                class="qty-btn w-9 h-9 flex items-center justify-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors {{ $item['qty'] <= 1 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                {{ $item['qty'] <= 1 ? 'disabled' : '' }}>−</button>
                                        <span class="w-10 text-center text-sm font-medium">{{ $item['qty'] }}</span>
                                        <button type="submit" name="quantity" value="{{ $item['qty'] + 1 }}"
                                                class="qty-btn w-9 h-9 flex items-center justify-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">+</button>
                                    </div>
                                </form>
                            </div>

                            <div class="text-right flex-shrink-0 flex flex-col items-end gap-3">
                                <p class="font-serif text-lg text-[var(--ink)]">
                                    Rp {{ number_format($item['line_total'], 0, ',', '.') }}
                                </p>
                                <form method="POST" action="{{ route('public.store.cart.remove', [$config->slug, $item['product']->id]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs uppercase tracking-[0.18em] text-[var(--mid)] hover:text-[var(--ink)] transition-colors">
                                        × Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Promo code --}}
                <!-- <div class="mt-6 bg-white border border-[var(--light)] p-5">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Kode Promo</p>
                    <form method="POST" action="#" class="flex gap-3">
                        @csrf
                        <input type="text" name="promo" placeholder="Masukkan kode"
                               class="flex-1 px-4 py-2.5 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none">
                        <button type="submit"
                                class="border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] px-5 hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                            Terapkan
                        </button>
                    </form>
                </div> -->
            </div>

            {{-- Summary --}}
            <aside>
                <div class="bg-white border border-[var(--light)] p-6 sticky top-24">
                    <h2 class="font-serif text-2xl text-[var(--ink)] mb-5 pb-4 border-b border-[var(--light)]">
                        Ringkasan
                    </h2>

                    <div class="space-y-3 text-sm">
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

                    <a href="{{ route('public.store.checkout', $config->slug) }}"
                       class="mt-6 block w-full text-center bg-[var(--ink)] text-[var(--paper)] text-xs uppercase tracking-[0.2em] font-medium py-4 hover:opacity-90 transition-opacity">
                        Lanjut Checkout →
                    </a>

                    <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] text-center mt-4">
                        Konfirmasi langsung oleh penjual
                    </p>
                </div>
            </aside>
        </div>
    @endif

</x-storefront.public-layout>
