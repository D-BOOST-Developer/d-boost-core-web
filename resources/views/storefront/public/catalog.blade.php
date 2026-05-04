@php
    $activeCategory = request('category');
    $activeSearch = request('search');
    $activeSort = request('sort');
    $hasActiveFilter = $activeSearch || $activeCategory || $activeSort;

    $gridLg = max(2, min(5, (int) ($config->grid_columns ?? 4)));
    $gridMd = max(2, min(3, $gridLg));
    $gridStyle = "--grid-md: {$gridMd}; --grid-lg: {$gridLg};";
@endphp

<x-storefront.public-layout :config="$config" pageTitle="Katalog" navMode="solid">

    {{-- ════════════════════════════════════════════════════
    HEADER — full-bleed
    ═══════════════════════════════════════════════════════ --}}
    <section class="relative bg-[var(--ink)] text-[var(--paper)] overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute top-10 right-10 w-[300px] h-[300px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[400px] h-[400px] rounded-full border border-[var(--paper)]">
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24 text-center">
            <p class="text-[10px] uppercase tracking-[0.4em] text-[var(--paper)]/50 mb-5">
                ○ &nbsp; {{ $products->total() }} Produk Tersedia &nbsp; ○
            </p>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.05]">
                Katalog <em class="text-[var(--paper)]/70">Lengkap</em>
            </h1>
            <p class="max-w-xl mx-auto text-sm md:text-base text-[var(--paper)]/65 leading-relaxed mt-6">
                Telusuri seluruh koleksi produk pilihan kami — gunakan filter di bawah untuk menemukan yang Anda
                butuhkan.
            </p>

            @if($hasActiveFilter)
                <div class="mt-8 flex flex-wrap justify-center gap-2">
                    @if($activeSearch)
                        <span
                            class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.24em] px-3 py-1.5 border border-[var(--paper)]/30 text-[var(--paper)]/80">
                            Cari: <span class="text-[var(--paper)] normal-case tracking-normal">"{{ $activeSearch }}"</span>
                        </span>
                    @endif
                    @if($activeCategory)
                        <span
                            class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.24em] px-3 py-1.5 border border-[var(--paper)]/30 text-[var(--paper)]/80">
                            Kategori: <span class="text-[var(--paper)]">{{ $activeCategory }}</span>
                        </span>
                    @endif
                    @if($activeSort)
                        <span
                            class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.24em] px-3 py-1.5 border border-[var(--paper)]/30 text-[var(--paper)]/80">
                            Urutkan
                        </span>
                    @endif
                    <a href="{{ route('public.store.catalog', $config->slug) }}"
                        class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.24em] px-3 py-1.5 border border-[var(--paper)]/30 text-[var(--paper)]/60 hover:bg-[var(--paper)] hover:text-[var(--ink)] hover:border-[var(--paper)] transition-colors">
                        × Reset Semua
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
    CONTAINED CONTENT
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">

        {{-- ════════════════════════════════════════════════════
        FILTER BAR
        ═══════════════════════════════════════════════════════ --}}
        <form method="GET" action="{{ route('public.store.catalog', $config->slug) }}"
            class="bg-white border border-[var(--light)] p-5 md:p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 items-stretch">
                <div class="md:col-span-7 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--mid)]">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $activeSearch }}"
                        placeholder="Cari nama atau kategori produk…"
                        class="w-full pl-10 pr-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none transition-colors">
                </div>
                <div class="md:col-span-3">
                    <select name="sort"
                        class="w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none transition-colors">
                        <option value="">Urutkan</option>
                        <option value="newest" @selected($activeSort === 'newest')>Terbaru</option>
                        <option value="price_asc" @selected($activeSort === 'price_asc')>Harga Terendah</option>
                        <option value="price_desc" @selected($activeSort === 'price_desc')>Harga Tertinggi</option>
                        <option value="name" @selected($activeSort === 'name')>Nama A–Z</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                        class="w-full bg-[var(--ink)] text-[var(--paper)] text-xs uppercase tracking-[0.2em] font-medium px-6 py-3 hover:opacity-90 transition-opacity">
                        Terapkan
                    </button>
                </div>
            </div>

            @if($activeCategory)
                <input type="hidden" name="category" value="{{ $activeCategory }}">
            @endif
        </form>

        {{-- ════════════════════════════════════════════════════
        CATEGORY PILLS
        ═══════════════════════════════════════════════════════ --}}
        @if(($categories ?? collect())->isNotEmpty())
            <div class="mb-10">
                <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Kategori</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('public.store.catalog', $config->slug) }}{{ $activeSearch ? '?search=' . urlencode($activeSearch) : '' }}"
                        class="text-xs uppercase tracking-[0.18em] px-4 py-2 border transition-colors
                                  {{ !$activeCategory ? 'bg-[var(--ink)] text-[var(--paper)] border-[var(--ink)]' : 'bg-white text-[var(--ink)] border-[var(--light)] hover:border-[var(--ink)]' }}">
                        Semua
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('public.store.catalog', $config->slug) }}?category={{ urlencode($cat) }}{{ $activeSearch ? '&search=' . urlencode($activeSearch) : '' }}"
                            class="text-xs uppercase tracking-[0.18em] px-4 py-2 border transition-colors
                                          {{ $activeCategory === $cat ? 'bg-[var(--ink)] text-[var(--paper)] border-[var(--ink)]' : 'bg-white text-[var(--ink)] border-[var(--light)] hover:border-[var(--ink)]' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ════════════════════════════════════════════════════
        PRODUCT GRID
        ═══════════════════════════════════════════════════════ --}}
        <section>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-2">Koleksi</p>
                    <h2 class="font-serif text-2xl md:text-3xl text-[var(--ink)]">Produk <em>Toko</em></h2>
                </div>
                <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)]">
                    Menampilkan {{ $products->count() }} dari {{ $products->total() }} item
                </p>
            </div>

            @if($products->isEmpty())
                <div class="bg-white border border-[var(--light)] py-24 text-center">
                    <span class="font-serif text-6xl text-[var(--mid)]">○</span>
                    <h3 class="font-serif text-3xl text-[var(--ink)] mt-4">Tidak Ada Produk</h3>
                    <p class="text-sm text-[var(--mid)] mt-2 mb-8 max-w-sm mx-auto">
                        Tidak ada produk yang cocok dengan filter Anda. Coba kata kunci lain atau lihat seluruh koleksi.
                    </p>
                    @if($hasActiveFilter)
                        <a href="{{ route('public.store.catalog', $config->slug) }}"
                            class="inline-block border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] px-8 py-3 hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                            Lihat Semua Produk
                        </a>
                    @endif
                </div>
            @else
                <div class="config-grid" style="{{ $gridStyle }}">
                    @foreach($products as $product)
                        @include('storefront.public._storefront_card', [
                            'item' => $product,
                            'type' => 'product',
                            'config' => $config,
                            'isPreview' => false,
                        ])
                    @endforeach
                </div>
                <div class="mt-14 pagination-wrap">
                        {{ $products->withQueryString()->links() }}
                    </div>
            @endif
        </section>
        {{-- ════════════════════════════════════════════════════
             MARKETPLACE LINKS
    ═══════════════════════════════════════════════════════ --}}
    @if(($links ?? collect())->isNotEmpty())
        <section class="mb-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-2">Tersedia juga di</p>
                    <h2 class="font-serif text-2xl md:text-3xl text-[var(--ink)]">Marketplace <em>Resmi</em></h2>
                </div>
                <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)]">{{ $links->count() }} Platform</p>
            </div>
        <div class="config-grid" style="{{ $gridStyle }}">
                @foreach($links as $link)
                    @include('storefront.public._storefront_card', [
                        'item' => $link,
                        'type' => 'link',
                        'config' => $config,
                        'isPreview' => false,
                    ])
                @endforeach
                </div>
            </section>
    @endif

    </div>

    @push('scripts')
        <script>
            function trackAndRedirect(linkId, url) {
                fetch(`/store/{{ $config->slug }}/track/${linkId}`, {
                    method: 'POST',
                    headers: {
                         'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).catch(() => {});
                window.open(url, '_blank');
            }
        </script>
    @endpush

</x-storefront.public-layout>
