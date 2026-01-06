<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Onboarding - SDA UMKM</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #006BFF;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #0056cc;
        }
    </style>
</head>
<body class="antialiased overflow-hidden">
    <div x-data="onboardingForm()" x-init="init()" class="h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex flex-col overflow-hidden">
        
        <!-- Loading Screen Overlay -->
        <div x-show="showLoading" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50 z-50 flex items-center justify-center overflow-hidden">
            <div class="text-center max-w-md mx-auto px-6 relative">
                
                <!-- Loading State (fades out when complete) -->
                <div x-show="!loadingComplete"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <!-- Modern Animated Icon -->
                    <div class="mb-12 relative">
                        <!-- Outer glow ring -->
                        <div class="absolute inset-0 w-40 h-40 mx-auto">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-[#006BFF] via-blue-400 to-cyan-400 opacity-20 blur-2xl animate-pulse"></div>
                        </div>
                        
                        <!-- Main spinner container -->
                        <div class="w-40 h-40 mx-auto relative">
                            <!-- Rotating gradient ring -->
                            <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#006BFF] via-blue-400 to-cyan-400 animate-spin" style="animation-duration: 1.2s;"></div>
                            <div class="absolute inset-2 rounded-full bg-white shadow-xl"></div>
                            
                            <!-- Inner rotating ring -->
                            <div class="absolute inset-4 rounded-full bg-gradient-to-bl from-[#006BFF] via-purple-400 to-pink-400 animate-spin" style="animation-duration: 2s; animation-direction: reverse;"></div>
                            <div class="absolute inset-6 rounded-full bg-white"></div>
                            
                            <!-- Center icon with animation -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="relative">
                                    <!-- Pulsing background circles -->
                                    <div class="absolute inset-0 bg-[#006BFF]/10 rounded-full animate-ping" style="animation-duration: 1.5s;"></div>
                                    <div class="absolute inset-0 bg-[#006BFF]/5 rounded-full animate-ping" style="animation-duration: 2s; animation-delay: 0.3s;"></div>
                                    
                                    <!-- Icon -->
                                    <svg class="w-20 h-20 text-[#006BFF] relative z-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation-duration: 2s;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Text -->
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-[#006BFF] to-blue-600 bg-clip-text text-transparent mb-4">Menyiapkan Dashboard Anda</h2>
                    <p class="text-lg text-gray-600 mb-12">Mohon tunggu sebentar...</p>
                    
                    <!-- Progress Steps with Modern Animation -->
                    <div class="space-y-5">
                        <div class="flex items-center space-x-4 text-left transition-all duration-700 ease-out"
                             :class="loadingStep >= 1 ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 shadow-lg"
                                 :class="loadingStep >= 1 ? 'bg-gradient-to-br from-[#006BFF] to-blue-600 scale-100 shadow-blue-500/50' : 'bg-gray-200 scale-90'">
                                <svg x-show="loadingStep >= 1" 
                                     x-transition:enter="transition ease-out duration-400 delay-150"
                                     x-transition:enter-start="opacity-0 scale-50 rotate-180"
                                     x-transition:enter-end="opacity-100 scale-100 rotate-0"
                                     class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div x-show="loadingStep < 1" class="w-3 h-3 bg-gray-400 rounded-full animate-pulse"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-lg">Menyimpan Profil Bisnis</p>
                                <p class="text-sm text-gray-500">Informasi bisnis Anda sedang diproses</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 text-left transition-all duration-700 ease-out"
                             :class="loadingStep >= 2 ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 shadow-lg"
                                 :class="loadingStep >= 2 ? 'bg-gradient-to-br from-[#006BFF] to-blue-600 scale-100 shadow-blue-500/50' : 'bg-gray-200 scale-90'">
                                <svg x-show="loadingStep >= 2"
                                     x-transition:enter="transition ease-out duration-400 delay-150"
                                     x-transition:enter-start="opacity-0 scale-50 rotate-180"
                                     x-transition:enter-end="opacity-100 scale-100 rotate-0"
                                     class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div x-show="loadingStep < 2" class="w-3 h-3 bg-gray-400 rounded-full animate-pulse"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-lg">Menyesuaikan Fitur</p>
                                <p class="text-sm text-gray-500">Mengatur modul sesuai kebutuhan Anda</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 text-left transition-all duration-700 ease-out"
                             :class="loadingStep >= 3 ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 shadow-lg"
                                 :class="loadingStep >= 3 ? 'bg-gradient-to-br from-[#006BFF] to-blue-600 scale-100 shadow-blue-500/50' : 'bg-gray-200 scale-90'">
                                <svg x-show="loadingStep >= 3"
                                     x-transition:enter="transition ease-out duration-400 delay-150"
                                     x-transition:enter-start="opacity-0 scale-50 rotate-180"
                                     x-transition:enter-end="opacity-100 scale-100 rotate-0"
                                     class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div x-show="loadingStep < 3" class="w-3 h-3 bg-gray-400 rounded-full animate-pulse"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-lg">Mempersiapkan Dashboard</p>
                                <p class="text-sm text-gray-500">Hampir selesai...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success State (appears with smooth animation) -->
                <div x-show="loadingComplete" 
                     x-transition:enter="transition ease-out duration-700 delay-300"
                     x-transition:enter-start="opacity-0 scale-50"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="relative">
                    
                    <!-- Animated Confetti Background -->
                    <div class="absolute inset-0 pointer-events-none overflow-hidden">
                        <div class="absolute top-0 left-1/4 w-2 h-2 bg-green-400 rounded-full animate-ping" style="animation-duration: 2s; animation-delay: 0.1s;"></div>
                        <div class="absolute top-10 right-1/4 w-2 h-2 bg-emerald-400 rounded-full animate-ping" style="animation-duration: 2.5s; animation-delay: 0.3s;"></div>
                        <div class="absolute top-5 left-1/3 w-2 h-2 bg-green-500 rounded-full animate-ping" style="animation-duration: 2.2s; animation-delay: 0.5s;"></div>
                        <div class="absolute top-12 right-1/3 w-2 h-2 bg-emerald-500 rounded-full animate-ping" style="animation-duration: 2.8s; animation-delay: 0.2s;"></div>
                    </div>

                    <!-- Success Icon with Enhanced Animation -->
                    <div class="mb-8 relative">
                        <!-- Outer pulsing glow rings -->
                        <div class="absolute inset-0 w-40 h-40 mx-auto">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full opacity-30 blur-3xl animate-pulse" style="animation-duration: 2s;"></div>
                        </div>
                        <div class="absolute inset-0 w-40 h-40 mx-auto">
                            <div class="absolute inset-4 bg-gradient-to-r from-green-300 to-emerald-400 rounded-full opacity-20 blur-2xl animate-pulse" style="animation-duration: 1.5s; animation-delay: 0.3s;"></div>
                        </div>
                        
                        <!-- Main success circle with scale animation -->
                        <div class="relative w-40 h-40 mx-auto"
                             x-data="{ iconLoaded: false }"
                             x-init="setTimeout(() => iconLoaded = true, 100)">
                            
                            <!-- Rotating ring background -->
                            <div class="absolute inset-0 rounded-full bg-gradient-to-br from-green-400 via-emerald-500 to-green-600 shadow-2xl shadow-green-500/50"
                                 :class="iconLoaded ? 'scale-100 rotate-0' : 'scale-0 rotate-180'"
                                 style="transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                            
                            <!-- Inner white circle -->
                            <div class="absolute inset-3 rounded-full bg-white/10 backdrop-blur-sm"></div>
                            
                            <!-- Checkmark with draw animation -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white"
                                     :class="iconLoaded ? 'scale-100 rotate-0' : 'scale-0 rotate-180'"
                                     style="transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.4s;"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"
                                          style="stroke-dasharray: 50; stroke-dashoffset: 50; animation: drawCheck 0.8s ease-out 0.6s forwards;">
                                    </path>
                                </svg>
                            </div>
                            
                            <!-- Sparkle effects -->
                            <div class="absolute -top-2 -right-2 w-4 h-4 bg-yellow-300 rounded-full animate-ping" style="animation-duration: 1.5s; animation-delay: 0.8s;"></div>
                            <div class="absolute -bottom-2 -left-2 w-3 h-3 bg-yellow-400 rounded-full animate-ping" style="animation-duration: 1.8s; animation-delay: 1s;"></div>
                        </div>
                    </div>
                    
                    <!-- Success Text with Staggered Animation -->
                    <div x-data="{ textLoaded: false }"
                         x-init="setTimeout(() => textLoaded = true, 800)">
                        
                        <!-- Main heading -->
                        <h2 class="text-5xl font-bold mb-3 transition-all duration-700"
                            :class="textLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                            <span class="bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 bg-clip-text text-transparent">
                                Selamat!
                            </span>
                        </h2>
                        
                        <!-- Subtitle -->
                        <p class="text-xl text-gray-700 font-medium mb-2 transition-all duration-700 delay-100"
                           :class="textLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                            Dashboard Anda sudah siap!
                        </p>
                        
                        <!-- Additional message -->
                        <p class="text-base text-gray-500 transition-all duration-700 delay-200"
                           :class="textLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                            Mengarahkan ke halaman utama...
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- CSS Animation for Checkmark Draw -->
            <style>
                @keyframes drawCheck {
                    to {
                        stroke-dashoffset: 0;
                    }
                }
            </style>
        </div>

        <form @submit.prevent="submitForm" class="flex-1 flex flex-col h-full overflow-hidden">
            @csrf
            
            <!-- Main Content Area - Full Height -->
            <div class="flex-1 flex items-center justify-center px-6 py-6 overflow-hidden">
                <div class="w-full max-w-4xl h-full flex items-center justify-center">

                    <!-- Question 1: Business Name -->
                    <div x-show="currentQuestion === 1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Apa nama bisnis Anda?
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Mulai dengan memperkenalkan bisnis Anda kepada kami
                        </p>

                        <div class="max-w-xl mx-auto">
                            <input type="text" 
                                   x-model="formData.business_name"
                                   @keyup.enter="nextQuestion"
                                   class="w-full px-8 py-5 text-xl text-center bg-white/80 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#006BFF] focus:ring-4 focus:ring-[#006BFF]/10 transition-all outline-none placeholder-gray-400"
                                   placeholder="Contoh: Toko Berkah Jaya"
                                   required
                                   autofocus>
                            <p x-show="errors.business_name" class="mt-3 text-red-500" x-text="errors.business_name"></p>
                        </div>
                    </div>

                    <!-- Question 2: Business Category -->
                    <div x-show="currentQuestion === 2" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Kategori bisnis apa?
                        </h1>
                        <p class="text-lg text-gray-600 mb-6">
                            Pilih kategori yang paling sesuai dengan bisnis Anda
                        </p>

                        <!-- Scrollable Container for Categories -->
                        <div class="max-w-4xl mx-auto max-h-[calc(100vh-400px)] overflow-y-auto px-2 scrollbar-thin">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($businessCategories as $key => $value)
                                    <button type="button"
                                            @click="formData.business_category = '{{ $key }}'; nextQuestion()"
                                            class="p-5 bg-white/80 backdrop-blur-sm border-2 border-transparent rounded-2xl transition-all duration-200 hover:border-[#006BFF] hover:bg-white hover:shadow-xl hover:shadow-blue-500/20 hover:scale-105 group">
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">
                                                @switch($key)
                                                    @case('Retail') 🏪 @break
                                                    @case('F&B') 🍔 @break
                                                    @case('Fashion') 👔 @break
                                                    @case('Services') 🔧 @break
                                                    @case('Manufacturing') 🏭 @break
                                                    @case('Agriculture') 🌾 @break
                                                    @case('Technology') 💻 @break
                                                    @case('Healthcare') 🏥 @break
                                                    @case('Education') 📚 @break
                                                    @case('Beauty') 💄 @break
                                                    @case('Automotive') 🚗 @break
                                                    @case('Construction') 🏗️ @break
                                                    @case('Trading') 📦 @break
                                                    @default 🏢
                                                @endswitch
                                            </div>
                                            <p class="font-semibold text-gray-900 group-hover:text-[#006BFF] text-sm">{{ $value }}</p>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Question 3: Business Age -->
                    <div x-show="currentQuestion === 3" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Sudah berapa lama bisnis Anda berjalan?
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Masukkan usia bisnis dalam bulan
                        </p>

                        <div class="max-w-md mx-auto space-y-5">
                            <div class="relative">
                                <input type="number" 
                                       x-model.number="formData.business_age_month"
                                       @keyup.enter="nextQuestion"
                                       min="0"
                                       max="1200"
                                       class="w-full px-8 py-5 text-xl text-center bg-white/80 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#006BFF] focus:ring-4 focus:ring-[#006BFF]/10 transition-all outline-none"
                                       placeholder="12"
                                       required>
                                <div class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 text-lg">
                                    bulan
                                </div>
                            </div>
                            
                            <div class="text-base text-gray-500" x-show="formData.business_age_month">
                                <span x-text="Math.floor(formData.business_age_month / 12)"></span> tahun 
                                <span x-text="formData.business_age_month % 12"></span> bulan
                            </div>

                            <p x-show="errors.business_age_month" class="text-red-500" x-text="errors.business_age_month"></p>
                        </div>
                    </div>

                    <!-- Question 4: Monthly Revenue -->
                    <div x-show="currentQuestion === 4" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Berapa pendapatan bulanan Anda?
                        </h1>
                        <p class="text-lg text-gray-600 mb-6">
                            Pilih rentang pendapatan rata-rata per bulan
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-w-2xl mx-auto">
                            @foreach($revenueRanges as $key => $value)
                                <button type="button"
                                        @click="formData.monthly_revenue_range = '{{ $key }}'; nextQuestion()"
                                        class="p-5 bg-white/80 backdrop-blur-sm border-2 border-transparent rounded-2xl transition-all duration-200 hover:border-[#006BFF] hover:bg-white hover:shadow-xl hover:shadow-blue-500/20 hover:scale-105 text-left group">
                                    <div class="flex items-center justify-between">
                                        <span class="text-base font-semibold text-gray-900 group-hover:text-[#006BFF]">{{ $value }}</span>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Question 5: Owner Name -->
                    <div x-show="currentQuestion === 5" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Siapa nama pemilik bisnis?
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Nama lengkap pemilik atau penanggung jawab
                        </p>

                        <div class="max-w-xl mx-auto">
                            <input type="text" 
                                   x-model="formData.owner_name"
                                   @keyup.enter="nextQuestion"
                                   class="w-full px-8 py-5 text-xl text-center bg-white/80 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#006BFF] focus:ring-4 focus:ring-[#006BFF]/10 transition-all outline-none placeholder-gray-400"
                                   placeholder="Nama lengkap"
                                   required>
                            <p x-show="errors.owner_name" class="mt-3 text-red-500" x-text="errors.owner_name"></p>
                        </div>
                    </div>

                    <!-- Question 6: Contact Email -->
                    <div x-show="currentQuestion === 6" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Email kontak bisnis
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Untuk komunikasi dan notifikasi penting
                        </p>

                        <div class="max-w-xl mx-auto">
                            <input type="email" 
                                   x-model="formData.contact_email"
                                   @keyup.enter="nextQuestion"
                                   class="w-full px-8 py-5 text-xl text-center bg-white/80 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#006BFF] focus:ring-4 focus:ring-[#006BFF]/10 transition-all outline-none placeholder-gray-400"
                                   placeholder="email@bisnis.com"
                                   required>
                            <p x-show="errors.contact_email" class="mt-3 text-red-500" x-text="errors.contact_email"></p>
                        </div>
                    </div>

                    <!-- Question 7: Contact Phone -->
                    <div x-show="currentQuestion === 7" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Nomor telepon
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Opsional - untuk kemudahan komunikasi
                        </p>

                        <div class="max-w-xl mx-auto">
                            <input type="tel" 
                                   x-model="formData.contact_phone"
                                   @keyup.enter="nextQuestion"
                                   class="w-full px-8 py-5 text-xl text-center bg-white/80 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#006BFF] focus:ring-4 focus:ring-[#006BFF]/10 transition-all outline-none placeholder-gray-400"
                                   placeholder="08123456789">
                            <p class="mt-3 text-gray-500">Boleh dilewati jika tidak ingin mengisi</p>
                        </div>
                    </div>

                    <!-- Question 8: Feature Selection -->
                    <div x-show="currentQuestion === 8" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-center w-full">
                        
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#006BFF]/10 to-blue-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-3xl font-bold text-gray-900 mb-3">
                            Fitur apa yang Anda butuhkan?
                        </h1>
                        <p class="text-base text-gray-600 mb-5">
                            Pilih minimal 1 fitur yang paling penting untuk bisnis Anda
                        </p>


                        <!-- Compact Grid for Modules - Centered Layout -->
                        <div class="max-w-6xl mx-auto">
                            <div class="flex flex-wrap justify-center gap-2.5">
                                @foreach($modules as $module)
                                    <button type="button"
                                            @click="toggleModule('{{ $module->id }}')"
                                            class="p-3 bg-white/80 backdrop-blur-sm border-2 transition-all duration-200 rounded-xl relative group hover:shadow-lg hover:scale-105"
                                            :class="formData.selected_modules.includes('{{ $module->id }}') ? 'border-[{{ $module->color }}] shadow-md' : 'border-transparent hover:border-[{{ $module->color }}]'"
                                            style="--module-color: {{ $module->color }}; width: 110px;">
                                        <div class="text-center">
                                            <!-- Module Icon as SVG with Dynamic Color -->
                                            <div class="flex items-center justify-center mb-1.5">
                                                <svg class="w-6 h-6 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                     :style="formData.selected_modules.includes('{{ $module->id }}') ? 'color: {{ $module->color }}' : 'color: #6B7280'"
                                                     style="color: #6B7280;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module->icon }}"></path>
                                                </svg>
                                            </div>
                                            <p class="font-semibold transition-colors text-[10px] leading-tight"
                                               :class="formData.selected_modules.includes('{{ $module->id }}') ? 'text-gray-900' : 'text-gray-700 group-hover:text-gray-900'"
                                               :style="formData.selected_modules.includes('{{ $module->id }}') ? 'color: {{ $module->color }}' : ''">{{ $module->display_name }}</p>
                                        </div>
                                        <div x-show="formData.selected_modules.includes('{{ $module->id }}')" 
                                             class="absolute -top-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center"
                                             style="background-color: {{ $module->color }};">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <p x-show="errors.selected_modules" class="mt-3 text-red-500 text-sm" x-text="errors.selected_modules"></p>
                    </div>
                </div>
            </div>

            <!-- Footer with Navigation -->
            <div class="border-t border-gray-200 bg-white/80 backdrop-blur-sm flex-shrink-0">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Back Button -->
                        <button type="button" 
                                @click="previousQuestion"
                                x-show="currentQuestion > 1"
                                class="flex items-center space-x-2 px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium rounded-xl transition-all hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Kembali</span>
                        </button>
                        <div x-show="currentQuestion === 1"></div>
                        
                        <!-- Progress Indicator -->
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <template x-for="i in totalQuestions" :key="i">
                                    <div class="transition-all duration-300"
                                         :class="currentQuestion >= i ? 'w-8 h-2 bg-[#006BFF] rounded-full' : 'w-2 h-2 bg-gray-300 rounded-full'"></div>
                                </template>
                            </div>
                            <span class="text-sm font-medium text-gray-600">
                                <span x-text="currentQuestion"></span> / <span x-text="totalQuestions"></span>
                            </span>
                        </div>
                        
                        <!-- Next/Submit Button -->
                        <button type="button" 
                                @click="currentQuestion < totalQuestions ? nextQuestion() : submitForm()"
                                x-show="currentQuestion !== 2 && currentQuestion !== 4"
                                :disabled="!canProceed() || isSubmitting"
                                class="flex items-center space-x-2 px-6 py-2.5 bg-[#006BFF] hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40">
                            <span x-show="!isSubmitting && currentQuestion < totalQuestions">Lanjutkan</span>
                            <span x-show="!isSubmitting && currentQuestion === totalQuestions">Selesai</span>
                            <span x-show="isSubmitting" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                            <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div x-show="currentQuestion === 2 || currentQuestion === 4"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function onboardingForm() {
            return {
                currentQuestion: 1,
                totalQuestions: 8,
                isSubmitting: false,
                showLoading: false,
                loadingStep: 0,
                loadingComplete: false,
                formData: {
                    business_name: '',
                    business_category: '',
                    business_age_month: '',
                    monthly_revenue_range: '',
                    owner_name: '',
                    contact_email: '',
                    contact_phone: '',
                    usage_reason: '',
                    selected_modules: []
                },
                errors: {},
                modules: @json($modules),

                init() {
                    const userEmail = '{{ auth()->user()->email }}';
                    if (userEmail) {
                        this.formData.contact_email = userEmail;
                    }
                    const userName = '{{ auth()->user()->name }}';
                    if (userName) {
                        this.formData.owner_name = userName;
                    }
                },

                canProceed() {
                    switch(this.currentQuestion) {
                        case 1: return this.formData.business_name;
                        case 2: return this.formData.business_category;
                        case 3: return this.formData.business_age_month;
                        case 4: return this.formData.monthly_revenue_range;
                        case 5: return this.formData.owner_name;
                        case 6: return this.formData.contact_email;
                        case 7: return true;
                        case 8: return this.formData.selected_modules.length > 0;
                        default: return true;
                    }
                },

                nextQuestion() {
                    if (this.validateCurrentQuestion()) {
                        if (this.currentQuestion < this.totalQuestions) {
                            this.currentQuestion++;
                        }
                    }
                },

                previousQuestion() {
                    if (this.currentQuestion > 1) {
                        this.currentQuestion--;
                    }
                },

                toggleModule(moduleId) {
                    const index = this.formData.selected_modules.indexOf(moduleId);
                    if (index > -1) {
                        this.formData.selected_modules.splice(index, 1);
                    } else {
                        this.formData.selected_modules.push(moduleId);
                    }
                },

                validateCurrentQuestion() {
                    this.errors = {};
                    
                    switch(this.currentQuestion) {
                        case 1:
                            if (!this.formData.business_name) {
                                this.errors.business_name = 'Nama bisnis wajib diisi';
                                return false;
                            }
                            break;
                        case 2:
                            if (!this.formData.business_category) {
                                this.errors.business_category = 'Kategori bisnis wajib dipilih';
                                return false;
                            }
                            break;
                        case 3:
                            if (!this.formData.business_age_month || this.formData.business_age_month < 0) {
                                this.errors.business_age_month = 'Usia bisnis wajib diisi dengan angka valid';
                                return false;
                            }
                            break;
                        case 4:
                            if (!this.formData.monthly_revenue_range) {
                                this.errors.monthly_revenue_range = 'Rentang pendapatan wajib dipilih';
                                return false;
                            }
                            break;
                        case 5:
                            if (!this.formData.owner_name) {
                                this.errors.owner_name = 'Nama pemilik wajib diisi';
                                return false;
                            }
                            break;
                        case 6:
                            if (!this.formData.contact_email) {
                                this.errors.contact_email = 'Email kontak wajib diisi';
                                return false;
                            }
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailRegex.test(this.formData.contact_email)) {
                                this.errors.contact_email = 'Format email tidak valid';
                                return false;
                            }
                            break;
                        case 8:
                            if (this.formData.selected_modules.length === 0) {
                                this.errors.selected_modules = 'Pilih minimal 1 fitur';
                                return false;
                            }
                            break;
                    }
                    
                    return true;
                },

                async animateLoadingSteps() {
                    // Step 1: Analyzing profile
                    await new Promise(resolve => setTimeout(resolve, 800));
                    this.loadingStep = 1;
                    
                    // Step 2: Adjusting features
                    await new Promise(resolve => setTimeout(resolve, 800));
                    this.loadingStep = 2;
                    
                    // Step 3: Preparing dashboard
                    await new Promise(resolve => setTimeout(resolve, 800));
                    this.loadingStep = 3;
                    
                    // Show success message
                    await new Promise(resolve => setTimeout(resolve, 500));
                    this.loadingComplete = true;
                    
                    // Wait a bit before redirect
                    await new Promise(resolve => setTimeout(resolve, 1500));
                },

                async submitForm() {
                    if (!this.validateCurrentQuestion()) {
                        return;
                    }

                    this.isSubmitting = true;
                    
                    try {
                        const response = await fetch('{{ route("onboarding.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Show loading screen
                            this.showLoading = true;
                            this.isSubmitting = false;
                            
                            // Animate loading steps
                            await this.animateLoadingSteps();
                            
                            // Redirect to dashboard using the URL from response
                            window.location.href = data.redirect_url;
                        } else {
                            this.errors = data.errors || {};
                            alert(data.message || 'Terjadi kesalahan. Silakan periksa kembali data Anda.');
                            this.isSubmitting = false;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
