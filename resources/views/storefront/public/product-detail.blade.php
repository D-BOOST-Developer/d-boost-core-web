@php
    $stock = (int) ($product->stock_quantity ?? 0);
@endphp

<x-storefront.public-layout :config="$config" :pageTitle="$product->name" navMode="solid">

    @push('head')
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        body > main { flex: 1 0 auto; }
        body > footer { flex-shrink: 0; }
    </style>
    @endpush

    {{-- ════════════════════════════════════════════════════
         HERO HEADER — full-bleed dark
    ═══════════════════════════════════════════════════════ --}}
    <section class="relative bg-[var(--ink)] text-[var(--paper)] overflow-hidden">
        {{-- Decorative rings --}}
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute top-10 right-10 w-[300px] h-[300px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[400px] h-[400px] rounded-full border border-[var(--paper)]"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3 mb-8 text-[10px] uppercase tracking-[0.28em] text-[var(--paper)]/50">
                <a href="{{ route('public.store.show', $config->slug) }}"
                   class="hover:text-[var(--paper)] transition-colors">Beranda</a>
                <span>›</span>
                <a href="{{ route('public.store.catalog', $config->slug) }}"
                   class="hover:text-[var(--paper)] transition-colors">Katalog</a>
                @if($product->category)
                    <span>›</span>
                    <span class="text-[var(--paper)]/70">{{ $product->category }}</span>
                @endif
            </div>

            <div class="max-w-2xl">
                @if($product->category)
                    <p class="text-[10px] uppercase tracking-[0.4em] text-[var(--paper)]/50 mb-4">
                        ○ &nbsp; {{ $product->category }} &nbsp; ○
                    </p>
                @endif
                <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-[1.05] mb-4">
                    {{ $product->name }}
                </h1>
                <p class="font-serif text-2xl md:text-3xl text-[var(--paper)]/70">
                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         PRODUCT BODY — padded container
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20">

            {{-- Image --}}
            <div class="bg-white border border-[var(--light)] aspect-square overflow-hidden">
                @if($product->image_url ?? null)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @elseif($product->image_path ?? null)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="font-serif text-8xl text-[var(--mid)]">○</span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col">

                {{-- Stock indicator --}}
                <div class="flex items-center gap-2 mb-8">
                    @if($stock > 0)
                        <span class="w-2 h-2 rounded-full" style="background-color: var(--accent)"></span>
                        <span class="text-[10px] uppercase tracking-[0.28em] text-[var(--mid)]">Tersedia · {{ $stock }} stok</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        <span class="text-[10px] uppercase tracking-[0.28em] text-[var(--mid)]">Stok Habis</span>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="border-t border-[var(--light)] pt-8 mb-8">
                        <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Deskripsi Produk</p>
                        <p class="text-[var(--ink)] leading-relaxed whitespace-pre-line text-sm md:text-base">{{ $product->description }}</p>
                    </div>
                @endif

                {{-- Add to cart --}}
                @if($stock > 0)
                    <form method="POST" action="{{ route('public.store.cart.add', $config->slug) }}"
                          class="mt-auto border-t border-[var(--light)] pt-8"
                          x-data="{ qty: 1, max: {{ $stock }} }">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" :value="qty">

                        <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Jumlah</p>
                        <div class="flex items-center gap-6 mb-8">
                            <div class="flex items-center border border-[var(--ink)]">
                                <button type="button" @click="if(qty > 1) qty--"
                                        class="qty-btn w-11 h-11 flex items-center justify-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors text-lg">−</button>
                                <span x-text="qty" class="w-14 text-center font-medium"></span>
                                <button type="button" @click="if(qty < max) qty++"
                                        class="qty-btn w-11 h-11 flex items-center justify-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors text-lg">+</button>
                            </div>
                            <span class="text-xs text-[var(--mid)] uppercase tracking-[0.2em]">Maks. {{ $stock }}</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                    class="flex-1 text-xs uppercase tracking-[0.2em] font-medium py-4 transition-opacity hover:opacity-90"
                                    style="background: var(--accent); color: #fff;">
                                + Tambah ke Keranjang
                            </button>
                            <a href="{{ route('public.store.catalog', $config->slug) }}"
                               class="border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] font-medium py-4 px-6 text-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                                ← Kembali
                            </a>
                        </div>
                    </form>
                @else
                    <div class="mt-auto border-t border-[var(--light)] pt-8">
                        <button type="button" disabled
                                class="w-full bg-[var(--light)] text-[var(--mid)] text-xs uppercase tracking-[0.2em] font-medium py-4 cursor-not-allowed">
                            Stok Habis
                        </button>
                        <a href="{{ route('public.store.catalog', $config->slug) }}"
                           class="mt-3 block border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] font-medium py-4 px-6 text-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                            ← Kembali ke Katalog
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-storefront.public-layout>
