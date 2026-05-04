@props(['config', 'cartCount' => null, 'pageTitle' => null, 'cartPanelItems' => null, 'cartPanelTotals' => null, 'navMode' => 'auto'])

@php
    $cartSession = session("storefront_cart.{$config->slug}", []);
    if ($cartCount === null) {
        $cartCount = collect($cartSession)->sum('qty');
    }

    // Lazy-load cart panel items only if not provided by parent
    if ($cartPanelItems === null) {
        $cartPanelItems = [];
        $cartPanelSubtotal = 0;
        foreach ($cartSession as $productId => $line) {
            $product = \App\Models\Product::withoutGlobalScope('user')
                ->where('id', $productId)
                ->where('user_id', $config->user_id)
                ->first();
            if (!$product) continue;
            $unit = (float) $product->price;
            $qty  = (int) ($line['qty'] ?? 1);
            $lineTotal = $unit * $qty;
            $cartPanelSubtotal += $lineTotal;
            $cartPanelItems[] = [
                'product' => $product,
                'qty' => $qty,
                'unit_price' => $unit,
                'line_total' => $lineTotal,
            ];
        }
        $cartPanelTotals = [
            'subtotal' => $cartPanelSubtotal,
            'tax' => $cartPanelSubtotal * 0.1,
            'total' => $cartPanelSubtotal + ($cartPanelSubtotal * 0.1),
        ];
    }

    $accent          = $config->primary_color   ?: '#0a0a0a';
    $accentSecondary = $config->secondary_color ?: '#888888';
    $accentText      = $config->text_color      ?: '#0a0a0a';
    $cardStyle       = $config->card_style      ?: 'flat';
    $gridCols        = (int) ($config->grid_columns ?? 4);
    $textBlocks  = is_string($config->text_blocks) ? json_decode($config->text_blocks, true) : ($config->text_blocks ?? []);
    $socialLinks = is_string($config->social_links) ? json_decode($config->social_links, true) : ($config->social_links ?? []);

    // Smart contrast for accent: darken when too light against paper
    $hexToRgb = function ($h) {
        $h = ltrim($h, '#');
        if (strlen($h) === 3) $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        return [hexdec(substr($h,0,2)), hexdec(substr($h,2,2)), hexdec(substr($h,4,2))];
    };
    [$ar,$ag,$ab] = $hexToRgb($accent);
    $accentLuminance = (0.299*$ar + 0.587*$ag + 0.114*$ab) / 255;
    $accentOnInk   = $accentLuminance > 0.45;
    $accentOnPaper = $accentLuminance < 0.85;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ? $pageTitle . ' — ' . $config->storefront_title : $config->storefront_title }}</title>
    <meta name="description" content="{{ $config->meta_description ?? $config->tagline }}">

    <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $config->storefront_title }}">
    <meta property="og:description" content="{{ $config->tagline }}">
    <meta property="og:type" content="website">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #0a0a0a;
            --paper: #f5f4f0;
            --white: #ffffff;
            --mid: #888888;
            --light: #e8e6e1;
            --accent: {{ $accent }};
            --accent-secondary: {{ $accentSecondary }};
            --accent-text: {{ $accentText }};
            --primary-color: {{ $accent }};
        }
        /* User-configured accent applied as subtle layer over MONŌ base */
        .accent-text { color: var(--accent); }
        .accent-text-secondary { color: var(--accent-secondary); }
        .accent-bg { background-color: var(--accent); }
        .accent-border { border-color: var(--accent); }
        .accent-ring:focus { outline: none; box-shadow: 0 0 0 2px var(--accent); }
        input.input-field:focus,
        textarea.input-field:focus,
        select.input-field:focus { border-color: var(--accent); }
        a.accent-link { color: var(--accent); transition: opacity .2s; }
        a.accent-link:hover { opacity: 0.7; }
        .price { color: var(--accent-text); }
        h1, h2, h3, h4, h5, h6 { color: var(--accent-text); }
        .accent-badge {
            background: var(--accent); color: #fff;
        }
        #cart-count-badge {
            background: var(--accent) !important;
            color: #fff !important;
        }
        .product-image-tag {
            background: var(--accent); color: #fff;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }
        .font-display { font-family: 'Playfair Display', serif; }

        /* ── BUTTONS ── */
        .btn-dark {
            background: var(--ink); color: var(--paper);
            padding: 14px 32px; font-size: 12px; letter-spacing: 0.12em;
            text-transform: uppercase; font-weight: 500;
            border: 1px solid var(--ink);
            transition: all 0.3s ease; cursor: pointer; display: inline-block;
            text-align: center;
        }
        .btn-dark:hover { background: transparent; color: var(--ink); }
        .btn-outline {
            background: transparent; color: var(--ink);
            padding: 14px 32px; font-size: 12px; letter-spacing: 0.12em;
            text-transform: uppercase; font-weight: 500;
            border: 1px solid var(--ink);
            transition: all 0.3s ease; cursor: pointer; display: inline-block;
            text-align: center;
        }
        .btn-outline:hover { background: var(--ink); color: var(--paper); }
        .btn-white {
            background: var(--paper); color: var(--ink);
            padding: 16px 40px; font-size: 12px; letter-spacing: 0.12em;
            text-transform: uppercase; font-weight: 500;
            border: 1px solid var(--paper);
            transition: all 0.3s ease; cursor: pointer; display: inline-block;
            text-align: center;
        }
        .btn-white:hover { background: transparent; color: var(--paper); }

        /* ── BADGES & TAGS ── */
        .cat-tag {
            font-size: 10px; letter-spacing: 0.15em; text-transform: uppercase;
            color: var(--mid); font-weight: 500;
        }
        .badge {
            display: inline-block; font-size: 10px; letter-spacing: 0.15em;
            text-transform: uppercase; font-weight: 500;
            padding: 6px 14px; border: 1px solid;
        }
        .price { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 600; }

        /* ── DIVIDER ── */
        .divider { height: 1px; background: rgba(10,10,10,0.08); }

        /* ── INPUTS ── */
        .input-field {
            width: 100%; padding: 14px 0;
            border: none; border-bottom: 1px solid rgba(10,10,10,0.15);
            background: transparent; font-family: 'DM Sans', sans-serif;
            font-size: 14px; outline: none; color: var(--ink);
            transition: border-color 0.2s;
        }
        .input-field:focus { border-color: var(--ink); }
        .input-field::placeholder { color: var(--mid); font-size: 13px; }

        /* ── QTY BUTTON ── */
        .qty-btn {
            width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(10,10,10,0.15); cursor: pointer;
            transition: all 0.2s; font-size: 16px; font-weight: 300;
            background: transparent; color: var(--ink);
        }
        .qty-btn:hover { background: var(--ink); color: var(--paper); }

        /* ── PRODUCT CARD ── */
        .product-card {
            background: var(--white);
            border: 1px solid rgba(10,10,10,0.07);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        box-shadow 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        border-color 0.3s ease;
            position: relative; overflow: hidden;
            display: flex; flex-direction: column;
        }
        .product-card:hover { transform: translateY(-6px); }

        /* Card style variants — driven by user config */
        .card-style-shadow.product-card { box-shadow: 0 4px 16px rgba(10,10,10,0.06); border-color: transparent; }
        .card-style-shadow.product-card:hover { box-shadow: 0 24px 60px rgba(10,10,10,0.14); }

        .card-style-bordered.product-card { border-color: var(--ink); border-width: 1.5px; }
        .card-style-bordered.product-card:hover { box-shadow: 6px 6px 0 0 var(--ink); transform: translateY(-3px) translateX(-3px); }

        .card-style-flat.product-card { border-color: rgba(10,10,10,0.08); box-shadow: none; }
        .card-style-flat.product-card:hover { border-color: var(--ink); box-shadow: 0 12px 30px rgba(10,10,10,0.08); }

        .card-style-minimal.product-card { background: transparent; border: none; box-shadow: none; }
        .card-style-minimal.product-card:hover { transform: translateY(-4px); }
        .card-style-minimal.product-card .card-img-wrap { background: var(--white); }
        .product-card:hover .card-img-wrap img { transform: scale(1.05); }
        .card-img-wrap {
            width: 100%; aspect-ratio: 1;
            background: #faf9f6;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; position: relative;
        }
        .card-img-wrap img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .card-img-emoji { font-size: 4rem; }

        /* ── CART PANEL ── */
        .panel {
            position: fixed; top: 0; right: -100%; width: 100%; max-width: 480px;
            height: 100vh; background: var(--white);
            z-index: 200; transition: right 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            display: flex; flex-direction: column;
            border-left: 1px solid rgba(10,10,10,0.08);
            box-shadow: -20px 0 60px rgba(10,10,10,0.08);
        }
        .panel.open { right: 0; }
        .overlay {
            position: fixed; inset: 0; background: rgba(10,10,10,0.4);
            z-index: 150; opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(4px);
        }
        .overlay.show { opacity: 1; pointer-events: all; }
        .panel-body { flex: 1; overflow-y: auto; padding: 24px; }

        /* ── TOAST ── */
        .toast {
            position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%) translateY(120px);
            background: var(--ink); color: var(--paper);
            padding: 14px 28px; font-size: 13px; letter-spacing: 0.04em;
            z-index: 999; transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            white-space: nowrap; max-width: 90vw; overflow: hidden; text-overflow: ellipsis;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }

        /* ── FADE IN ── */
        .fade-in { opacity: 0; transform: translateY(30px); transition: opacity 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94), transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
        .fade-in.visible { opacity: 1; transform: translateY(0); }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(10,10,10,0.2); }

        /* ── PAGINATION (Laravel) ── */
        .pagination-wrap nav { display: flex; justify-content: center; gap: 4px; }
        .pagination-wrap a, .pagination-wrap span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 12px;
            font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase;
            border: 1px solid rgba(10,10,10,0.12);
            color: var(--ink); background: var(--white);
            transition: all 0.2s;
        }
        .pagination-wrap a:hover { background: var(--ink); color: var(--paper); border-color: var(--ink); }
        .pagination-wrap [aria-current="page"] span,
        .pagination-wrap .bg-blue-500,
        .pagination-wrap .bg-blue-600 { background: var(--ink) !important; color: var(--paper) !important; border-color: var(--ink) !important; }

        @media (max-width: 768px) {
            .panel { max-width: 100%; }
        }

        /* ── DYNAMIC NAV ── */
        .nav-root {
            position: fixed; top: 0; left: 0; right: 0; z-index: 40;
            transition: background-color 0.4s ease, border-color 0.4s ease,
                        color 0.4s ease, backdrop-filter 0.4s ease, box-shadow 0.4s ease;
        }
        /* Default state — over the dark hero (paper-on-ink): transparent canvas, paper text */
        .nav-root[data-state="hero"] {
            background-color: transparent;
            border-bottom-color: rgba(245, 244, 240, 0.10);
            color: var(--paper);
        }
        /* Scrolled state — over paper bg: solid paper canvas, ink text */
        .nav-root[data-state="scrolled"] {
            background-color: rgba(245, 244, 240, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom-color: rgba(10, 10, 10, 0.08);
            color: var(--ink);
            box-shadow: 0 1px 0 rgba(10,10,10,0.04);
        }
        /* Pages without a dark hero (e.g. catalog/cart) get scrolled-style at top too */
        .nav-root[data-mode="solid"][data-state="hero"] {
            background-color: rgba(245, 244, 240, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom-color: rgba(10, 10, 10, 0.08);
            color: var(--ink);
        }
        .nav-root .nav-link { color: inherit; transition: opacity 0.2s; }
        .nav-root .nav-link:hover { opacity: 0.55; }
        .nav-root[data-state="hero"] .nav-link.active { color: var(--paper); border-bottom: 1px solid var(--paper); padding-bottom: 2px; }
        .nav-root[data-state="scrolled"] .nav-link.active { color: var(--ink); border-bottom: 1px solid var(--ink); padding-bottom: 2px; }

        /* Cart badge stays visible in both states */
        .nav-root[data-state="hero"] #cart-count-badge {
            box-shadow: 0 0 0 2px var(--ink);
        }

        /* Mobile menu */
        .mobile-menu {
            position: fixed; top: 0; right: 0; bottom: 0; width: 80%; max-width: 320px;
            background: var(--paper); transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 180; padding: 24px; display: flex; flex-direction: column;
        }
        .mobile-menu.open { transform: translateX(0); }

        /* ── DYNAMIC GRID (driven by config) ── */
        .config-grid { display: grid; gap: 1.5rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        @media (min-width: 768px) {
            .config-grid { gap: 1.75rem; grid-template-columns: repeat(var(--grid-md, 3), minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .config-grid { gap: 2rem; grid-template-columns: repeat(var(--grid-lg, 4), minmax(0, 1fr)); }
        }
    </style>
    @stack('head')
</head>
<body>

{{-- ═══════════════════════════════════════════════
     NAV — dynamic transparent ↔ solid
═══════════════════════════════════════════════ --}}
@php
    // Pages with a dark hero at the top (so nav can start transparent over it):
    $hasDarkHero = ($navMode ?? 'auto') !== 'solid';
@endphp
<nav id="nav-root" class="nav-root border-b" data-state="hero" data-mode="{{ $hasDarkHero ? 'auto' : 'solid' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between gap-6">
        <a href="{{ route('public.store.show', $config->slug) }}" class="flex items-center gap-3 min-w-0 nav-link">
            @if($config->hasLogo())
                <img src="{{ $config->logo_url }}" alt="{{ $config->storefront_title }}" class="h-9 w-9 object-cover">
            @endif
            <span class="font-display text-xl md:text-2xl tracking-widest font-semibold truncate">
                {{ strtoupper($config->storefront_title) }}
            </span>
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('public.store.show', $config->slug) }}" class="nav-link text-xs tracking-widest uppercase font-medium">Beranda</a>
            <a href="{{ route('public.store.catalog', $config->slug) }}" class="nav-link text-xs tracking-widest uppercase font-medium">Katalog</a>
            <a href="{{ route('public.store.show', $config->slug) }}#about" class="nav-link text-xs tracking-widest uppercase font-medium">Tentang</a>
            <a href="{{ route('public.store.show', $config->slug) }}#reviews" class="nav-link text-xs tracking-widest uppercase font-medium">Review</a>
            <a href="{{ route('public.store.my-orders', $config->slug) }}" class="nav-link text-xs tracking-widest uppercase font-medium">Pesanan</a>
        </div>

        <div class="flex items-center gap-5">
            <a href="{{ route('public.store.catalog', $config->slug) }}" class="hidden md:flex nav-link" title="Cari produk">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </a>
            <button type="button" onclick="openCartPanel()" class="relative flex items-center nav-link" aria-label="Keranjang">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span id="cart-count-badge"
                      class="absolute -top-2 -right-2 text-[9px] w-4 h-4 flex items-center justify-center font-bold {{ $cartCount > 0 ? '' : 'hidden' }}">
                    {{ $cartCount }}
                </span>
            </button>
            <button type="button" onclick="openMobileMenu()" class="md:hidden nav-link" aria-label="Menu">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>
</nav>

{{-- Mobile menu --}}
<aside id="mobile-menu" class="mobile-menu">
    <div class="flex items-center justify-between mb-10">
        <span class="font-display text-lg tracking-widest">{{ strtoupper($config->storefront_title) }}</span>
        <button type="button" onclick="closeMobileMenu()" aria-label="Tutup">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="flex flex-col gap-5 text-sm uppercase tracking-[0.18em] font-medium">
        <a href="{{ route('public.store.show', $config->slug) }}">Beranda</a>
        <a href="{{ route('public.store.catalog', $config->slug) }}">Katalog</a>
        <a href="{{ route('public.store.show', $config->slug) }}#about">Tentang</a>
        <a href="{{ route('public.store.show', $config->slug) }}#reviews">Review</a>
        <a href="{{ route('public.store.show', $config->slug) }}#contact">Kontak</a>
        <a href="{{ route('public.store.my-orders', $config->slug) }}">Pesanan Saya</a>
    </nav>
    <div class="mt-auto pt-6 border-t border-black/10 text-[10px] uppercase tracking-[0.24em] text-[var(--mid)]">
        © {{ date('Y') }} {{ $config->storefront_title }}
    </div>
</aside>

{{-- ═══════════════════════════════════════════════
     OVERLAY + TOAST
═══════════════════════════════════════════════ --}}
<div id="cart-overlay" class="overlay"></div>
<div id="toast" class="toast"></div>

{{-- ═══════════════════════════════════════════════
     CART PANEL (slide-in)
═══════════════════════════════════════════════ --}}
<aside id="cart-panel" class="panel">
    <div class="flex items-center justify-between px-6 py-5 border-b border-black/8">
        <div>
            <span class="font-display text-xl font-semibold">Keranjang Anda</span>
            <span class="ml-3 text-xs text-gray-400 tracking-widest uppercase font-medium">{{ count($cartPanelItems) }} item</span>
        </div>
        <button type="button" onclick="closeCartPanel()" class="hover:opacity-50 transition-opacity" aria-label="Tutup">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="panel-body">
        @if(empty($cartPanelItems))
            <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                <div class="text-6xl mb-6 opacity-20">○</div>
                <p class="text-sm tracking-widest uppercase text-gray-400 font-medium">Keranjang Kosong</p>
                <p class="text-xs text-gray-300 mt-2">Mulai jelajahi koleksi kami</p>
                <a href="{{ route('public.store.catalog', $config->slug) }}" class="btn-outline mt-8" style="font-size:11px;">Lihat Katalog</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($cartPanelItems as $item)
                    <div class="flex gap-4 pb-4 border-b border-black/5">
                        <div class="w-16 h-16 bg-stone-50 flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($item['product']->image_path)
                                <img src="{{ asset('storage/' . $item['product']->image_path) }}" alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $item['product']->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Rp {{ number_format($item['unit_price'], 0, ',', '.') }} / pcs</p>

                            <form method="POST" action="{{ route('public.store.cart.update', [$config->slug, $item['product']->id]) }}" class="flex items-center gap-2 mt-3">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="quantity" value="{{ max($item['qty'] - 1, 1) }}" class="qty-btn" {{ $item['qty'] <= 1 ? 'disabled' : '' }}>−</button>
                                <span class="text-sm font-medium w-6 text-center">{{ $item['qty'] }}</span>
                                <button type="submit" name="quantity" value="{{ $item['qty'] + 1 }}" class="qty-btn">+</button>
                            </form>
                        </div>
                        <div class="text-right flex flex-col justify-between">
                            <form method="POST" action="{{ route('public.store.cart.remove', [$config->slug, $item['product']->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-black transition-colors text-xs" aria-label="Hapus">✕</button>
                            </form>
                            <span class="font-display text-sm font-semibold whitespace-nowrap">
                                Rp {{ number_format($item['line_total'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if(!empty($cartPanelItems))
    <div class="border-t border-black/8 px-6 py-6">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs tracking-widest uppercase font-medium text-gray-500">Subtotal</span>
            <span class="font-display text-2xl font-semibold">Rp {{ number_format($cartPanelTotals['subtotal'], 0, ',', '.') }}</span>
        </div>
        <p class="text-xs text-gray-400 mb-5">Pajak & ongkir dihitung saat checkout</p>
        <a href="{{ route('public.store.checkout', $config->slug) }}" class="btn-dark w-full" style="display:block;">Lanjut Checkout</a>
        <a href="{{ route('public.store.catalog', $config->slug) }}" class="btn-outline w-full mt-3" style="display:block;">Lanjut Belanja</a>
    </div>
    @endif
</aside>

{{-- ═══════════════════════════════════════════════
     FLASH MESSAGES (auto-toast)
═══════════════════════════════════════════════ --}}
@if(session('success'))
    <script>window.__flash = { msg: @json(session('success')) };</script>
@endif
@if(session('error'))
    <script>window.__flashError = { msg: @json(session('error')) };</script>
@endif
@if($errors->any())
    <div class="max-w-7xl mx-auto px-6 mt-24">
        <div class="border border-black p-4 bg-white text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════ --}}
<main class="pt-20">
    {{ $slot }}
</main>

{{-- ═══════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════ --}}
<footer class="bg-black text-white mt-24">
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid md:grid-cols-4 gap-10 mb-12">
            <div class="md:col-span-1">
                <div class="font-display text-2xl tracking-widest mb-4">{{ strtoupper($config->storefront_title) }}</div>
                @if($config->tagline)
                    <p class="text-xs leading-relaxed font-light text-white/35">{{ $config->tagline }}</p>
                @endif
            </div>

            <div>
                <div class="text-xs tracking-widest uppercase font-medium mb-5 text-white/40">Belanja</div>
                <div class="space-y-3">
                    <a href="{{ route('public.store.catalog', $config->slug) }}" class="block text-sm font-light text-white/60 hover:opacity-80">Semua Produk</a>
                    <a href="{{ route('public.store.show', $config->slug) }}#products" class="block text-sm font-light text-white/60 hover:opacity-80">Pilihan Kami</a>
                    <a href="{{ route('public.store.cart', $config->slug) }}" class="block text-sm font-light text-white/60 hover:opacity-80">Keranjang</a>
                    <a href="{{ route('public.store.my-orders', $config->slug) }}" class="block text-sm font-light text-white/60 hover:opacity-80">Pesanan Saya</a>
                </div>
            </div>

            <div>
                <div class="text-xs tracking-widest uppercase font-medium mb-5 text-white/40">Toko</div>
                <div class="space-y-3">
                    <a href="{{ route('public.store.show', $config->slug) }}#about" class="block text-sm font-light text-white/60 hover:opacity-80">Tentang Kami</a>
                    <a href="{{ route('public.store.show', $config->slug) }}#reviews" class="block text-sm font-light text-white/60 hover:opacity-80">Review</a>
                    <a href="{{ route('public.store.show', $config->slug) }}#contact" class="block text-sm font-light text-white/60 hover:opacity-80">Kontak</a>
                    <a href="{{ route('public.store.review-form', $config->slug) }}" class="block text-sm font-light text-white/60 hover:opacity-80">Tulis Review</a>
                </div>
            </div>

            <div>
                <div class="text-xs tracking-widest uppercase font-medium mb-5 text-white/40">Hubungi</div>
                @if($config->footer_content)
                    <div class="text-sm font-light text-white/60 space-y-1 mb-4">{!! nl2br(e($config->footer_content)) !!}</div>
                @endif
                @if(!empty($socialLinks))
                    <div class="flex gap-4">
                        @foreach($socialLinks as $platform => $url)
                            @if($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-xs tracking-widest uppercase font-medium text-white/40 hover:opacity-80 transition-opacity">
                                    {{ ucfirst($platform) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="border-t border-white/8 pt-8 flex flex-col md:flex-row justify-between gap-4">
            <p class="text-xs font-light text-white/30">© {{ date('Y') }} {{ $config->storefront_title }}. Powered by D-BOOST.</p>
            @if(isset($isPreview) && $isPreview)
                <p class="text-xs font-medium text-yellow-400/80 tracking-widest uppercase">⚠ Mode Preview — Belum Dipublikasi</p>
            @endif
        </div>
    </div>
</footer>

{{-- ═══════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════ --}}
<script>
    function openCartPanel() {
        document.getElementById('cart-panel').classList.add('open');
        document.getElementById('cart-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeCartPanel() {
        document.getElementById('cart-panel').classList.remove('open');
        document.getElementById('cart-overlay').classList.remove('show');
        document.body.style.overflow = '';
    }
    function showToast(msg) {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.classList.add('show');
        clearTimeout(window.__toastTimer);
        window.__toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
    }
    function openMobileMenu() {
        document.getElementById('mobile-menu').classList.add('open');
        document.getElementById('cart-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileMenu() {
        document.getElementById('mobile-menu').classList.remove('open');
        document.getElementById('cart-overlay').classList.remove('show');
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.__flash && window.__flash.msg) showToast(window.__flash.msg);
        if (window.__flashError && window.__flashError.msg) showToast(window.__flashError.msg);

        // Fade-in observer
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));

        // ── DYNAMIC NAV ──
        const nav = document.getElementById('nav-root');
        if (nav) {
            const mode = nav.dataset.mode || 'auto';
            const onScroll = () => {
                if (mode === 'solid') { nav.dataset.state = 'scrolled'; return; }
                nav.dataset.state = window.scrollY > 60 ? 'scrolled' : 'hero';
            };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        }

        // Active link
        const path = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(a => {
            const href = a.getAttribute('href');
            if (href && href !== '#' && !href.startsWith('http') && href.split('#')[0] === path) {
                a.classList.add('active');
            }
        });

        // Auto-close mobile menu on link click
        document.querySelectorAll('.mobile-menu a').forEach(a => {
            a.addEventListener('click', closeMobileMenu);
        });

        // Click overlay closes both panels
        const overlay = document.getElementById('cart-overlay');
        if (overlay) overlay.addEventListener('click', () => { closeCartPanel(); closeMobileMenu(); });

        // ESC closes panels
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeCartPanel(); closeMobileMenu(); } });
    });
</script>
@stack('scripts')
</body>
</html>
