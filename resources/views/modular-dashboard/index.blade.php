<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
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
                    fontFamily: {
                        sans: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        primary: '#A78BFA',
                    },
                    animation: {
                        glow: {
                            '0%': { boxShadow: '0 0 5px #A78BFA, 0 0 10px #A78BFA, 0 0 15px #A78BFA' },
                            '100%': { boxShadow: '0 0 10px #A78BFA, 0 0 20px #A78BFA, 0 0 30px #A78BFA' }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Custom Styles for Sidebar -->
    <style>
        /* ===================================================
           GLOBAL DARK / BLACK THEME OVERRIDES
           =================================================== */
        html, body {
            background-color: #0A0A0F !important;
            color: #F1F0FF !important;
            font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, sans-serif !important;
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

        /* Module Popup Overrides */
        .module-popup {
            background-color: #1A1A24 !important;
            border-color: #2A2A3A !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
        }

        /* Global smooth scrolling */
        * {
            -webkit-tap-highlight-color: transparent;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Ensure sidebar is fixed height and sticky */
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

        /* Ensure main content can scroll properly */
        .main-content-scrollable {
            height: 100vh;
            overflow-y: auto;
        }

        /* Hide scrollbar for sidebar */
        .sidebar-container::-webkit-scrollbar {
            display: none;
        }

        .sidebar-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Module popup styles */
        .module-popup {
            backdrop-filter: blur(12px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.05);
            margin-bottom: -4px;
            /* Bring popup closer to button area */
        }

        /* Module card animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes moduleGlow {

            0%,
            100% {
                box-shadow: 0 4px 20px rgba(0, 255, 156, 0.1);
            }

            50% {
                box-shadow: 0 8px 40px rgba(0, 255, 156, 0.2);
            }
        }

        /* Enhanced module card styling */
        .module-card-container .group:hover {
            animation: moduleGlow 2s ease-in-out infinite;
        }

        /* Smooth grid transitions */
        .module-card-container {
            perspective: 1000px;
            position: relative;
        }

        .module-card-container .group {
            transform-style: preserve-3d;
            backface-visibility: hidden;
            will-change: transform, box-shadow;
        }

        /* Visual hierarchy improvements */
        .module-card-container::before {
            content: '';
            position: absolute;
            top: -1rem;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 156, 0.3), transparent);
            opacity: 0.5;
        }

        /* Responsive grid improvements */
        @media (min-width: 768px) {
            .module-card-container {
                gap: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .module-card-container {
                gap: 2rem;
            }
        }

        /* Enhanced popup animations */
        .popup-enter {
            animation: popupSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .popup-leave {
            animation: popupSlideOut 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Advanced popup transitions */
        .module-popup {
            transform-origin: center bottom;
            will-change: transform, opacity;
        }

        /* Staggered popup content animation */
        .module-popup .popup-content-item {
            animation: popupContentFadeIn 0.3s ease-out forwards;
            opacity: 0;
        }

        .module-popup .popup-content-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .module-popup .popup-content-item:nth-child(2) {
            animation-delay: 0.15s;
        }

        .module-popup .popup-content-item:nth-child(3) {
            animation-delay: 0.2s;
        }

        .module-popup .popup-content-item:nth-child(4) {
            animation-delay: 0.25s;
        }

        @keyframes popupContentFadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes popupSlideOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            to {
                opacity: 0;
                transform: translateY(8px) scale(0.95);
            }
        }

        /* Ensure popups don't get clipped */
        .module-card-container {
            overflow: visible !important;
        }

        /* ============================================
               MOBILE RESPONSIVE STYLES
               ============================================ */

        @media (max-width: 767px) {

            /* Fix body and html for mobile scrolling */
            html,
            body {
                height: 100%;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            body {
                position: relative;
            }

            /* Hide sidebar on mobile */
            .sidebar-container {
                display: none !important;
            }

            /* Hide desktop header on mobile */
            .dashboard-header {
                display: none !important;
            }

            /* Full width main content on mobile */
            .navigation-container {
                display: block !important;
                height: auto !important;
                min-height: 100vh;
                overflow: visible !important;
                /* Fix sticky positioning by removing new stacking context */
                contain: none !important;
                transform: none !important;
                will-change: auto !important;
            }

            .navigation-container>div:last-child {
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* Enable scrolling on main content */
            main {
                height: auto !important;
                min-height: 100vh;
                overflow-y: visible !important;
                overflow-x: hidden;
                padding-bottom: 5rem !important;
                /* Space for bottom nav */
            }

            main>div {
                padding-bottom: 2rem;
            }

            /* Mobile header with gradient */
            .mobile-header {
                background: linear-gradient(135deg, #1E1B2E 0%, #111118 100%);
                border-radius: 0 0 24px 24px;
                padding: 1.5rem 1.25rem;
                margin: 0;
                box-shadow: 0 4px 20px rgba(167, 139, 250, 0.2);
            }

            .mobile-greeting {
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.875rem;
                font-weight: 400;
                margin-bottom: 0.25rem;
            }

            .mobile-username {
                color: white;
                font-size: 1.25rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
            }

            /* Account Balance Card */
            .account-balance-card {
                background: rgba(26, 26, 36, 0.85);
                backdrop-filter: blur(10px);
                border-radius: 16px;
                padding: 1.25rem;
                border: 1px solid #2A2A3A;
            }

            .balance-label {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.75rem;
                font-weight: 500;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.375rem;
            }

            .balance-amount {
                color: white;
                font-size: 1.875rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                letter-spacing: -0.02em;
            }

            .account-number {
                color: rgba(255, 255, 255, 0.7);
                font-size: 0.75rem;
                margin-bottom: 1rem;
            }

            /* Action Buttons in Balance Card */
            .balance-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .balance-action-btn {
                background: #252535;
                color: #A78BFA;
                border: 1px solid #3A3A4A;
                border-radius: 12px;
                padding: 0.75rem;
                font-size: 0.875rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .balance-action-btn:active {
                transform: scale(0.95);
                background: #2E2E42;
            }

            .balance-action-btn svg {
                width: 1rem;
                height: 1rem;
            }

            /* Financial Chart Section */
            .financial-chart-section {
                padding: 0 1.25rem;
                margin-top: 1.25rem;
            }

            .financial-chart-card {
                background: #1A1A24;
                border-radius: 16px;
                padding: 1.25rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
                cursor: pointer;
                transition: all 0.3s ease;
                border: 1px solid #2A2A3A;
            }

            .financial-chart-card:active {
                transform: scale(0.98);
                box-shadow: 0 4px 15px rgba(0, 107, 255, 0.15);
            }

            .chart-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
            }

            .chart-title {
                font-size: 1rem;
                font-weight: 700;
                color: #F1F0FF;
            }

            .chart-period {
                font-size: 0.75rem;
                color: #6B7280;
                background: #252535;
                padding: 0.25rem 0.75rem;
                border-radius: 12px;
            }

            .chart-container {
                position: relative;
                height: 180px;
                margin-bottom: 0.75rem;
            }

            .chart-stats {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid #2A2A3A;
            }

            .chart-stat-item {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .chart-stat-label {
                font-size: 0.6875rem;
                color: #6B7280;
                font-weight: 500;
            }

            .chart-stat-value {
                font-size: 0.9375rem;
                font-weight: 700;
            }

            .chart-stat-value.positive {
                color: #10B981;
            }

            .chart-stat-value.negative {
                color: #EF4444;
            }

            .chart-stat-value.neutral {
                color: #F1F0FF;
            }

            /* Quick Actions Section */
            .quick-actions-section {
                padding: 1.25rem;
            }

            .section-title {
                font-size: 1rem;
                font-weight: 700;
                color: #F1F0FF;
                margin-bottom: 1rem;
            }

            .quick-actions-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
            }

            .quick-action-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
            }

            .quick-action-icon {
                width: 3.5rem;
                height: 3.5rem;
                background: rgba(167, 139, 250, 0.15);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .quick-action-item:active .quick-action-icon {
                transform: scale(0.9);
                background: rgba(167, 139, 250, 0.25);
            }

            .quick-action-icon svg {
                width: 1.5rem;
                height: 1.5rem;
                color: #A78BFA;
            }

            .quick-action-label {
                font-size: 0.75rem;
                font-weight: 500;
                color: #A9A8C3;
                text-align: center;
            }

            /* Recent Transactions Section */
            .transactions-section {
                padding: 0 1.25rem 6rem 1.25rem;
                /* Increased bottom padding for bottom nav */
            }

            .transactions-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
            }

            .transactions-filter {
                background: white;
                border: 1px solid #2A2A3A;
                border-radius: 8px;
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                color: #6B7280;
                display: flex;
                align-items: center;
                gap: 0.375rem;
            }

            .transaction-item {
                background: white;
                border-radius: 16px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.875rem;
                border: 1px solid #2A2A3A;
                transition: all 0.2s ease;
            }

            .transaction-item:active {
                transform: scale(0.98);
                background: #0A0A0F;
            }

            .transaction-icon {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .transaction-icon svg {
                width: 1.25rem;
                height: 1.25rem;
            }

            .transaction-details {
                flex: 1;
                min-width: 0;
            }

            .transaction-name {
                font-size: 0.9375rem;
                font-weight: 600;
                color: #F1F0FF;
                margin-bottom: 0.25rem;
            }

            .transaction-date {
                font-size: 0.75rem;
                color: #9CA3AF;
            }

            .transaction-amount-wrapper {
                text-align: right;
            }

            .transaction-amount {
                font-size: 0.9375rem;
                font-weight: 700;
                margin-bottom: 0.25rem;
            }

            .transaction-amount.positive {
                color: #10B981;
            }

            .transaction-amount.negative {
                color: #EF4444;
            }

            .transaction-type {
                font-size: 0.6875rem;
                color: #6B7280;
                text-transform: capitalize;
            }

            /* Bottom Navigation */
            .bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-top: 1px solid #2A2A3A;
                padding: 0.75rem 1.25rem;
                padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
                /* iOS safe area */
                display: flex;
                justify-content: space-around;
                align-items: center;
                z-index: 1000;
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
            }

            .bottom-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.25rem;
                cursor: pointer;
                transition: all 0.2s ease;
                padding: 0.5rem;
                border-radius: 12px;
                min-width: 3.5rem;
            }

            .bottom-nav-item:active {
                transform: scale(0.9);
            }

            .bottom-nav-item.active {
                background: rgba(167, 139, 250, 0.15);
            }

            .bottom-nav-icon {
                width: 1.5rem;
                height: 1.5rem;
                color: #9CA3AF;
                transition: color 0.2s ease;
            }

            .bottom-nav-item.active .bottom-nav-icon {
                color: #A78BFA;
            }

            .bottom-nav-label {
                font-size: 0.6875rem;
                color: #9CA3AF;
                font-weight: 500;
                transition: color 0.2s ease;
            }

            .bottom-nav-item.active .bottom-nav-label {
                color: #A78BFA;
                font-weight: 600;
            }

            /* Hide desktop elements on mobile */
            .module-card-container {
                display: none !important;
            }

            /* Animations */
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .mobile-header {
                animation: slideInUp 0.5s ease-out;
            }

            .quick-actions-section {
                animation: slideInUp 0.6s ease-out 0.1s both;
            }

            .transactions-section {
                animation: slideInUp 0.7s ease-out 0.2s both;
            }

            .transaction-item {
                animation: fadeIn 0.4s ease-out;
            }

            /* Upgrade notice - hide on mobile */
            .upgrade-notice {
                display: none !important;
            }
        }

        /* Desktop - hide mobile elements */
        @media (min-width: 768px) {

            .mobile-header,
            .quick-actions-section,
            .transactions-section,
            .bottom-nav,
            .mobile-only {
                display: none !important;
            }
        }
    </style>

    <!-- Global Alpine.js Components -->
    <script>
        // Global loading state management
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                loading: false,
                loadingMessage: '',

                showLoading(message = 'Loading...') {
                    this.loading = true;
                    this.loadingMessage = message;
                },

                hideLoading() {
                    this.loading = false;
                    this.loadingMessage = '';
                }
            });
        });
    </script>
</head>

<body class="font-sans antialiased" style="background-color: #0A0A0F; color: #F1F0FF;">
    <!-- Sidebar Navigation -->
    <div x-data="navigationComponent()" x-init="init()" class="flex h-screen navigation-container" style="background-color: #0A0A0F;"
        :class="sidebarExpanded ? '' : 'sidebar-collapsed'" style="contain: layout style;">
        <!-- Sidebar -->
        <div :class="sidebarExpanded ? 'w-64' : 'w-16'"
            class="sidebar-transition flex flex-col relative z-[1000] sidebar-container h-screen sticky top-0 overflow-hidden" style="background-color: #0E0E16; border-right: 1px solid #2A2A3A;">

            <!-- Logo Section -->
            <div class="flex items-center justify-center h-16" style="border-bottom: 1px solid #2A2A3A;">
                <div class="flex items-center space-x-3">
                    @php
                        $sidebarProfile = Auth::user()->businessProfile;
                        $sidebarLogo = $sidebarProfile && $sidebarProfile->business_logo_url
                            ? $sidebarProfile->business_logo_url
                            : asset('images/D-BOOST.png');
                    @endphp

                    <img src="{{ $sidebarLogo }}" alt="Logo"
                        class="w-8 h-8 rounded-lg object-cover shadow-sm ring-1 ring-gray-200/50">

                    <div x-show="sidebarExpanded" x-transition class="flex flex-col">
                        <span class="font-bold text-base leading-tight tracking-tight" style="color: #F1F0FF;">D-BOOST</span>
                        <span
                            class="text-[#A78BFA] font-semibold text-[10px] uppercase tracking-wider leading-none">Partners</span>
                    </div>
                </div>
            </div>

            <!-- User Section -->
            <div class="mt-auto" style="border-top: 1px solid #2A2A3A;" :class="sidebarExpanded ? 'p-4' : 'p-2'">
                <div x-data="userMenuComponent()" class="relative">
                    <button @click="toggleUserMenu()"
                        class="w-full flex items-center text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-all duration-200"
                        :class="sidebarExpanded ? 'px-2 py-3 justify-start' : 'px-0 py-3 justify-center'">
                        <div class="flex items-center justify-center" :class="sidebarExpanded ? 'w-8 h-8' : 'w-full'">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-[#A78BFA] to-[#7C3AED] rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div x-show="sidebarExpanded" x-transition class="ml-3 flex-1 text-left">
                            <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Auth::user()->getPrimaryRoleName() }}</div>
                        </div>
                        <svg x-show="sidebarExpanded" x-transition class="w-4 h-4 ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="userMenuOpen" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute backdrop-blur-sm rounded-lg shadow-2xl user-dropdown" style="background-color: #1A1A24; border: 1px solid #2A2A3A;"
                        :class="sidebarExpanded ? 'bottom-full left-0 w-full mb-2' : 'fixed top-1/2 -translate-y-1/2 left-20 w-56'"
                        x-cloak style="z-index: 9999 !important;">
                        <!-- User Info Header (only when collapsed) -->
                        <div x-show="!sidebarExpanded" class="px-4 py-3" style="border-bottom: 1px solid #2A2A3A;">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-[#A78BFA] to-[#7C3AED] rounded-full flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->getPrimaryRoleName() }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profile Settings</span>
                            </a>

                            <div class="my-1" style="border-top: 1px solid #2A2A3A;"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Toggle -->
            <div class="p-2" style="border-top: 1px solid #2A2A3A;">
                <button @click="toggleSidebar()"
                    class="w-full flex items-center justify-center px-2 py-2 text-[#7E7D9C] hover:text-[#A78BFA] hover:bg-[#1A1A24] rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 transform transition-transform duration-200"
                        :class="sidebarExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-screen">
            <!-- Top Header -->
            <header class="shadow-sm flex-shrink-0 dashboard-header" style="background-color: #111118; border-bottom: 1px solid #2A2A3A;">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        @php
                            $businessProfile = Auth::user()->businessProfile;
                            $businessName = $businessProfile ? $businessProfile->business_name : 'My Business';
                            $ownerName = $businessProfile ? $businessProfile->owner_name : Auth::user()->name;
                        @endphp

                        <!-- Business Name with DBOOST Badge -->
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-2xl leading-tight" style="color: #F1F0FF;">
                                {{ $businessName }}
                            </h2>
                            <div
                                class="flex items-center gap-1 px-2.5 py-0.5 rounded-full" style="background: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3);">
                                <span class="text-[10px] font-medium text-[#A78BFA]">Powered by</span>
                                <span class="text-[10px] font-bold text-[#A78BFA] tracking-wide">D-BOOST</span>
                            </div>
                        </div>

                        <!-- Welcome Message with Owner Name -->
                        <div class="flex items-center gap-2">
                            <div class="text-right">
                                <div class="text-sm" style="color: #7E7D9C;">Welcome back,</div>
                                <div class="text-base font-semibold" style="color: #F1F0FF;">{{ $ownerName }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>


            <!-- Mobile Header (Only visible on mobile) -->
            <div class="mobile-header mobile-only">
                <!-- Top Bar with Time and Profile -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        @php
                            $hour = now()->hour;
                            $greeting = $hour < 12 ? 'Good Morning!' : ($hour < 18 ? 'Good Afternoon!' : 'Good Evening!');
                        @endphp
                        <div class="mobile-greeting">{{ $greeting }}</div>
                        <div class="mobile-username">{{ Auth::user()->name }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Notification Icon -->
                        <button
                            class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center relative">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <!-- Profile Avatar -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-br from-[#A78BFA] to-[#7C3AED]">
                            <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Account Balance Card -->
                <div class="account-balance-card" style="position: relative; overflow: hidden;">
                    <div class="balance-label">
                        <span style="position: relative; z-index: 10;">Account Balance</span>
                        <svg class="w-3 h-3" style="position: relative; z-index: 10;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </div>

                    @php
                        // Calculate financial stats for the last 7 days for the chart
                        $last7Days = collect();
                        $chartLabels = [];
                        $incomeData = [];
                        $expenseData = [];

                        // Dummy Data for Visualization (Requested by User)
                        $dummyIncomes = [1500000, 4200000, 3100000, 5800000, 4900000, 7200000, 6500000];
                        $dummyExpenses = [800000, 2100000, 1500000, 4200000, 3100000, 2800000, 3200000];

                        for ($i = 6; $i >= 0; $i--) {
                            $date = now()->subDays($i);
                            $chartLabels[] = $date->format('D');

                            // Use dummy data if available, otherwise 0
                            // This gives the "looks" of active data
                            $incomeData[] = $dummyIncomes[6 - $i] ?? 0;
                            $expenseData[] = $dummyExpenses[6 - $i] ?? 0;
                        }

                        // Calculate Totals for Color Logic
                        $totalIncomeSum = array_sum($incomeData);
                        $totalExpenseSum = array_sum($expenseData);
                        $netResult = $totalIncomeSum - $totalExpenseSum;
                        $marginPercent = $totalIncomeSum > 0 ? ($netResult / $totalIncomeSum) * 100 : 0;

                        // Determine Chart Color based on Performance
                        if ($netResult < 0) {
                            // Bad (Loss) -> Red
                            $chartColor = '#F87171'; // Red-400
                            $chartMood = 'bad';
                        } elseif ($marginPercent < 20) {
                            // Fair (Low Margin) -> Yellow
                            $chartColor = '#FACC15'; // Yellow-400
                            $chartMood = 'fair';
                        } else {
                            // Good (Healthy Profit) -> Geen
                            $chartColor = '#4ADE80'; // Green-400
                            $chartMood = 'good';
                        }

                        $totalSales = isset($moduleStats['finance']['total_sales']) ? $moduleStats['finance']['total_sales'] : 0;
                        $totalExpenses = isset($moduleStats['finance']['total_expenses']) ? $moduleStats['finance']['total_expenses'] : 0;
                        $balance = $totalSales - $totalExpenses;
                        $accountNumber = str_pad(Auth::user()->id, 4, '0', STR_PAD_LEFT);
                    @endphp

                    <!-- Debug Info (Hidden) -->
                    <div style="display:none;">Mood: {{ $chartMood }}, Margin: {{ number_format($marginPercent, 1) }}%
                    </div>

                    <!-- Mini Chart Wrapper -->
                    <div class="mini-chart-wrapper" onclick="window.location.href='{{ route('analytics') }}'"
                        style="height: 80px; margin: 0.5rem -1.25rem 0.5rem -1.25rem; position: relative; cursor: pointer; mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
                        <canvas id="financialChart"></canvas>
                    </div>

                    <div class="balance-amount" style="position: relative; z-index: 10;">Rp
                        {{ number_format($balance, 0, ',', '.') }}
                    </div>
                    <div class="account-number" style="position: relative; z-index: 10;">Account no. ******
                        {{ $accountNumber }}
                    </div>

                    <!-- Action Buttons -->
                    <div class="balance-actions" style="position: relative; z-index: 10;">
                        <button class="balance-action-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Money</span>
                        </button>
                        <button class="balance-action-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            <span>Send Money</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section (Only visible on mobile) -->
            <div class="quick-actions-section mobile-only">
                <h3 class="section-title">Quick Actions</h3>
                <div class="quick-actions-grid">
                    @php
                        $quickActions = [
                            ['name' => 'Sales', 'route' => 'finance.sales.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
                            ['name' => 'Expenses', 'route' => 'finance.expenses.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['name' => 'Inventory', 'route' => 'module.redirect', 'param' => 'inventory', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ];
                    @endphp
                    @foreach($quickActions as $action)
                        <a href="{{ isset($action['param']) ? route($action['route'], $action['param']) : route($action['route']) }}"
                            class="quick-action-item">
                            <div class="quick-action-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $action['icon'] }}"></path>
                                </svg>
                            </div>
                            <div class="quick-action-label">{{ $action['name'] }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Recent Transactions Section (Only visible on mobile) -->
            <div class="transactions-section mobile-only">
                <div class="transactions-header">
                    <h3 class="section-title" style="margin-bottom: 0;">Recent Transactions</h3>
                    <button class="transactions-filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                    </button>
                </div>

                @php
                    // Get recent sales (income)
                    $recentSales = \App\Models\Sale::latest('sale_date')
                        ->take(3)
                        ->get();

                    // Get recent expenses (outcome)
                    $recentExpenses = \App\Models\Expense::latest('expense_date')
                        ->take(3)
                        ->get();

                    // Combine and sort by date
                    $transactions = collect();

                    foreach ($recentSales as $sale) {
                        $transactions->push([
                            'type' => 'income',
                            'name' => 'Walk-in',
                            'date' => $sale->sale_date,
                            'amount' => $sale->total_amount,
                            'label' => 'Sale',
                            'icon' => 'M5 13l4 4L19 7'
                        ]);
                    }

                    foreach ($recentExpenses as $expense) {
                        $transactions->push([
                            'type' => 'expense',
                            'name' => $expense->title,
                            'date' => $expense->expense_date,
                            'amount' => $expense->amount,
                            'label' => ucfirst($expense->category),
                            'icon' => 'M13 7l5 5m0 0l-5 5m5-5H6'
                        ]);
                    }

                    // Sort by date descending and take top 5
                    $transactions = $transactions->sortByDesc('date')->take(5);
                @endphp

                <!-- Transaction Items -->
                @forelse($transactions as $transaction)
                    <div class="transaction-item">
                        <div class="transaction-icon"
                            style="background: {{ $transaction['type'] === 'income' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }};">
                            <svg style="color: {{ $transaction['type'] === 'income' ? '#10B981' : '#EF4444' }};" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $transaction['icon'] }}"></path>
                            </svg>
                        </div>
                        <div class="transaction-details">
                            <div class="transaction-name">{{ Str::limit($transaction['name'], 25) }}</div>
                            <div class="transaction-date">{{ $transaction['date']->format('l d, h:i A') }}</div>
                        </div>
                        <div class="transaction-amount-wrapper">
                            <div
                                class="transaction-amount {{ $transaction['type'] === 'income' ? 'positive' : 'negative' }}">
                                {{ $transaction['type'] === 'income' ? '+' : '-' }}Rp
                                {{ number_format($transaction['amount'], 0, ',', '.') }}
                            </div>
                            <div class="transaction-type">{{ $transaction['label'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-sm">No transactions yet</p>
                    </div>
                @endforelse
            </div>

            <!-- Bottom Navigation (Only visible on mobile) -->
            <div class="bottom-nav mobile-only">
                <div class="bottom-nav-item">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="bottom-nav-label">Settings</span>
                </div>
                <div class="bottom-nav-item">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="bottom-nav-label">Activity</span>
                </div>
                <div class="bottom-nav-item active">
                    <svg class="bottom-nav-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path>
                    </svg>
                    <span class="bottom-nav-label">Home</span>
                </div>
                <div class="bottom-nav-item">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    <span class="bottom-nav-label">Assistant</span>
                </div>
                <div class="bottom-nav-item">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="bottom-nav-label">Profile</span>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto min-h-0" style="background-color: #0A0A0F;">
                <div class="py-8 px-4 sm:px-6 lg:px-8">

                    <!-- Quick Stats Section -->
                    @if(isset($moduleStats['dashboard']) || isset($moduleStats['finance']))
                        <div class="mb-12">
                            <h3 class="text-lg font-semibold mb-6" style="color: #F1F0FF;">Quick Overview</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                @php
                                    $overallStats = [];

                                    // Get dashboard stats from database
                                    if (isset($moduleStats['dashboard'])) {
                                        if (isset($moduleStats['dashboard']['today_income'])) {
                                            $overallStats[] = [
                                                'label' => 'Today Income',
                                                'value' => 'Rp ' . number_format($moduleStats['dashboard']['today_income']),
                                                'color' => '#10B981',
                                                'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'
                                            ];
                                        }
                                        if (isset($moduleStats['dashboard']['today_expenses'])) {
                                            $overallStats[] = [
                                                'label' => 'Today Expenses',
                                                'value' => 'Rp ' . number_format($moduleStats['dashboard']['today_expenses']),
                                                'color' => '#EF4444',
                                                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
                                            ];
                                        }
                                        if (isset($moduleStats['dashboard']['total_sales'])) {
                                            $overallStats[] = [
                                                'label' => 'Total Sales',
                                                'value' => $moduleStats['dashboard']['total_sales'],
                                                'color' => '#00FF9C',
                                                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'
                                            ];
                                        }
                                        if (isset($moduleStats['dashboard']['total_products'])) {
                                            $overallStats[] = [
                                                'label' => 'Total Products',
                                                'value' => $moduleStats['dashboard']['total_products'],
                                                'color' => '#3B82F6',
                                                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
                                            ];
                                        }
                                    }

                                    // // Get finance stats from database
                                    // if (isset($moduleStats['finance']['monthly_profit'])) {
                                    //     $overallStats[] = [
                                    //         'label' => 'Monthly Profit',
                                    //         'value' => 'Rp ' . number_format($moduleStats['finance']['monthly_profit']),
                                    //         'color' => '#10B981',
                                    //         'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'
                                    //     ];
                                    // }
                                @endphp

                                @foreach($overallStats as $stat)
                                    <div
                                        class="rounded-xl p-6 transition-all duration-300 hover:bg-[#252535]" style="background-color: #1A1A24; border: 1px solid #2A2A3A;">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm mb-1" style="color: #A9A8C3;">{{ $stat['label'] }}</p>
                                                <p class="text-2xl font-bold" style="color: #F1F0FF;">
                                                    {{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}
                                                </p>
                                            </div>
                                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                                style="background: linear-gradient(135deg, {{ $stat['color'] }}20, {{ $stat['color'] }}10);">
                                                <svg class="w-6 h-6" style="color: {{ $stat['color'] }}" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $stat['icon'] }}"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Module Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 module-card-container"
                        x-data="modularDashboard()" style="overflow: visible; padding: 0.5rem; margin: -0.5rem;">
                        @foreach($accessibleModules as $index => $module)
                            @php
                                $stats = $moduleStats[$module['name']] ?? [];
                                $primaryMenu = $module['menus']->first();
                                $moduleColor = $module['color'] ?? '#A78BFA';

                                // Create tooltip content
                                $tooltipStats = [];
                                if (!empty($stats)) {
                                    foreach (array_slice($stats, 0, 3) as $key => $value) {
                                        if ($key !== 'recent_activities') {
                                            $label = ucwords(str_replace('_', ' ', $key));
                                            $displayValue = is_numeric($value) ? number_format($value) : $value;
                                            $tooltipStats[] = "{$label}: {$displayValue}";
                                        }
                                    }
                                }
                                $tooltipContent = !empty($tooltipStats) ? implode(' • ', $tooltipStats) : $module['description'];
                            @endphp

                            <div class="relative rounded-2xl transition-all duration-500 hover:shadow-lg overflow-visible group hover:bg-[#222230]" style="background-color: #1A1A24; border: 1px solid #2A2A3A;"
                                x-data="modulePopup()" @mouseenter="setTimeout(() => showPopup = true, 150)"
                                @mouseleave="setTimeout(() => showPopup = false, 100)"
                                style="animation: fadeInUp 0.6s ease-out {{ $index * 0.1 }}s both; transform-origin: center bottom; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);"
                                onmouseover="this.style.boxShadow='0 8px 30px {{ $moduleColor }}15, 0 0 0 1px {{ $moduleColor }}25'; this.style.transform='translateY(-4px) scale(1.02)'"
                                onmouseout="this.style.boxShadow='0 4px 20px rgba(0, 0, 0, 0.06)'; this.style.transform='translateY(0) scale(1)'">

                                <!-- Mini Popup -->
                                <div x-show="showPopup" x-transition:enter="transition ease-out duration-400"
                                    x-transition:enter-start="opacity-0 transform translate-y-6 scale-90 rotate-1"
                                    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100 rotate-0"
                                    x-transition:leave="transition ease-in duration-250"
                                    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100 rotate-0"
                                    x-transition:leave-end="opacity-0 transform translate-y-6 scale-90 rotate-1"
                                    class="absolute bottom-16 left-1/2 transform -translate-x-1/2 z-50 w-80 backdrop-blur-md rounded-xl shadow-2xl p-4 module-popup" style="background-color: #1A1A24; border: 1px solid #2A2A3A;"
                                    style="z-index: 1000; border-color: {{ $moduleColor }}30; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px {{ $moduleColor }}15;">

                                    <!-- Popup Header -->
                                    <div class="flex items-center space-x-3 mb-3 popup-content-item">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                            style="background: linear-gradient(135deg, {{ $moduleColor }}30, {{ $moduleColor }}20);">
                                            @if($module['icon'])
                                                <svg class="w-5 h-5" style="color: {{ $moduleColor }}" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $module['icon'] }}"></path>
                                                </svg>
                                            @else
                                                <div class="w-5 h-5 rounded flex items-center justify-center text-white font-bold text-sm"
                                                    style="background-color: {{ $moduleColor }}">
                                                    {{ substr($module['display_name'], 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-sm" style="color: #F1F0FF;">{{ $module['display_name'] }}
                                            </h4>
                                            <p class="text-xs" style="color: #7E7D9C;">{{ $module['description'] }}</p>
                                        </div>
                                        <div class="ml-auto flex flex-col items-end space-y-1">
                                            @php
                                                $status = $stats['status'] ?? 'active';
                                                $healthScore = $stats['health_score'] ?? 90;

                                                // Define colors with proper values
                                                if ($status === 'active') {
                                                    $statusBg = 'rgb(240, 253, 244)'; // green-50
                                                    $statusText = 'rgb(22, 101, 52)'; // green-800
                                                    $statusBorder = 'rgb(187, 247, 208)'; // green-200
                                                    $statusDot = 'rgb(34, 197, 94)'; // green-500
                                                } elseif ($status === 'warning') {
                                                    $statusBg = 'rgb(254, 249, 195)'; // yellow-100
                                                    $statusText = 'rgb(133, 77, 14)'; // yellow-800
                                                    $statusBorder = 'rgb(253, 230, 138)'; // yellow-300
                                                    $statusDot = 'rgb(234, 179, 8)'; // yellow-500
                                                } else {
                                                    $statusBg = 'rgb(254, 226, 226)'; // red-100
                                                    $statusText = 'rgb(127, 29, 29)'; // red-800
                                                    $statusBorder = 'rgb(252, 165, 165)'; // red-300
                                                    $statusDot = 'rgb(239, 68, 68)'; // red-500
                                                }

                                                // Health score color
                                                if ($healthScore >= 80) {
                                                    $healthColor = 'rgb(74, 222, 128)'; // green-400
                                                } elseif ($healthScore >= 60) {
                                                    $healthColor = 'rgb(250, 204, 21)'; // yellow-400
                                                } else {
                                                    $healthColor = 'rgb(248, 113, 113)'; // red-400
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                style="background-color: {{ $statusBg }}; color: {{ $statusText }}; border: 1px solid {{ $statusBorder }};">
                                                <div class="w-1.5 h-1.5 rounded-full mr-1 animate-pulse"
                                                    style="background-color: {{ $statusDot }};"></div>
                                                {{ ucfirst($status) }}
                                            </span>
                                            <div class="flex items-center space-x-1">
                                                <div class="w-8 bg-gray-200 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                                        style="width: {{ $healthScore }}%; background-color: {{ $healthColor }};">
                                                    </div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $healthScore }}%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Module Statistics -->
                                    @if(!empty($stats))
                                        <div class="grid grid-cols-2 gap-3 mb-3 popup-content-item">
                                            @php
                                                $displayStats = array_slice($stats, 0, 4);
                                            @endphp
                                            @foreach($displayStats as $key => $value)
                                                @if(!in_array($key, ['recent_activities', 'status', 'health_score']))
                                                    <div class="rounded-lg p-2" style="background-color: #111118; border: 1px solid #2A2A3A;">
                                                        <p class="text-xs font-medium" style="color: #7E7D9C;">
                                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                                        </p>
                                                        <p class="text-sm font-semibold" style="color: #F1F0FF;">
                                                            @if(is_numeric($value))
                                                                {{ number_format($value) }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Last Updated -->
                                    <div class="flex items-center justify-between text-xs text-gray-500 border-top pt-2 popup-content-item"
                                        style="border-top: 1px solid #2A2A3A; color: #7E7D9C;">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Last updated: {{ now()->format('M d, H:i') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @php
                                                $menuColor = $module['menus']->count() > 0 ? 'rgb(22, 163, 74)' : 'rgb(234, 179, 8)'; // green-600 or yellow-500
                                            @endphp
                                            <svg class="w-3 h-3" style="color: {{ $menuColor }};" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span style="color: {{ $menuColor }};">
                                                {{ $module['menus']->count() }}
                                                menu{{ $module['menus']->count() !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Popup Arrow -->
                                    <div
                                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-[#1A1A24]">
                                    </div>
                                </div>

                                <!-- Module Header -->
                                <div class="p-6">
                                    <!-- Module Icon -->
                                    <div class="flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
                                        style="background: linear-gradient(135deg, {{ $moduleColor }}20, {{ $moduleColor }}10);">
                                        @if($module['icon'])
                                            <svg class="w-8 h-8" style="color: {{ $moduleColor }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $module['icon'] }}"></path>
                                            </svg>
                                        @else
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-lg"
                                                style="background-color: {{ $moduleColor }}">
                                                {{ substr($module['display_name'], 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Module Title -->
                                    <h3 class="text-lg font-semibold mb-2" style="color: #F1F0FF;">
                                        {{ $module['display_name'] }}
                                    </h3>

                                    <!-- Module Description -->
                                    <p class="text-sm mb-6 line-clamp-2" style="color: #A9A8C3;">
                                        {{ $module['description'] ?? 'Here has to be short description of this module. Some benefits.' }}
                                    </p>

                                    <!-- Module Action Button -->
                                    @if($module['name'] === 'dashboard')
                                        <!-- Dashboard Module - Show Analytics Button -->
                                        <a href="{{ route('analytics') }}"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-[#A78BFA] hover:text-[#C4B5FD]" style="background-color: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3); text-sm font-medium">
                                            <span>View Analytics</span>
                                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @elseif($module['name'] === 'inventory')
                                        <!-- Inventory Module - Auto redirect to first menu -->
                                        <a href="{{ route('module.redirect', 'inventory') }}"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-[#A78BFA] hover:text-[#C4B5FD]" style="background-color: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3); text-sm font-medium">
                                            <span>Open Inventory</span>
                                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @elseif($module['name'] === 'finance')
                                        <!-- Finance Module - Auto redirect to first menu -->
                                        <a href="{{ route('module.redirect', 'finance') }}"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-[#A78BFA] hover:text-[#C4B5FD]" style="background-color: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3); text-sm font-medium">
                                            <span>Open Finance</span>
                                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @elseif($module['name'] === 'settings')
                                        <!-- Settings Module - Single Button to open settings area -->
                                        <a href="{{ route('settings.modules.index') }}"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-[#A78BFA] hover:text-[#C4B5FD]" style="background-color: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3); text-sm font-medium">
                                            <span>Open Settings</span>
                                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>

                                    @elseif($module['name'] === 'digital_storefront')
                                        <!-- Digital Storefront Module - Simple button to open storefront -->
                                        <a href="{{ route('storefront.index') }}"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-[#A78BFA] hover:text-[#C4B5FD]" style="background-color: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3); text-sm font-medium">
                                            <span>Open Digital Storefront</span>
                                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @elseif($module['menus']->count() > 1)
                                        <!-- Multiple Menus - Show as dropdown -->
                                        <div class="relative" x-data="{ menuOpen: false }">
                                            <button @click="menuOpen = !menuOpen"
                                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 text-sm font-medium hover:bg-[#2C2C40]" style="background-color: #252535; color: #F1F0FF; border: 1px solid #3A3A4A;">
                                                <span>Explore Module</span>
                                                <svg class="w-4 h-4 transition-transform duration-200"
                                                    :class="menuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div x-show="menuOpen" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute top-full left-0 right-0 mt-2 rounded-lg shadow-2xl z-50" style="background-color: #1A1A24; border: 1px solid #2A2A3A;">
                                                @foreach($module['menus'] as $menu)
                                                    <a href="{{ $menu['url'] }}"
                                                        class="flex items-center px-4 py-3 text-sm hover:bg-[#252535] hover:text-[#F1F0FF] transition-colors duration-200 first:rounded-t-lg last:rounded-b-lg" style="color: #A9A8C3;">
                                                        @if($menu['icon'])
                                                            <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="{{ $menu['icon'] }}"></path>
                                                            </svg>
                                                        @endif
                                                        <div>
                                                            <div class="font-medium" style="color: #F1F0FF;">{{ $menu['display_name'] }}</div>
                                                            @if($menu['description'])
                                                                <div class="text-xs" style="color: #7E7D9C;">{{ $menu['description'] }}</div>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <!-- Single Menu - Direct link -->
                                        @if($primaryMenu)
                                            <a href="{{ $primaryMenu['url'] }}"
                                                class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-[#A78BFA] hover:text-[#C4B5FD]" style="background-color: rgba(167, 139, 250, 0.15); border: 1px solid rgba(167, 139, 250, 0.3); text-sm font-medium">
                                                <span>Open {{ $module['display_name'] }}</span>
                                                <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function modularDashboard() {
            return {
                activePopup: null, // Track which popup is currently active

                init() {
                    // Initialize any dashboard-specific functionality
                    console.log('Modular Dashboard initialized');
                },

                showPopup(moduleId) {
                    // Close any existing popup first
                    this.hideAllPopups();
                    // Set the active popup
                    this.activePopup = moduleId;
                },

                hidePopup(moduleId) {
                    if (this.activePopup === moduleId) {
                        this.activePopup = null;
                    }
                },

                hideAllPopups() {
                    this.activePopup = null;
                },

                isPopupActive(moduleId) {
                    return this.activePopup === moduleId;
                }
            }
        }

        // Module popup positioning
        function modulePopup(moduleId) {
            return {
                moduleId: moduleId,
                showPopup: false,
                popupPosition: 'top',
                hoverTimeout: null,

                init() {
                    // Auto-adjust popup position based on viewport
                    this.$watch('showPopup', (value) => {
                        if (value) {
                            this.$nextTick(() => {
                                this.adjustPopupPosition();
                            });
                        }
                    });
                },

                handleMouseEnter() {
                    // Clear any existing timeout
                    if (this.hoverTimeout) {
                        clearTimeout(this.hoverTimeout);
                    }

                    // Set timeout to show popup after delay
                    this.hoverTimeout = setTimeout(() => {
                        // Get parent dashboard component
                        const dashboard = this.$el.closest('[x-data*="modularDashboard"]').__x.$data;
                        dashboard.showPopup(this.moduleId);
                        this.showPopup = true;
                    }, 200); // Increased delay for better UX
                },

                handleMouseLeave() {
                    // Clear timeout if mouse leaves before popup shows
                    if (this.hoverTimeout) {
                        clearTimeout(this.hoverTimeout);
                    }

                    // Hide popup with small delay to allow moving to popup
                    setTimeout(() => {
                        const dashboard = this.$el.closest('[x-data*="modularDashboard"]').__x.$data;
                        dashboard.hidePopup(this.moduleId);
                        this.showPopup = false;
                    }, 150);
                },

                adjustPopupPosition() {
                    const popup = this.$el.querySelector('.module-popup');
                    const card = this.$el;

                    if (popup && card) {
                        const cardRect = card.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        const popupHeight = 200; // Approximate popup height

                        // Check if popup would go off-screen at the bottom
                        if (cardRect.bottom + popupHeight > viewportHeight - 20) {
                            // Position popup above the card instead
                            popup.classList.remove('bottom-16');
                            popup.classList.add('-top-2', '-translate-y-full');
                            // Update arrow direction
                            const arrow = popup.querySelector('.absolute');
                            if (arrow) {
                                arrow.className = 'absolute top-full left-1/2 transform -translate-x-1/2 -mt-px w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-[#1A1A24]';
                            }
                            this.popupPosition = 'top';
                        } else {
                            // Default position near button area
                            popup.classList.remove('-top-2', '-translate-y-full');
                            popup.classList.add('bottom-16');
                            // Update arrow direction
                            const arrow = popup.querySelector('.absolute');
                            if (arrow) {
                                arrow.className = 'absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-[#1A1A24]';
                            }
                            this.popupPosition = 'bottom';
                        }
                    }
                }
            }
        }

        // Navigation component to prevent redefinition issues
        function navigationComponent() {
            return {
                sidebarOpen: false,
                sidebarExpanded: false,
                isInitialized: false,

                init() {
                    // Prevent multiple initializations
                    if (this.isInitialized) return;

                    // Initialize from localStorage if available
                    const saved = localStorage.getItem('sidebarExpanded');
                    if (saved !== null) {
                        this.sidebarExpanded = saved === 'true';
                    }

                    this.isInitialized = true;

                    // Force a re-render after initialization
                    this.$nextTick(() => {
                        this.$el.style.opacity = '1';
                    });
                },

                toggleSidebar() {
                    this.sidebarExpanded = !this.sidebarExpanded;
                    localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
                }
            }
        }

        // User menu component
        function userMenuComponent() {
            return {
                userMenuOpen: false,

                init() {
                    // Close menu when clicking outside (global handler)
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest('[x-data*="userMenuComponent"]')) {
                            this.userMenuOpen = false;
                        }
                    });

                    // Handle escape key to close menu
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.userMenuOpen) {
                            this.userMenuOpen = false;
                        }
                    });
                },

                toggleUserMenu() {
                    this.userMenuOpen = !this.userMenuOpen;
                },

                closeUserMenu() {
                    this.userMenuOpen = false;
                }
            }
        }
    </script>

    <!-- Financial Chart Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartCanvas = document.getElementById('financialChart');

            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');

                // Chart data from PHP
                const chartLabels = @json($chartLabels ?? []);
                const incomeData = @json($incomeData ?? []);
                const expenseData = @json($expenseData ?? []);
                const chartColor = @json($chartColor ?? '#ffffff');

                // Create gradient for income line (Dynamic Color)
                const incomeGradient = ctx.createLinearGradient(0, 0, 0, 80);

                // Convert hex to rgba for gradient
                const hexToRgb = (hex) => {
                    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                    return result ? {
                        r: parseInt(result[1], 16),
                        g: parseInt(result[2], 16),
                        b: parseInt(result[3], 16)
                    } : null;
                }

                const rgb = hexToRgb(chartColor);
                const colorString = rgb ? `${rgb.r}, ${rgb.g}, ${rgb.b}` : '255, 255, 255';

                incomeGradient.addColorStop(0, `rgba(${colorString}, 0.5)`);
                incomeGradient.addColorStop(1, `rgba(${colorString}, 0.0)`);

                // Create gradient for expense line (More transparent)
                const expenseGradient = ctx.createLinearGradient(0, 0, 0, 80);
                expenseGradient.addColorStop(0, 'rgba(255, 255, 255, 0.1)');
                expenseGradient.addColorStop(1, 'rgba(255, 255, 255, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: 'Income',
                                data: incomeData,
                                borderColor: chartColor,
                                backgroundColor: incomeGradient,
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointBackgroundColor: chartColor,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1,
                                pointHoverBackgroundColor: chartColor,
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2
                            },
                            {
                                label: 'Expenses',
                                data: expenseData,
                                borderColor: 'rgba(255, 255, 255, 0.5)',
                                backgroundColor: expenseGradient,
                                borderWidth: 1.5,
                                borderDash: [5, 5],
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointBackgroundColor: 'rgba(255, 255, 255, 0.8)',
                                pointBorderColor: 'rgba(255, 255, 255, 0.8)',
                                pointBorderWidth: 0,
                                pointHoverBackgroundColor: 'rgba(255, 255, 255, 0.8)',
                                pointHoverBorderColor: 'rgba(255, 255, 255, 0.8)',
                                pointHoverBorderWidth: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: -10,
                                right: -10,
                                top: 0,
                                bottom: 0
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1e3a8a',
                                bodyColor: '#1e3a8a',
                                borderColor: 'rgba(255, 255, 255, 0.5)',
                                borderWidth: 1,
                                padding: 8,
                                displayColors: true,
                                boxPadding: 4,
                                usePointStyle: true,
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                display: false,
                                min: 0,
                                grid: {
                                    display: false
                                }
                            },
                            x: {
                                display: false,
                                grid: {
                                    display: false
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }
        });
    </script>

    <!-- Welcome Tour Session Flag -->
    <script>
        // Pass flash session flag to JavaScript
        @if(session('show_welcome_tour'))
            window.showWelcomeTour = true;
            console.log('✅ Welcome Tour Flag from Session: TRUE (User just completed onboarding)');
        @else
            window.showWelcomeTour = false;
            console.log('❌ Welcome Tour Flag from Session: FALSE (Regular login or session expired)');
        @endif
    </script>

    <!-- Welcome Tour Script -->
    <script src="{{ asset('js/welcome-tour.js') }}"></script>

    <!-- DEBY AI Widget -->
    @include('components.deby-ai-widget')
</body>

</html>