@php
    /**
     * Unified MONŌ-style card for storefront grids.
     * @var \App\Models\Product|\App\Models\MarketplaceLink $item
     * @var string $type 'product' or 'link'
     * @var \App\Models\StorefrontConfig $config
     * @var bool $isPreview
     */
    $isPreview = $isPreview ?? false;
    $isProduct = ($type ?? 'product') === 'product';
    $cardStyleClass = 'card-style-' . ($config->card_style ?: 'flat');

    if ($isProduct) {
        $title          = $item->name;
        $description    = $item->description;
        $priceFormatted = 'Rp ' . number_format((float) ($item->price ?? 0), 0, ',', '.');
        $imageUrl       = $item->image_url ?? null;
        $tag            = $item->category ?: 'BARU';
        $stock          = (int) ($item->stock_quantity ?? 0);
        $href           = $isPreview ? '#' : route('public.store.product-detail', ['slug' => $config->slug, 'product' => $item->id]);
    } else {
        $title          = $item->display_name;
        $description    = $item->description;
        $priceFormatted = $item->formatted_price;
        $imageUrl       = $item->hasImage() ? $item->image_url : null;
        $tag            = strtoupper($item->platform);
        $stock          = null;
        $href           = '#';
    }
@endphp

<article class="product-card {{ $cardStyleClass }} fade-in group flex flex-col overflow-hidden">
    {{-- Image area --}}
    @if($isProduct)
        <a href="{{ $href }}" class="card-img-wrap block relative w-full aspect-square overflow-hidden bg-[var(--paper)]">
    @else
        <div class="card-img-wrap block relative w-full aspect-square overflow-hidden bg-[var(--paper)] cursor-pointer"
             @if(!$isPreview) onclick="trackAndRedirect('{{ $item->id }}', '{{ $item->url }}')" @endif>
    @endif

        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" loading="lazy"
                 class="product-image w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
        @else
            <div class="flex items-center justify-center h-full w-full">
                @if($isProduct)
                    <span class="font-serif text-[var(--mid)] text-5xl">○</span>
                @else
                    <span class="text-6xl">{{ $item->platform_icon }}</span>
                @endif
            </div>
        @endif

        {{-- Tag pill --}}
        @if($tag)
            <span class="cat-tag absolute top-3 left-3 bg-[var(--ink)] text-[var(--paper)] text-[10px] font-medium uppercase tracking-[0.18em] px-2.5 py-1">
                {{ $tag }}
            </span>
        @endif

    @if($isProduct)
        </a>
    @else
        </div>
    @endif

    {{-- Body --}}
    <div class="p-5 sm:p-6 flex flex-col flex-1">
        <h3 class="font-serif text-lg sm:text-xl text-[var(--ink)] leading-snug mb-1.5 line-clamp-2">
            @if($isProduct)
                <a href="{{ $href }}" class="hover:underline">{{ $title }}</a>
            @else
                {{ $title }}
            @endif
        </h3>

        @if($description)
            <p class="text-[var(--mid)] text-sm mb-4 line-clamp-2 leading-relaxed">
                {{ $description }}
            </p>
        @endif

        <div class="mt-auto pt-4 border-t border-[var(--light)] flex items-center justify-between gap-3">
            <p class="price font-serif text-xl text-[var(--ink)]">
                {{ $priceFormatted ?: '—' }}
            </p>

            @if($isProduct)
                @if($stock <= 0)
                    <button type="button" disabled
                            class="text-[10px] uppercase tracking-[0.18em] font-medium px-3 py-2 border border-[var(--light)] text-[var(--mid)] cursor-not-allowed">
                        Stok Habis
                    </button>
                @elseif($isPreview)
                    <button type="button" disabled
                            class="text-[10px] uppercase tracking-[0.18em] font-medium px-3 py-2 border border-[var(--ink)] text-[var(--ink)] opacity-60 cursor-not-allowed">
                        Tambah
                    </button>
                @else
                    <form method="POST" action="{{ route('public.store.cart.add', $config->slug) }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                                class="text-[10px] uppercase tracking-[0.18em] font-medium px-3 py-2 border border-[var(--ink)] text-[var(--ink)] hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                            + Tambah
                        </button>
                    </form>
                @endif
            @else
                <button type="button"
                        @if(!$isPreview) onclick="trackAndRedirect('{{ $item->id }}', '{{ $item->url }}')" @else disabled @endif
                        class="text-[10px] uppercase tracking-[0.18em] font-medium px-3 py-2 border border-[var(--ink)] text-[var(--ink)] hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors @if($isPreview) opacity-60 cursor-not-allowed @endif">
                    Beli →
                </button>
            @endif
        </div>
    </div>
</article>
