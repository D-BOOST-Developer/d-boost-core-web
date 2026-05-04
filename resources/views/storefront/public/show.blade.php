@php
    $textBlocks  = is_string($config->text_blocks) ? json_decode($config->text_blocks, true) : ($config->text_blocks ?? []);
    $socialLinks = is_string($config->social_links) ? json_decode($config->social_links, true) : ($config->social_links ?? []);
    $headerText  = $textBlocks['header_text'] ?? '';
    $aboutText   = $textBlocks['about_text']  ?? '';
    $ctaText     = $textBlocks['cta_text']    ?? '';
    $isPreview   = $isPreview ?? false;

    $marqueeWords = $headerText
        ? collect(preg_split('/[•·\|,]+/', $headerText))->map(fn($w) => trim($w))->filter()->values()->all()
        : ['Pengiriman Gratis', 'Kualitas Terkurasi', 'Garansi 30 Hari', 'Pembayaran Aman', 'Customer Care 24/7'];
    if (count($marqueeWords) < 3) {
        $marqueeWords = array_merge($marqueeWords, ['Kualitas Terkurasi', 'Pembayaran Aman', 'Pengiriman Cepat']);
    }

    $featured = $featuredProducts ?? collect();
    $categoriesList = $featured->pluck('category')->filter()->unique()->take(4)->values();
    if ($categoriesList->isEmpty()) {
        $categoriesList = collect(['Pilihan', 'Terbaru', 'Favorit', 'Eksklusif']);
    }

    $businessYears = optional($businessProfile ?? null)->business_age_years ?? null;
    $productCount  = $featured->count();

    // Hero accents
    $heroProductImg = $featured->first(fn($p) => !empty($p->image_path))->image_path ?? null;
    $heroBusinessLogo = optional($businessProfile ?? null)->business_logo_url;

    // Grid columns from config — clamp to sensible range, derive responsive steps
    $gridLg = (int) ($config->grid_columns ?? 4);
    $gridLg = max(2, min(5, $gridLg));
    $gridMd = max(2, min(3, $gridLg));
    $gridStyle = "--grid-md: {$gridMd}; --grid-lg: {$gridLg};";

    // Maps embed
    $mapsEmbedUrl = optional($businessProfile ?? null)->google_maps_embed_url;
    $businessAddress = optional($businessProfile ?? null)->business_address;
    $businessName = optional($businessProfile ?? null)->business_name ?? $config->storefront_title;
    $contactPhone = optional($businessProfile ?? null)->contact_phone;
    $contactEmail = optional($businessProfile ?? null)->contact_email;
    $googleMapsLink = optional($businessProfile ?? null)->google_maps_link;
@endphp

