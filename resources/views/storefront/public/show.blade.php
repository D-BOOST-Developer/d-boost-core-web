<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config->storefront_title }} - Digital Storefront</title>
    <meta name="description" content="{{ $config->meta_description ?? $config->tagline }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/D-BOOST-removebg.png') }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $config->storefront_title }}">
    <meta property="og:description" content="{{ $config->tagline }}">
    <meta property="og:type" content="website">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        :root {
            --primary-color: {{ $config->primary_color }};
            --secondary-color: {{ $config->secondary_color ?? '#10B981' }};
            --text-color: {{ $config->text_color ?? '#1F2937' }};
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Animated Gradient Background */
        .gradient-bg {
            background: linear-gradient(-45deg, {{ $config->primary_color }}, {{ $config->secondary_color ?? '#10B981' }}, {{ $config->primary_color }}dd, {{ $config->secondary_color ?? '#10B981' }}dd);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Fade In Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.5s ease-out forwards;
        }
        
        /* Stagger animation delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        
        /* Card Hover Effects */
        .product-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .product-card:hover .product-image {
            transform: scale(1.1);
        }
        
        .product-image {
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Gentle pulse animation */
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.95; }
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Fade in up - more subtle */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* CTA Content Animation */
        .cta-content {
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
        }
        
        .cta-content h2 {
            animation: fadeInUp 0.8s ease-out 0.3s forwards;
        }
        
        .cta-content p {
            animation: fadeInUp 0.8s ease-out 0.4s forwards;
        }
        
        .cta-content a {
            animation: fadeInUp 0.8s ease-out 0.5s forwards;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Header Section with Animated Gradient -->
    <header class="relative overflow-hidden gradient-bg min-h-[500px] flex items-center">
        <!-- Decorative circles -->
        <div class="absolute top-10 right-10 w-64 h-64 bg-white/10 rounded-full blur-3xl floating"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-white/10 rounded-full blur-3xl floating" style="animation-delay: 1.5s;"></div>
        
        <div class="relative container mx-auto px-4 py-20 text-center z-10">
            @if($config->hasLogo())
                <img src="{{ $config->logo_url }}" alt="{{ $config->storefront_title }}" 
                     class="mx-auto h-32 w-auto mb-8 drop-shadow-2xl animate-scale-in">
            @endif
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 drop-shadow-lg animate-fade-in-up">
                {{ $config->storefront_title }}
            </h1>
            
            @if($config->tagline)
                <p class="text-xl md:text-2xl text-white/95 max-w-3xl mx-auto drop-shadow-md mb-8 animate-fade-in-up delay-100">
                    {{ $config->tagline }}
                </p>
            @endif
            
            @php
                $textBlocks = is_string($config->text_blocks) ? json_decode($config->text_blocks, true) : $config->text_blocks;
                $headerText = $textBlocks['header_text'] ?? '';
            @endphp
            
            @if($headerText)
                <p class="text-lg text-white/90 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200">
                    {{ $headerText }}
                </p>
            @endif
            
            <div class="mt-10 animate-fade-in-up delay-300">
                <a href="#products" class="inline-block px-8 py-4 bg-white text-gray-900 font-bold rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    Lihat Produk Kami
                    <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Wave divider -->
        <div class="absolute bottom-0 left-0 w-full">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="#F9FAFB"/>
            </svg>
        </div>
    </header>

    <!-- About Section -->
    @php
        $aboutText = $textBlocks['about_text'] ?? '';
    @endphp
    
    @if($aboutText)
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">Tentang Kami</h2>
                <p class="text-lg text-gray-700 leading-relaxed">
                    {{ $aboutText }}
                </p>
            </div>
        </div>
    </section>
    @endif

    <!-- Products Grid Section -->
    <section id="products" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Produk Kami
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Koleksi lengkap produk berkualitas pilihan terbaik untuk Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-{{ $config->grid_columns ?? 3 }} gap-8 max-w-7xl mx-auto">
                @foreach($links as $index => $link)
                    <div class="product-card bg-white rounded-2xl overflow-hidden @if($config->card_style === 'shadow') shadow-xl @elseif($config->card_style === 'bordered') border-2 border-gray-200 @endif opacity-0 animate-fade-in-up delay-{{ min($index * 100, 400) }}"
                         onclick="trackAndRedirect('{{ $link->id }}', '{{ $link->url }}')">
                        
                        <!-- Image -->
                        <div class="relative w-full aspect-square bg-gradient-to-br from-gray-100 to-gray-50 overflow-hidden group">
                            @if($link->hasImage())
                                <img src="{{ $link->image_url }}" 
                                     alt="{{ $link->display_name }}" 
                                     class="product-image w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <span class="text-7xl">{{ $link->platform_icon }}</span>
                                </div>
                            @endif
                            
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="text-white font-bold text-lg">Lihat Detail →</div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-3 line-clamp-2 text-gray-900">
                                {{ $link->display_name }}
                            </h3>

                            @if($link->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                    {{ $link->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mb-4">
                                @if($link->price)
                                    <p class="font-bold text-2xl" style="color: var(--primary-color)">
                                        {{ $link->formatted_price }}
                                    </p>
                                @else
                                    <span></span>
                                @endif

                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    {{ ucfirst($link->platform) }}
                                </span>
                            </div>

                            <button class="w-full py-3 px-4 rounded-xl text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" 
                                    style="background: var(--primary-color)">
                                Beli Sekarang
                                <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($links->isEmpty())
                <div class="text-center py-20">
                    <div class="animate-pulse">
                        <svg class="w-32 h-32 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-400 mb-3">Segera Hadir</h3>
                    <p class="text-gray-500 text-lg">Produk-produk menarik akan segera ditambahkan</p>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    @php
        $ctaText = $textBlocks['cta_text'] ?? '';
    @endphp
    
    @if($ctaText)
    <section class="py-20 relative overflow-hidden cta-section" style="background: linear-gradient(135deg, {{ $config->primary_color }}, {{ $config->secondary_color ?? '#10B981' }})">
        <!-- Subtle overlay with gentle pulse -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/5 to-transparent animate-pulse-slow"></div>
        
        <!-- Decorative blur circles -->
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl mx-auto text-center text-white opacity-0 cta-content">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 transform translate-y-4">
                    {{ $ctaText }}
                </h2>
                <p class="text-xl mb-8 text-white/90 transform translate-y-4">
                    Jangan lewatkan penawaran terbaik kami!
                </p>
                <a href="#products" class="inline-block px-10 py-4 bg-white text-gray-900 font-bold rounded-full shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 ease-out">
                    Mulai Belanja
                    <svg class="inline-block w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">{{ $config->storefront_title }}</h3>
                        @if($config->tagline)
                            <p class="text-gray-400">{{ $config->tagline }}</p>
                        @endif
                    </div>
                    
                    @if($config->footer_content)
                    <div>
                        <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                        <div class="text-gray-400 space-y-2">
                            {!! nl2br(e($config->footer_content)) !!}
                        </div>
                    </div>
                    @endif
                    
                    @if($config->social_links)
                    <div>
                        <h4 class="font-semibold text-lg mb-4">Follow Us</h4>
                        <div class="flex gap-4">
                            @foreach($config->social_links as $platform => $link)
                                @if($link)
                                    <a href="{{ $link }}" target="_blank" rel="noopener" 
                                       class="w-12 h-12 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center transition-all hover:scale-110">
                                        <span class="text-xl">
                                            @if($platform === 'instagram') 📷
                                            @elseif($platform === 'facebook') 📘
                                            @elseif($platform === 'whatsapp') 💬
                                            @else 🔗
                                            @endif
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-800 pt-8 text-center text-gray-500">
                    <p>© {{ date('Y') }} {{ $config->storefront_title }}. All rights reserved.</p>
                    @if(isset($isPreview) && $isPreview)
                        <p class="text-yellow-400 mt-2 font-semibold">⚠️ MODE PREVIEW - Storefront belum dipublikasi</p>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTop" class="fixed bottom-8 right-8 w-14 h-14 bg-gray-900 hover:bg-gray-800 text-white rounded-full shadow-2xl opacity-0 pointer-events-none transition-all duration-300 hover:scale-110 z-50">
        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <!-- Click Tracking & Smooth Scroll Script -->
    <script>
        // Track and redirect
        function trackAndRedirect(linkId, url) {
            @if(!isset($isPreview) || !$isPreview)
            fetch(`/store/{{ $config->slug }}/track/${linkId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(err => console.log('Track error:', err));
            @endif

            window.open(url, '_blank');
        }
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                scrollTopBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        });
        
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.animate-fade-in-up, .product-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
