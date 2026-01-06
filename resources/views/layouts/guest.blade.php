<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>D-BOOST - Digital Empowerment Platform</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/D-BOOST-removebg.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- TailwindCSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#006BFF',
                        },
                    }
                }
            }
        </script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-32 w-96 h-96 bg-[#006BFF]/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-32 w-96 h-96 bg-cyan-400/10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-[#006BFF]/5 rounded-full blur-3xl"></div>
            </div>

            <!-- Login Container -->
            <div class="relative w-full max-w-md">
                <!-- Logo & Brand -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center mb-4">
                        <img src="{{ asset('images/D-BOOST.png') }}" 
                             alt="D-BOOST Logo" 
                             class="w-16 h-16 rounded-xl object-cover shadow-lg">
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">D-BOOST</h1>
                    <p class="text-gray-600 text-sm">Digital Empowerment Platform</p>
                    <p class="text-gray-500 text-xs mt-1">Digitalize your business with intelligent insights</p>
                </div>

                <!-- Login Form Card -->
                <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-xl">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-6">
                    <p class="text-gray-500 text-xs">
                        © {{ date('Y') }} D-BOOST. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
