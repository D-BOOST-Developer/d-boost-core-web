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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body, * { font-family: 'Space Grotesk', sans-serif; }
        </style>
        
        <!-- TailwindCSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#A78BFA',
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
    <body class="font-sans antialiased" style="background-color: #0A0A0F; color: #F1F0FF;">
        <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-32 w-96 h-96 bg-[#A78BFA]/15 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-32 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-[#A78BFA]/8 rounded-full blur-3xl"></div>
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
                    <h1 class="text-2xl font-bold mb-2" style="color: #F1F0FF;">D-BOOST</h1>
                    <p class="text-sm" style="color: #A9A8C3;">Digital Empowerment Platform</p>
                    <p class="text-xs mt-1" style="color: #6B6A8A;">Digitalize your business with intelligent insights</p>
                </div>

                <!-- Login Form Card -->
                <div class="rounded-2xl p-8 shadow-2xl" style="background-color: #1A1A24; border: 1px solid #2A2A3A;">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-6">
                    <p class="text-xs" style="color: #6B6A8A;">
                        © {{ date('Y') }} D-BOOST. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
