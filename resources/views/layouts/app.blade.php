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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- TailwindCSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: '#006BFF',
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
    <body class="font-sans antialiased bg-white text-gray-900">
        <div class="min-h-screen bg-gray-50 flex">
            <!-- Sidebar - Only show if not on modular dashboard -->
            @if(!request()->routeIs('dashboard'))
                @include('layouts.sidebar')
            @endif

            <!-- Main Content -->
            <div class="flex-1 flex flex-col h-screen">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow border-b border-gray-200 flex-shrink-0">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-white min-h-0">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div x-data="{ show: true }" 
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg">
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
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-lg">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-400 hover:text-red-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
