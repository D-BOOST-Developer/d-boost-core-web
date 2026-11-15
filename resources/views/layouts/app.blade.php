<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SDA UMKM Dashboard') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/D-BOOST-removebg.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- TailwindCSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: '#A78BFA',
                        },
                        animation: {
                            glow: {
                                '0%': { boxShadow: '0 0 5px rgb(0, 127, 196), 0 0 10px rgb(0, 127, 196), 0 0 15px rgb(0, 127, 196)' },
                                '100%': { boxShadow: '0 0 10px rgb(0, 127, 196), 0 0 20px rgb(0, 127, 196), 0 0 30px rgb(0, 127, 196)' }
                            }
                        }
                    }
                }
            }
        </script>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Global Alpine.js Components -->
        <script>
            // Global delete modal function
            function openDeleteModal(action, title = 'Delete Confirmation', message = 'Are you sure you want to delete this item?') {
                window.dispatchEvent(new CustomEvent('open-delete-modal', {
                    detail: { action, title, message }
                }));
            }

            // Global loading state management
            document.addEventListener('alpine:init', () => {
                Alpine.store('app', {
                    loading: false,
                    loadingMessage: 'Loading...',
                    showLoading(message = 'Loading...') {
                        this.loading = true;
                        this.loadingMessage = message;
                    },
                    hideLoading() {
                        this.loading = false;
                    }
                });

                // Ensure navigation components are initialized
                document.addEventListener('DOMContentLoaded', () => {
                    // Add loaded class to navigation after Alpine.js is ready
                    setTimeout(() => {
                        const navContainer = document.querySelector('.navigation-container');
                        if (navContainer) {
                            navContainer.classList.add('loaded');
                        }
                    }, 50);
                });
            });
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Navigation Stability Styles -->
        <style>
            body, * { font-family: 'Space Grotesk', sans-serif; }

            /* ===================================================
               GLOBAL DARK / BLACK THEME OVERRIDES
               =================================================== */
            html, body {
                background-color: #0A0A0F !important;
                color: #F1F0FF !important;
            }

            /* Surfaces & Backgrounds */
            .bg-white {
                background-color: #1A1A24 !important;
            }
            .bg-gray-50 {
                background-color: #0A0A0F !important;
            }
            .bg-gray-100 {
                background-color: #252535 !important;
            }
            .bg-gray-200 {
                background-color: #2E2E42 !important;
            }

            /* Borders */
            .border-gray-100, .border-gray-200, .border-gray-300 {
                border-color: #2A2A3A !important;
            }

            /* Typography */
            .text-gray-900, .text-gray-800 {
                color: #F1F0FF !important;
            }
            .text-gray-700, .text-gray-600 {
                color: #A9A8C3 !important;
            }
            .text-gray-500, .text-gray-400 {
                color: #7E7D9C !important;
            }

            /* Hover States */
            .hover\:bg-gray-50:hover, .hover\:bg-gray-100:hover {
                background-color: #252535 !important;
            }
            .hover\:text-gray-900:hover, .hover\:text-gray-800:hover {
                color: #FFFFFF !important;
            }
            .hover\:text-\[\#006BFF\]:hover {
                color: #C4B5FD !important;
            }

            /* Primary Colors & Accents (#006BFF -> #A78BFA) */
            .text-\[\#006BFF\] {
                color: #A78BFA !important;
            }
            .bg-\[\#006BFF\] {
                background-color: #A78BFA !important;
            }
            .bg-\[\#006BFF\]\/10 {
                background-color: rgba(167, 139, 250, 0.15) !important;
            }
            .bg-\[\#006BFF\]\/20 {
                background-color: rgba(167, 139, 250, 0.25) !important;
            }
            .border-\[\#006BFF\]\/20, .border-\[\#006BFF\]\/30 {
                border-color: rgba(167, 139, 250, 0.35) !important;
            }
            .bg-\[\#006BFF\]\/80 {
                background-color: rgba(167, 139, 250, 0.8) !important;
            }
            .hover\:bg-\[\#006BFF\]:hover, .hover\:bg-\[\#006BFF\]\/80:hover {
                background-color: #9333EA !important;
            }
            .focus\:ring-\[\#006BFF\]:focus {
                --tw-ring-color: #A78BFA !important;
            }
            .focus\:border-\[\#006BFF\]:focus {
                border-color: #A78BFA !important;
            }

            /* Header / Navbar Elements */
            header, .dashboard-header {
                background-color: #111118 !important;
                border-bottom: 1px solid #2A2A3A !important;
            }
            header h1, header h2, header h3, .dashboard-header h1, .dashboard-header h2, .dashboard-header h3 {
                color: #F1F0FF !important;
            }

            /* Cards & Modals */
            .card, [class*="rounded-xl"], [class*="rounded-2xl"] {
                border-color: #2A2A3A;
            }

            /* Tables */
            table {
                border-color: #2A2A3A !important;
            }
            thead tr, th {
                background-color: #161622 !important;
                color: #A9A8C3 !important;
                border-color: #2A2A3A !important;
            }
            tbody tr {
                background-color: #1A1A24 !important;
                border-color: #2A2A3A !important;
            }
            tbody tr:hover {
                background-color: #222232 !important;
            }
            td {
                border-color: #2A2A3A !important;
                color: #F1F0FF !important;
            }

            /* Form Elements */
            input:not([type="checkbox"]):not([type="radio"]), select, textarea {
                background-color: #111118 !important;
                border-color: #2A2A3A !important;
                color: #F1F0FF !important;
            }
            input::placeholder, textarea::placeholder {
                color: #5A5A7A !important;
            }

            /* Soft Badges */
            .bg-blue-50 {
                background-color: rgba(167, 139, 250, 0.15) !important;
            }
            .bg-teal-50, .bg-green-50 {
                background-color: rgba(16, 185, 129, 0.15) !important;
            }
            .bg-red-50 {
                background-color: rgba(239, 68, 68, 0.15) !important;
            }
            .bg-yellow-50, .bg-amber-50 {
                background-color: rgba(245, 158, 11, 0.15) !important;
            }
            .bg-violet-50, .bg-purple-50 {
                background-color: rgba(167, 139, 250, 0.15) !important;
            }
            
            /* User dropdown positioning fixes */
            .user-dropdown {
                min-width: 200px;
                max-width: 280px;
            }
            .user-dropdown.collapsed {
                position: fixed !important;
                z-index: 9999 !important;
            }
            
            /* Responsive dropdown positioning */
            @media (max-width: 768px) {
                .user-dropdown.collapsed {
                    left: 70px !important;
                    right: auto !important;
                    top: auto !important;
                    transform: none !important;
                }
            }
            
            /* Collapsed sidebar dropdown positioning */
            .dropdown-collapsed-positioning {
                top: 50%;
                transform: translateY(-50%);
                right: 100%;
                margin-right: 12px;
                width: 220px;
                position: absolute;
            }
            
            /* Fixed positioning for collapsed sidebar popup */
            .sidebar-collapsed .user-dropdown {
                position: fixed !important;
                left: 80px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                width: 220px !important;
                z-index: 9999 !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .sidebar-collapsed .user-dropdown {
                    left: 70px !important;
                    width: 200px !important;
                }
            }
            
            /* Sidebar sticky and scroll fixes */
            .sidebar-container {
                position: sticky;
                top: 0;
                height: 100vh;
                overflow: hidden;
            }
            
            /* Smooth sidebar transitions */
            .sidebar-transition {
                transition: width 0.3s ease-in-out;
            }
            
            /* Hide scrollbar for sidebar */
            .sidebar-container::-webkit-scrollbar {
                display: none;
            }
            
            .sidebar-container {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    </head>
    <body class="font-sans antialiased" style="background-color: #0A0A0F; color: #F1F0FF; font-family: 'Space Grotesk', sans-serif;">
        <div class="min-h-screen flex" style="background-color: #0A0A0F;">
            <!-- Sidebar - Only show if not on modular dashboard -->
            @if(!request()->routeIs('dashboard'))
                @include('layouts.sidebar')
            @endif

            <!-- Main Content -->
            <div class="flex-1 flex flex-col h-screen">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="flex-shrink-0" style="background-color: #111118; border-bottom: 1px solid #2A2A3A;">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto min-h-0" style="background-color: #0A0A0F;">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div x-data="{ show: true }" 
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg" style="background-color: #1A2F1A; border: 1px solid #2D5A2D; color: #86EFAC;">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-400 hover:text-green-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg" style="background-color: #2F1A1A; border: 1px solid #5A2D2D; color: #FCA5A5;">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-400 hover:text-red-300">                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        
        <!-- Toast Notification Component -->
        @include('components.toast-notification')

        <!-- Delete Modal -->
        <x-delete-modal />

        <!-- Confirm Modal -->
        <x-confirm-modal 
            title="Confirmation"
            message="Are you sure?"
            confirmText="Confirm"
            confirmColor="green"
            icon="check"
        />

        <!-- Reject Modal -->
        <x-reject-modal />

        <!-- DEBY AI Widget -->
        <x-deby-ai-widget />

        @stack('scripts')
    </body>
</html>