<x-storefront.public-layout :config="$config" pageTitle="Beranda">

    @if($isPreview)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-900 px-4 py-3 text-sm">
                <strong class="font-semibold">Mode Preview</strong> — Storefront ini belum dipublikasikan.
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════
         HERO — full-viewport, editorial split layout
    ═══════════════════════════════════════════════════════ --}}
    <section class="relative bg-[var(--ink)] text-[var(--paper)] overflow-hidden -mt-20 pt-20 min-h-[95vh] flex items-center">
        {{-- Decorative concentric circles --}}
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[600px] h-[600px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute top-20 left-20 w-[400px] h-[400px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute -bottom-40 -right-40 w-[700px] h-[700px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute bottom-10 right-32 w-[280px] h-[280px] rounded-full border border-[var(--paper)]"></div>
        </div>

        {{-- Top-right accent label --}}
        <div class="absolute top-28 right-8 hidden md:flex items-center gap-3 z-10">
            <span class="text-[10px] uppercase tracking-[0.32em] text-[var(--paper)]/50">Est. {{ $businessYears ? (date('Y') - (int) round($businessYears)) : date('Y') }}</span>
            <span class="font-serif text-2xl text-[var(--paper)]/40">○</span>
        </div>

        {{-- Bottom-left accent label --}}
        <div class="absolute bottom-10 left-8 hidden md:block z-10">
            <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--paper)]/40 leading-relaxed">
                Marketplace<br>Modern · Indonesia
            </p>
        </div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                {{-- Left: copy --}}
                <div class="lg:col-span-7 text-center lg:text-left">
                    @if($config->hasLogo())
                        <img src="{{ $config->logo_url }}" alt="{{ $config->storefront_title }}" class="h-16 w-auto mb-8 mx-auto lg:mx-0 opacity-95">
                    @endif

                    <p class="text-[10px] md:text-xs uppercase tracking-[0.5em] text-[var(--paper)]/50 mb-6">
                        ○ &nbsp; {{ $config->tagline ?: 'Marketplace Modern' }} &nbsp; ○
                    </p>

                    <h1 class="font-serif text-5xl sm:text-6xl md:text-7xl lg:text-[5.5rem] xl:text-[6.5rem] leading-[0.98] mb-8 tracking-tight">
                        {{ $config->storefront_title }}<span class="text-[var(--paper)]/40">.</span>
                        <span class="block italic font-normal text-[var(--paper)]/55 text-3xl sm:text-4xl md:text-5xl lg:text-6xl mt-3">
                            untuk <em>setiap</em> momen.
                        </span>
                    </h1>

                    <p class="max-w-xl mx-auto lg:mx-0 text-base md:text-lg text-[var(--paper)]/70 leading-relaxed mb-10">
                        {{ $headerText ?: 'Koleksi produk pilihan, terkurasi dengan cermat untuk kebutuhan harian Anda. Sederhana, elegan, terpercaya.' }}
                    </p>

                    <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                        <a href="{{ route('public.store.catalog', $config->slug) }}"
                           class="inline-flex items-center gap-3 bg-[var(--paper)] text-[var(--ink)] text-xs uppercase tracking-[0.24em] font-medium px-8 py-4 hover:bg-white transition-colors group">
                            Lihat Katalog
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </a>
                        <a href="#featured"
                           class="inline-flex items-center gap-3 border border-[var(--paper)]/40 text-[var(--paper)] text-xs uppercase tracking-[0.24em] font-medium px-8 py-4 hover:bg-[var(--paper)]/10 hover:border-[var(--paper)] transition-colors">
                            Jelajahi Koleksi
                        </a>
                    </div>

                    {{-- Mini stats strip --}}
                    <div class="mt-14 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0 pt-8 border-t border-[var(--paper)]/15">
                        <div class="text-center lg:text-left">
                            <p class="font-serif text-2xl md:text-3xl text-[var(--paper)]">12K+</p>
                            <p class="text-[9px] uppercase tracking-[0.24em] text-[var(--paper)]/45 mt-1">Pelanggan</p>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="font-serif text-2xl md:text-3xl text-[var(--paper)]">{{ max($productCount, 50) }}+</p>
                            <p class="text-[9px] uppercase tracking-[0.24em] text-[var(--paper)]/45 mt-1">Produk</p>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="font-serif text-2xl md:text-3xl text-[var(--paper)]">98%</p>
                            <p class="text-[9px] uppercase tracking-[0.24em] text-[var(--paper)]/45 mt-1">Puas</p>
                        </div>
                    </div>
                </div>

                {{-- Right: visual frame --}}
                <div class="lg:col-span-5 hidden lg:block">
                    <div class="relative">
                        {{-- Main visual --}}
                        <div class="relative aspect-[4/5] bg-[var(--paper)]/5 border border-[var(--paper)]/15 overflow-hidden">
                            @if($heroProductImg)
                                <img src="{{ asset('storage/' . $heroProductImg) }}" alt="{{ $config->storefront_title }}"
                                     class="w-full h-full object-cover opacity-90">
                            @elseif($heroBusinessLogo)
                                <div class="w-full h-full flex items-center justify-center p-12">
                                    <img src="{{ $heroBusinessLogo }}" alt="{{ $businessName }}" class="max-w-full max-h-full object-contain opacity-80">
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="font-serif text-[12rem] text-[var(--paper)]/15 leading-none">○</span>
                                </div>
                            @endif

                            {{-- Overlay corner badge --}}
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="text-[10px] uppercase tracking-[0.24em] text-[var(--paper)]/80 bg-[var(--ink)]/60 px-2 py-1 backdrop-blur-sm">Live Now</span>
                            </div>

                            {{-- Bottom plate --}}
                            <div class="absolute bottom-0 inset-x-0 px-5 py-4 bg-gradient-to-t from-[var(--ink)] to-transparent">
                                <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--paper)]/60">Koleksi</p>
                                <p class="font-serif text-lg text-[var(--paper)]">{{ $featured->first()->name ?? $config->storefront_title }}</p>
                            </div>
                        </div>

                        {{-- Floating accent card --}}
                        <div class="absolute -bottom-8 -right-4 bg-[var(--paper)] text-[var(--ink)] px-6 py-5 max-w-[200px] shadow-2xl">
                            <p class="text-[9px] uppercase tracking-[0.32em] text-[var(--mid)] mb-2">★★★★★</p>
                            <p class="font-serif text-sm leading-snug italic">"Produk terkurasi dengan cermat."</p>
                            <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] mt-2">— Pelanggan</p>
                        </div>

                        {{-- Floating accent number --}}
                        <div class="absolute -top-6 -right-4 bg-[var(--ink)] border border-[var(--paper)]/30 text-[var(--paper)] px-5 py-4 text-center">
                            <p class="font-serif text-3xl">{{ $businessYears ?: 5 }}+</p>
                            <p class="text-[9px] uppercase tracking-[0.24em] text-[var(--paper)]/60 mt-1">Tahun</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <a href="#featured" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 z-10 hover:opacity-70 transition-opacity">
            <span class="text-[9px] uppercase tracking-[0.32em] text-[var(--paper)]/40">Gulir</span>
            <span class="w-px h-8 bg-[var(--paper)]/40 animate-pulse"></span>
        </a>
    </section>

    {{-- ════════════════════════════════════════════════════
         MARQUEE — full-bleed
    ═══════════════════════════════════════════════════════ --}}
    <section class="bg-[var(--ink)] text-[var(--paper)] py-5 overflow-hidden border-t border-[var(--paper)]/10">
        <div class="flex items-center gap-12 whitespace-nowrap animate-[marquee_28s_linear_infinite] text-xs uppercase tracking-[0.32em]">
            @for($i = 0; $i < 3; $i++)
                @foreach($marqueeWords as $w)
                    <span>{{ $w }}</span>
                    <span class="text-[var(--mid)]">○</span>
                @endforeach
            @endfor
        </div>
        <style>
            @keyframes marquee {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
        </style>
    </section>

    {{-- ════════════════════════════════════════════════════
         CONTAINED CONTENT — uniform horizontal padding
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ════════════════════════════════════════════════════
             FEATURED PRODUCTS
        ═══════════════════════════════════════════════════════ --}}
        <section id="featured" class="py-20 md:py-24">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Koleksi Pilihan</p>
                    <h2 class="font-serif text-4xl md:text-5xl text-[var(--ink)]">Pilihan <em>Kami</em></h2>
                </div>
                <a href="{{ route('public.store.catalog', $config->slug) }}"
                   class="text-xs uppercase tracking-[0.2em] text-[var(--ink)] border-b border-[var(--ink)] pb-1 hover:text-[var(--mid)] hover:border-[var(--mid)] transition-colors">
                    Lihat Semua →
                </a>
            </div>

            @if($featured->isNotEmpty())
                <div class="config-grid" style="{{ $gridStyle }}">
                    @foreach($featured as $product)
                        @include('storefront.public._storefront_card', [
                            'item' => $product, 'type' => 'product',
                            'config' => $config, 'isPreview' => $isPreview,
                        ])
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-[var(--light)] py-20 text-center">
                    <span class="font-serif text-5xl text-[var(--mid)]">○</span>
                    <h3 class="font-serif text-2xl text-[var(--ink)] mt-4">Segera Hadir</h3>
                    <p class="text-sm text-[var(--mid)] mt-2">Produk-produk pilihan akan segera ditambahkan.</p>
                </div>
            @endif
        </section>

        {{-- ════════════════════════════════════════════════════
             CATEGORIES
        ═══════════════════════════════════════════════════════ --}}
        <section class="py-20 md:py-24">
            <div class="text-center mb-12">
                <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Telusuri</p>
                <h2 class="font-serif text-4xl md:text-5xl text-[var(--ink)]">Berdasarkan <em>Kategori</em></h2>
            </div>

            <div class="flex flex-wrap justify-center gap-4 max-w-[var(--layout-width)] mx-auto px-4">
                @foreach($categoriesList as $cat)
                    <a href="{{ route('public.store.catalog', $config->slug) }}?category={{ urlencode($cat) }}"
                       class="w-[calc(50%-0.5rem)] md:w-[calc(25%-0.75rem)] max-w-[280px] group relative aspect-[4/5] bg-[var(--ink)] text-[var(--paper)] flex items-center justify-center overflow-hidden">
                        <span class="absolute inset-0 bg-gradient-to-b from-transparent to-black/40 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                        <div class="relative text-center px-4">
                            <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-2">Kategori</p>
                            <p class="font-serif text-2xl">{{ $cat }}</p>
                            <span class="block mt-3 text-xs uppercase tracking-[0.2em] opacity-0 group-hover:opacity-100 transition-opacity">Telusuri →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ════════════════════════════════════════════════════
             MARKETPLACE LINKS
        ═══════════════════════════════════════════════════════ --}}
        @if(($links ?? collect())->isNotEmpty())
            <section class="py-20 md:py-24">
                <div class="text-center mb-12">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Tersedia juga di</p>
                    <h2 class="font-serif text-4xl md:text-5xl text-[var(--ink)]">Marketplace <em>Resmi</em></h2>
                </div>

                <div class="config-grid" style="{{ $gridStyle }}">
                    @foreach($links as $link)
                        @include('storefront.public._storefront_card', [
                            'item' => $link, 'type' => 'link',
                            'config' => $config, 'isPreview' => $isPreview,
                        ])
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ════════════════════════════════════════════════════
             ABOUT — split layout
        ═══════════════════════════════════════════════════════ --}}
        <section id="about" class="py-20 md:py-24 grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <div>
                <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Tentang Kami</p>
                <h2 class="font-serif text-4xl md:text-5xl text-[var(--ink)] mb-6">
                    Kisah di <em>balik</em> setiap produk.
                </h2>
                <p class="text-[var(--mid)] leading-relaxed mb-6">
                    {{ $aboutText ?: 'Kami percaya bahwa kualitas dan kesederhanaan tidak harus saling meniadakan. Setiap produk dipilih dengan cermat — dari pengrajin lokal hingga merek terpercaya — untuk menghadirkan pengalaman belanja yang bermakna.' }}
                </p>
                <a href="{{ route('public.store.catalog', $config->slug) }}"
                   class="inline-block text-xs uppercase tracking-[0.2em] text-[var(--ink)] border-b border-[var(--ink)] pb-1 hover:text-[var(--mid)] hover:border-[var(--mid)] transition-colors">
                    Mulai Belanja →
                </a>
            </div>

            <div class="grid grid-cols-2 gap-px bg-[var(--light)] border border-[var(--light)]">
                <div class="bg-[var(--paper)] p-8 text-center">
                    <p class="font-serif text-4xl text-[var(--ink)]">12K+</p>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mt-2">Pelanggan</p>
                </div>
                <div class="bg-[var(--paper)] p-8 text-center">
                    <p class="font-serif text-4xl text-[var(--ink)]">{{ max($productCount, 50) }}+</p>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mt-2">Produk Pilihan</p>
                </div>
                <div class="bg-[var(--paper)] p-8 text-center">
                    <p class="font-serif text-4xl text-[var(--ink)]">98%</p>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mt-2">Kepuasan</p>
                </div>
                <div class="bg-[var(--paper)] p-8 text-center">
                    <p class="font-serif text-4xl text-[var(--ink)]">{{ $businessYears ?: 5 }}+</p>
                    <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mt-2">Tahun Berkarya</p>
                </div>
            </div>
        </section>

        {{-- ════════════════════════════════════════════════════
             REVIEWS
        ═══════════════════════════════════════════════════════ --}}
        @if(($reviews ?? collect())->isNotEmpty())
            <section id="reviews" class="py-20 md:py-24">
                <div class="text-center mb-12">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Apa Kata Mereka</p>
                    <h2 class="font-serif text-4xl md:text-5xl text-[var(--ink)]">
                        <em>Cerita</em> dari Pelanggan
                    </h2>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($reviews->take(6) as $review)
                        @php
                            $name = $review->display_name ?? $review->client_name ?? 'Pelanggan';
                            $initials = collect(explode(' ', $name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->implode('');
                            $loc = $review->client_email ? ('@' . substr(strrchr($review->client_email, "@") ?: '', 1)) : 'Pembeli Terverifikasi';
                        @endphp
                        <div class="bg-white border border-[var(--light)] p-6 fade-in">
                            <div class="flex items-center gap-1 mb-4 text-[var(--ink)]">
                                @for($i = 0; $i < 5; $i++)
                                    <span class="{{ $i < (int) $review->rating ? '' : 'text-[var(--light)]' }}">★</span>
                                @endfor
                            </div>
                            <p class="font-serif italic text-[var(--ink)] leading-relaxed mb-6">
                                "{{ $review->review_text ?? 'Pelayanan dan produk sangat memuaskan.' }}"
                            </p>
                            <div class="flex items-center gap-3 pt-4 border-t border-[var(--light)]">
                                <div class="w-10 h-10 rounded-full bg-[var(--ink)] text-[var(--paper)] flex items-center justify-center text-xs font-medium">
                                    {{ $initials ?: '··' }}
                                </div>
                                <div>
                                    <p class="text-sm text-[var(--ink)] font-medium">{{ $name }}</p>
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)]">{{ $loc }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-10">
                    <a href="{{ route('public.store.review-form', $config->slug) }}"
                       class="inline-block text-xs uppercase tracking-[0.2em] text-[var(--ink)] border-b border-[var(--ink)] pb-1 hover:text-[var(--mid)] hover:border-[var(--mid)] transition-colors">
                        Tulis Ulasan Anda →
                    </a>
                </div>
            </section>
        @endif

    </div>
    {{-- end contained content --}}

    {{-- ════════════════════════════════════════════════════
         CTA BANNER (newsletter) — full-bleed
    ═══════════════════════════════════════════════════════ --}}
    <section class="bg-[var(--ink)] text-[var(--paper)]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Newsletter</p>
            <h2 class="font-serif text-4xl md:text-5xl mb-4">
                {{ $ctaText ?: 'Koleksi baru setiap minggu.' }}
            </h2>
            <p class="text-[var(--paper)]/70 mb-8">
                Dapatkan informasi produk pilihan dan penawaran eksklusif langsung ke email Anda.
            </p>
            <form action="#" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                @csrf
                <input type="email" name="email" placeholder="email@anda.com" required
                       class="flex-1 px-4 py-3 bg-transparent border border-[var(--paper)]/30 text-[var(--paper)] placeholder-[var(--paper)]/50 focus:border-[var(--paper)] focus:outline-none text-sm">
                <button type="submit"
                        class="bg-[var(--paper)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] font-medium px-6 py-3 hover:bg-white transition-colors">
                    Berlangganan
                </button>
            </form>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════
         CONTACT + MAPS
    ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <section id="contact" class="py-20 md:py-24">
            <div class="text-center mb-12">
                <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Hubungi Kami</p>
                <h2 class="font-serif text-4xl md:text-5xl text-[var(--ink)]">
                    Mari <em>terhubung</em>.
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Visit --}}
                <div class="bg-white border border-[var(--light)] p-8">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Kunjungi</p>
                    <h3 class="font-serif text-xl text-[var(--ink)] mb-4">Lokasi Kami</h3>
                    <p class="text-sm text-[var(--mid)] leading-relaxed whitespace-pre-line">
                        {{ $businessAddress ?: ($config->footer_content ?: "Jl. Sudirman No. 1\nJakarta, Indonesia") }}
                    </p>
                </div>

                {{-- Contact info --}}
                <div class="bg-white border border-[var(--light)] p-8">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Kontak</p>
                    <h3 class="font-serif text-xl text-[var(--ink)] mb-4">Info Kontak</h3>
                    <div class="space-y-2.5 text-sm text-[var(--mid)]">
                        @if($contactPhone)
                            <a href="tel:{{ $contactPhone }}" class="flex items-center gap-2 hover:text-[var(--ink)] transition-colors">
                                <span class="text-[10px] uppercase tracking-[0.24em]">Telp</span>
                                <span class="text-[var(--ink)]">{{ $contactPhone }}</span>
                            </a>
                        @endif
                        @if($contactEmail)
                            <a href="mailto:{{ $contactEmail }}" class="flex items-center gap-2 hover:text-[var(--ink)] transition-colors">
                                <span class="text-[10px] uppercase tracking-[0.24em]">Email</span>
                                <span class="text-[var(--ink)] truncate">{{ $contactEmail }}</span>
                            </a>
                        @endif
                        @if(!empty($socialLinks['whatsapp']))
                            <a href="{{ $socialLinks['whatsapp'] }}" target="_blank" rel="noopener" class="block hover:text-[var(--ink)] transition-colors">WhatsApp →</a>
                        @endif
                        @if(!empty($socialLinks['instagram']))
                            <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener" class="block hover:text-[var(--ink)] transition-colors">Instagram →</a>
                        @endif
                        @if(!empty($socialLinks['facebook']))
                            <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener" class="block hover:text-[var(--ink)] transition-colors">Facebook →</a>
                        @endif
                        @if(!$contactPhone && !$contactEmail && empty(array_filter($socialLinks ?? [])))
                            <p>Hubungi kami melalui formulir di samping.</p>
                        @endif
                    </div>
                </div>

                {{-- Send message --}}
                <div class="bg-[var(--ink)] text-[var(--paper)] p-8">
                    <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Kirim Pesan</p>
                    <h3 class="font-serif text-xl mb-4">Pertanyaan?</h3>
                    <p class="text-sm text-[var(--paper)]/70 mb-4">
                        Tinggalkan ulasan atau pertanyaan untuk kami.
                    </p>
                    <a href="{{ route('public.store.review-form', $config->slug) }}"
                       class="inline-block border border-[var(--paper)]/40 px-5 py-2.5 text-xs uppercase tracking-[0.2em] hover:bg-[var(--paper)] hover:text-[var(--ink)] transition-colors">
                        Tulis Ulasan
                    </a>
                </div>
            </div>

            {{-- ─────────────────────────────────────────
                 MAPS PREVIEW
            ───────────────────────────────────────── --}}
            <div class="mt-10">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-2">Lokasi Toko</p>
                        <h3 class="font-serif text-2xl md:text-3xl text-[var(--ink)]">Temukan <em>Kami</em></h3>
                    </div>
                    @if($googleMapsLink)
                        <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener"
                           class="text-xs uppercase tracking-[0.2em] text-[var(--ink)] border-b border-[var(--ink)] pb-1 hover:text-[var(--mid)] hover:border-[var(--mid)] transition-colors self-start sm:self-auto">
                            Buka di Google Maps →
                        </a>
                    @endif
                </div>

                @if($mapsEmbedUrl)
                    <div class="relative bg-[var(--ink)] border border-[var(--light)] overflow-hidden">
                        <iframe
                            src="{{ $mapsEmbedUrl }}"
                            width="100%" height="420"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full block grayscale-[0.2]">
                        </iframe>

                        {{-- Floating address pill --}}
                        @if($businessAddress || $businessName)
                            <div class="absolute top-4 left-4 right-4 sm:right-auto sm:max-w-sm bg-[var(--paper)] border border-[var(--light)] p-4 shadow-xl">
                                <div class="flex items-start gap-3">
                                    <span class="font-serif text-3xl text-[var(--ink)] leading-none mt-1">○</span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-1">Alamat</p>
                                        <p class="font-serif text-base text-[var(--ink)] leading-snug">{{ $businessName }}</p>
                                        @if($businessAddress)
                                            <p class="text-xs text-[var(--mid)] mt-1 leading-relaxed">{{ $businessAddress }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white border border-[var(--light)] py-16 text-center">
                        <span class="font-serif text-5xl text-[var(--mid)]">○</span>
                        <h4 class="font-serif text-xl text-[var(--ink)] mt-3">Lokasi Belum Tersedia</h4>
                        <p class="text-sm text-[var(--mid)] mt-2">Informasi lokasi belum diatur oleh pemilik toko.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        function trackAndRedirect(linkId, url) {
            @if(!$isPreview)
            fetch(`/store/{{ $config->slug }}/track/${linkId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).catch(err => console.log('Track error:', err));
            @endif
            window.open(url, '_blank');
        }
    </script>
    @endpush

</x-storefront.public-layout>
