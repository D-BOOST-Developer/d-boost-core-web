<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between desktop-only">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Smart Data Analytics Dashboard') }}
        </h2>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-500">{{ now()->format('d M Y, H:i') }}</span>
                <button onclick="window.location.reload()" class="p-2 text-gray-500 hover:text-[#006BFF] hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>



    <style>
        /* ============================================
           MOBILE RESPONSIVE STYLES FOR DASHBOARD
           ============================================ */
        
        /* ============================================
           MOBILE RESPONSIVE STYLES
           ============================================ */
        
        @media (max-width: 767px) {
            /* Fix body and html for mobile scrolling */
            html, body {
                height: 100%;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            body {
                position: relative;
            }
            
            /* Hide sidebar on mobile */
            .sidebar-container,
            aside,
            nav.sidebar,
            [class*="sidebar"],
            .navigation-container > div:first-child,
            div[class*="w-64"],
            div[class*="w-16"] {
                display: none !important;
                width: 0 !important;
            }
            
            /* Hide desktop header on mobile */
            header,
            .dashboard-header,
            .desktop-only {
                display: none !important;
            }
            
            /* Full width main content on mobile */
            .navigation-container,
            .navigation-container > div:last-child,
            main,
            .main-content-scrollable,
            .flex-1 {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                height: auto !important;
                min-height: 100vh;
                overflow: visible !important;
                /* Fix sticky positioning by removing new stacking context */
                contain: none !important;
                transform: none !important;
                will-change: auto !important;
            }
            
            /* Enable scrolling on main content */
            main {
                padding-bottom: 6rem !important; /* increased space for bottom nav */
            }
            
            main > div {
                padding-bottom: 2rem;
            }

            /* Mobile content styling */
            .mobile-dashboard-content {
                padding: 0 !important;
                background: #F9FAFB;
                min-height: 100vh;
            }
            
            /* Mobile header with gradient */
            .mobile-header {
                background: linear-gradient(135deg, #006BFF 0%, #02337eff 100%);
                border-radius: 0 0 24px 24px;
                padding: 1.5rem 1.25rem;
                margin: 0 0 1.5rem 0;
                box-shadow: 0 4px 20px rgba(0, 107, 255, 0.3);
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
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(10px);
                border-radius: 16px;
                padding: 1.25rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
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
                background: rgba(255, 255, 255, 0.95);
                color: #006BFF;
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
                background: rgba(255, 255, 255, 0.85);
            }
            
            .balance-action-btn svg {
                width: 1rem;
                height: 1rem;
            }

            /* Quick Actions Section */
            .quick-actions-section {
                padding: 0 1.25rem 1.25rem 1.25rem;
            }
            
            .section-title {
                font-size: 1rem;
                font-weight: 700;
                color: #1F2937;
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
                text-decoration: none;
            }
            
            .quick-action-icon {
                width: 3.5rem;
                height: 3.5rem;
                background: rgba(0, 107, 255, 0.1);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            
            .quick-action-item:active .quick-action-icon {
                transform: scale(0.9);
                background: rgba(0, 107, 255, 0.2);
            }
            
            .quick-action-icon svg {
                width: 1.5rem;
                height: 1.5rem;
                color: #006BFF;
            }
            
            .quick-action-label {
                font-size: 0.75rem;
                font-weight: 500;
                color: #4B5563;
                text-align: center;
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
                border-top: 1px solid #F3F4F6;
                padding: 0.75rem 1.25rem;
                padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
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
                text-decoration: none;
            }
            
            .bottom-nav-item:active {
                transform: scale(0.9);
            }
            
            .bottom-nav-item.active {
                background: rgba(0, 107, 255, 0.1);
            }
            
            .bottom-nav-icon {
                width: 1.5rem;
                height: 1.5rem;
                color: #9CA3AF;
                transition: color 0.2s ease;
            }
            
            .bottom-nav-item.active .bottom-nav-icon {
                color: #006BFF;
            }
            
            .bottom-nav-label {
                font-size: 0.6875rem;
                color: #9CA3AF;
                font-weight: 500;
                transition: color 0.2s ease;
            }
            
            .bottom-nav-item.active .bottom-nav-label {
                color: #006BFF;
                font-weight: 600;
            }

            /* Hide desktop elements */
            .desktop-only {
                display: none !important;
            }
        
            /* Specific fixes for dashboard content on mobile */
            .dashboard-grid {
                grid-template-columns: 1fr !important;
                padding: 0 1rem; /* Compact padding */
                gap: 0.75rem !important; /* Tighter gap */
            }
            
            /* Aggressive Responsive Font Sizing for Mobile "App-like" feel */
            .text-3xl {
                font-size: 1.25rem !important; /* Significantly smaller */
                line-height: 1.5rem !important;
            }
            
            .text-2xl {
                font-size: 1.125rem !important;
                line-height: 1.35rem !important;
            }
            
            .text-xl {
                font-size: 1rem !important;
                line-height: 1.35rem !important;
            }
            
            .text-lg {
                font-size: 0.875rem !important;
                line-height: 1.25rem !important;
            }
            
            .text-base {
                font-size: 0.75rem !important;
                line-height: 1rem !important;
            }
            
            .text-sm {
                font-size: 0.7rem !important;
            }
            
            .text-xs {
                font-size: 0.65rem !important;
            }
            
            /* Compact Card Styling */
            .p-6 {
                padding: 0.75rem !important; /* Very compact padding */
            }
            
            .p-4 {
                padding: 0.75rem !important;
            }
            
            .gap-6 {
                gap: 0.75rem !important;
            }
            
            .gap-4 {
                gap: 0.5rem !important;
            }
            
            /* Adjust Charts for mobile */
            canvas {
                max-height: 180px !important; /* Shorter charts */
            }
            
            .mobile-chart-container {
                overflow-x: auto;
                padding-bottom: 0.5rem;
                margin-right: -1rem;
                padding-right: 1rem;
            }
            
            .mobile-padding {
                padding: 0 1rem;
                padding-bottom: 6rem !important; /* Ensure content is above nav */
            }
            
            /* Hide Sidebar ONLY - Show Header & Content */
            .sidebar-container,
            aside,
            nav.sidebar,
            [class*="sidebar"],
            .navigation-container > div:first-child {
                display: none !important;
                width: 0 !important;
            }
            
            /* Show desktop header but scaled down */
            header, 
            .dashboard-header {
                display: block !important;
                padding: 0.75rem !important;
            }
            
            /* Desktop specific large spacing - NOW VISIBLE BUT SCALED */
            .desktop-only {
                display: block !important; 
            }
            
            /* Specific Mobile Grid Layouts */
            .mobile-stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            
            /* Ensure other grids revert to single column if not specified */
            .grid-cols-1 {
                grid-template-columns: 1fr !important;
            }
            
            /* Center Business Health on Mobile */
            .business-health-card .flex-col {
                align-items: center !important;
                text-align: center;
            }
            
            .business-health-card .text-left {
                text-align: center !important;
            }
            
            /* Specific overrides for single column sections if needed */
            .mobile-full-width {
                grid-column: span 2 !important;
            }
            
            /* Reduce icon sizes */
            svg.w-10, .w-10 {
                width: 2rem !important;
                height: 2rem !important;
            }
            
            svg.w-8, .w-8 {
                width: 1.75rem !important;
                height: 1.75rem !important;
            }
            
            svg.w-6, .w-6 {
                width: 1.25rem !important;
                height: 1.25rem !important;
            } 
            
            /* Specific layout spacing */
            .mb-6 {
                margin-bottom: 0.75rem !important;
            }
            
            .space-x-6 > :not([hidden]) ~ :not([hidden]) {
                margin-left: 0.75rem !important;
            }

            /* Key Metrics Compatibility Styles */
            .mobile-kpi-section {
                padding: 0 1.25rem 1.5rem 1.25rem;
            }
            
            .mobile-kpi-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .mobile-kpi-card {
                background: white;
                border: 1px solid #F3F4F6;
                border-radius: 16px;
                padding: 1rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            }
            
            .kpi-icon {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 0.75rem;
            }
            
            .kpi-label {
                font-size: 0.75rem;
                color: #6B7280;
                font-weight: 500;
                margin-bottom: 0.25rem;
            }
            
            .kpi-value {
                font-size: 1.125rem;
                font-weight: 700;
                color: #1F2937;
                margin-bottom: 0.25rem;
            }
            
            .kpi-change {
                font-size: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
        }
        
        /* Desktop - hide mobile elements */
        @media (min-width: 768px) {
            .mobile-header,
            .quick-actions-section,
            .mobile-kpi-section,
            .bottom-nav,
            .mobile-only {
                display: none !important;
            }
        }
    </style>

    <div class="mobile-dashboard-content md:px-6 md:pb-6 md:pt-2" x-data="dashboardAnalytics()">
        
        <!-- Mobile Content Container -->
        <div class="mobile-padding pt-8 md:pt-0">
        
        <!-- ==================== BUSINESS HEALTH SCORE ==================== -->
        <div class="mt-4 mb-6 desktop-only business-health-card">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <svg class="w-28 h-28 transform -rotate-90">
                                <circle cx="56" cy="56" r="48" stroke="#E5E7EB" stroke-width="10" fill="none"></circle>
                                <circle cx="56" cy="56" r="48" stroke="{{ $businessHealthColor === 'green' ? '#10B981' : ($businessHealthColor === 'yellow' ? '#F59E0B' : '#EF4444') }}" stroke-width="10" fill="none" 
                                    stroke-dasharray="{{ 301.59 * ($businessHealthScore / 100) }} 301.59"
                                    stroke-linecap="round"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold text-gray-900">{{ $businessHealthScore }}</span>
                                <span class="text-xs text-gray-500">/100</span>
                            </div>
                        </div>
                        <div class="text-left">
                            <h2 class="text-xl font-bold text-gray-900">Business Health Score</h2>
                            <p class="text-base font-semibold {{ $businessHealthColor === 'green' ? 'text-green-600' : ($businessHealthColor === 'yellow' ? 'text-yellow-600' : 'text-red-600') }} mt-1">{{ $businessHealthStatus }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($businessHealthScore >= 70)
                                    Bisnis Anda dalam kondisi baik.
                                @elseif($businessHealthScore >= 40)
                                    Ada area yang perlu diperbaiki.
                                @else
                                    Perlu tindakan segera.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 w-full lg:w-auto">
                        @foreach($healthScores as $key => $score)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-base font-bold text-gray-900">{{ $score['score'] }}/{{ $score['max'] }}</div>
                            <div class="text-xs text-gray-500">{{ $score['label'] }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-1 mt-2">
                                <div class="{{ $businessHealthColor === 'green' ? 'bg-green-500' : ($businessHealthColor === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }} h-1 rounded-full" style="width: {{ ($score['score'] / $score['max']) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== SUGGESTED ACTIONS ==================== -->
        @if(count($suggestedActions) > 0)
        <div class="mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Rekomendasi Aksi</h3>
                    <span class="text-xs bg-[#006BFF] text-white px-2 py-1 rounded-full">{{ count($suggestedActions) }} Prioritas</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($suggestedActions as $action)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 {{ $action['color'] === 'red' ? 'bg-red-100' : ($action['color'] === 'yellow' ? 'bg-yellow-100' : ($action['color'] === 'green' ? 'bg-green-100' : ($action['color'] === 'orange' ? 'bg-orange-100' : 'bg-blue-100'))) }} rounded-lg flex items-center justify-center">
                                @if($action['icon'] == 'package')
                                <svg class="w-5 h-5 {{ $action['color'] === 'red' ? 'text-red-600' : ($action['color'] === 'yellow' ? 'text-yellow-600' : ($action['color'] === 'green' ? 'text-green-600' : ($action['color'] === 'orange' ? 'text-orange-600' : 'text-blue-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                @elseif($action['icon'] == 'tag')
                                <svg class="w-5 h-5 {{ $action['color'] === 'red' ? 'text-red-600' : ($action['color'] === 'yellow' ? 'text-yellow-600' : ($action['color'] === 'green' ? 'text-green-600' : ($action['color'] === 'orange' ? 'text-orange-600' : 'text-blue-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                @elseif($action['icon'] == 'gift')
                                <svg class="w-5 h-5 {{ $action['color'] === 'red' ? 'text-red-600' : ($action['color'] === 'yellow' ? 'text-yellow-600' : ($action['color'] === 'green' ? 'text-green-600' : ($action['color'] === 'orange' ? 'text-orange-600' : 'text-blue-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                </svg>
                                @elseif($action['icon'] == 'trending-up')
                                <svg class="w-5 h-5 {{ $action['color'] === 'red' ? 'text-red-600' : ($action['color'] === 'yellow' ? 'text-yellow-600' : ($action['color'] === 'green' ? 'text-green-600' : ($action['color'] === 'orange' ? 'text-orange-600' : 'text-blue-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                @elseif($action['icon'] == 'shopping-cart')
                                <svg class="w-5 h-5 {{ $action['color'] === 'red' ? 'text-red-600' : ($action['color'] === 'yellow' ? 'text-yellow-600' : ($action['color'] === 'green' ? 'text-green-600' : ($action['color'] === 'orange' ? 'text-orange-600' : 'text-blue-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                @else
                                <svg class="w-5 h-5 {{ $action['color'] === 'red' ? 'text-red-600' : ($action['color'] === 'yellow' ? 'text-yellow-600' : ($action['color'] === 'green' ? 'text-green-600' : ($action['color'] === 'orange' ? 'text-orange-600' : 'text-blue-600'))) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-medium uppercase {{ $action['priority'] === 'high' ? 'text-red-600' : ($action['priority'] === 'medium' ? 'text-yellow-600' : 'text-gray-500') }}">
                                    {{ $action['priority'] === 'high' ? 'Prioritas Tinggi' : ($action['priority'] === 'medium' ? 'Prioritas Sedang' : 'Saran') }}
                                </span>
                                <p class="text-sm text-gray-700 mt-1">{{ $action['message'] }}</p>
                                <a href="{{ $action['action_url'] }}" class="inline-flex items-center mt-2 text-sm font-medium text-[#006BFF] hover:text-[#006BFF]/80">
                                    {{ $action['action_text'] }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- ==================== KPI CARDS ROW 1 ==================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 mobile-stats-grid">
            <!-- Total Sales Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors cursor-pointer" onclick="window.location.href='{{ route('finance.sales.index') }}'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                        <p class="text-[#006BFF] text-sm mt-1">+{{ number_format($monthSales, 0, ',', '.') }} bulan ini</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-7 h-7 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Customers Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors cursor-pointer" onclick="window.location.href='{{ route('customers.index') }}'">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalCustomers) }}</p>
                        <p class="text-teal-600 text-sm mt-1">+{{ $newCustomersThisMonth }} baru bulan ini</p>
                        </div>
                    <div class="p-3 bg-teal-50 rounded-lg">
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Products Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors cursor-pointer" onclick="window.location.href='{{ route('inventory.products.index') }}'">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalProducts) }}</p>
                        <p class="text-violet-600 text-sm mt-1">{{ $stockHealthSummary['healthy_stock'] }} stok sehat</p>
                        </div>
                    <div class="p-3 bg-violet-50 rounded-lg">
                        <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Net Profit Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Profit Bulan Ini</p>
                        <p class="text-2xl font-bold {{ $monthProfit >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-1">Rp {{ number_format($monthProfit, 0, ',', '.') }}</p>
                        <p class="{{ $todayProfit >= 0 ? 'text-green-600' : 'text-red-600' }} text-sm mt-1">{{ $todayProfit >= 0 ? '+' : '' }}{{ number_format($todayProfit, 0, ',', '.') }} hari ini</p>
                    </div>
                    <div class="p-3 {{ $monthProfit >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                        <svg class="w-7 h-7 {{ $monthProfit >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($monthProfit >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            @endif
                                </svg>
                            </div>
                    </div>
                </div>
            </div>

        <!-- ==================== KPI CARDS ROW 2 ==================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 mobile-stats-grid">
                <!-- Total Expenses Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors cursor-pointer" onclick="window.location.href='{{ route('finance.expenses.index') }}'">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Pengeluaran</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</p>
                        <p class="text-rose-600 text-sm mt-1">Bulan ini</p>
                        </div>
                    <div class="p-3 bg-rose-50 rounded-lg">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Average Order Value -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Rata-rata Order</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($avgOrderValue ?? 0, 0, ',', '.') }}</p>
                        <p class="text-amber-600 text-sm mt-1">Per transaksi</p>
                        </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

            <!-- Transactions Today -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Transaksi Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayTransactions }}</p>
                        <p class="text-indigo-600 text-sm mt-1">{{ $weekTransactions }} minggu ini</p>
                        </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Growth Rate -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                        <p class="text-gray-500 text-sm font-medium">Pertumbuhan</p>
                        <p class="text-2xl font-bold {{ ($growthRate ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">{{ number_format($growthRate ?? 0, 1) }}%</p>
                        <p class="text-gray-500 text-sm mt-1">vs bulan lalu</p>
                    </div>
                    <div class="p-3 {{ ($growthRate ?? 0) >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                        <svg class="w-7 h-7 {{ ($growthRate ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if(($growthRate ?? 0) >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            @endif
                                </svg>
                            </div>
                </div>
            </div>
        </div>

        <!-- ==================== CASHFLOW & SALES TREND ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Sales Trend Chart -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-semibold text-gray-900">Sales Trend</h3>
                    <div class="flex space-x-2">
                        <button @click="updateChartPeriod('all')" :class="chartPeriod === 'all' ? 'bg-[#006BFF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 text-xs rounded-lg transition-colors font-medium">All Time</button>
                        <button @click="updateChartPeriod('90d')" :class="chartPeriod === '90d' ? 'bg-[#006BFF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 text-xs rounded-lg transition-colors font-medium">3 Bulan</button>
                        <button @click="updateChartPeriod('30d')" :class="chartPeriod === '30d' ? 'bg-[#006BFF] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 text-xs rounded-lg transition-colors font-medium">1 Bulan</button>
                    </div>
                </div>
                <div class="h-72" x-show="!chartLoading">
                    <canvas id="salesChart"></canvas>
                            </div>
                <div x-show="chartLoading" class="h-72 flex items-center justify-center">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-5 w-5 border-2 border-[#006BFF] border-t-transparent"></div>
                        <span class="text-gray-500 text-sm">Memuat data...</span>
                    </div>
                </div>
            </div>

            <!-- Profit Summary -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Ringkasan Profit</h3>
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Profit Hari Ini</p>
                        <p class="text-xl font-bold {{ $profitSummary['today'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($profitSummary['today'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Profit Minggu Ini</p>
                        <p class="text-xl font-bold {{ $profitSummary['this_week'] >= 0 ? 'text-[#006BFF]' : 'text-red-600' }}">
                            Rp {{ number_format($profitSummary['this_week'], 0, ',', '.') }}
                        </p>
                        </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Profit Bulan Ini</p>
                        <p class="text-xl font-bold {{ $profitSummary['this_month'] >= 0 ? 'text-violet-600' : 'text-red-600' }}">
                            Rp {{ number_format($profitSummary['this_month'], 0, ',', '.') }}
                        </p>
                    </div>
                    @if($biggestExpenseCategory)
                    <div class="bg-rose-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Pengeluaran Terbesar</p>
                        <p class="text-base font-bold text-rose-600">{{ $biggestExpenseCategory['category'] }}</p>
                        <p class="text-sm text-rose-500">Rp {{ number_format($biggestExpenseCategory['total'], 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ==================== PRODUCT ANALYTICS ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Best Sellers -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Top 5 Best Sellers</h3>
                    <a href="{{ route('inventory.products.index') }}" class="text-sm text-[#006BFF] hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($topProducts as $index => $product)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-medium text-sm truncate">{{ $product->name }}</p>
                            <p class="text-gray-500 text-xs">{{ $product->total_sold ?? 0 }} terjual</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[#006BFF] font-semibold text-sm">Rp {{ number_format($product->total_revenue ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Belum ada data penjualan</p>
                    @endforelse
                    </div>
                </div>

            <!-- Dead Stock -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Dead Stock</h3>
                </div>
                <div class="space-y-3">
                    @forelse($deadStockProducts->take(5) as $product)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-medium text-sm truncate">{{ $product->name }}</p>
                            <p class="text-gray-500 text-xs">{{ $product->days_without_sale ?? 0 }} hari tanpa penjualan</p>
                            </div>
                            <div class="text-right">
                            <p class="text-red-600 font-semibold text-sm">{{ $product->stock_quantity }} unit</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Tidak ada dead stock</p>
                    @endforelse
                    </div>
                </div>
            </div>

        <!-- ==================== STOCK HEALTH INDICATOR ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Stock Health Overview -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-6">Kesehatan Stok</h3>
                    
                <!-- Stock Health Bars -->
                <div class="space-y-5">
                    <!-- Stok Sehat -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-700">Stok Sehat</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ $stockHealthSummary['healthy_stock'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $stockHealthSummary['total_products'] > 0 ? ($stockHealthSummary['healthy_stock'] / $stockHealthSummary['total_products']) * 100 : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stockHealthSummary['total_products'] > 0 ? number_format(($stockHealthSummary['healthy_stock'] / $stockHealthSummary['total_products']) * 100, 1) : 0 }}% dari total produk</p>
                                </div>

                    <!-- Stok Rendah -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-700">Stok Rendah</span>
                            </div>
                            <span class="text-lg font-bold text-yellow-600">{{ $stockHealthSummary['low_stock'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500" style="width: {{ $stockHealthSummary['total_products'] > 0 ? ($stockHealthSummary['low_stock'] / $stockHealthSummary['total_products']) * 100 : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stockHealthSummary['total_products'] > 0 ? number_format(($stockHealthSummary['low_stock'] / $stockHealthSummary['total_products']) * 100, 1) : 0 }}% dari total produk</p>
                    </div>

                    <!-- Habis -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-700">Habis</span>
                            </div>
                            <span class="text-lg font-bold text-red-600">{{ $stockHealthSummary['out_of_stock'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-red-500 h-3 rounded-full transition-all duration-500" style="width: {{ $stockHealthSummary['total_products'] > 0 ? ($stockHealthSummary['out_of_stock'] / $stockHealthSummary['total_products']) * 100 : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stockHealthSummary['total_products'] > 0 ? number_format(($stockHealthSummary['out_of_stock'] / $stockHealthSummary['total_products']) * 100, 1) : 0 }}% dari total produk</p>
                                </div>

                    <!-- Dead Stock -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-700">Dead Stock</span>
                            </div>
                            <span class="text-lg font-bold text-gray-600">{{ $stockHealthSummary['dead_stock_count'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gray-500 h-3 rounded-full transition-all duration-500" style="width: {{ $stockHealthSummary['total_products'] > 0 ? ($stockHealthSummary['dead_stock_count'] / $stockHealthSummary['total_products']) * 100 : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stockHealthSummary['total_products'] > 0 ? number_format(($stockHealthSummary['dead_stock_count'] / $stockHealthSummary['total_products']) * 100, 1) : 0 }}% dari total produk</p>
                    </div>
                </div>

                <!-- Total Summary -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Total Produk</span>
                        <span class="text-xl font-bold text-[#006BFF]">{{ $stockHealthSummary['total_products'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Fast Moving Products -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Fast Moving - Perlu Restock</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($fastMovingProducts->take(5) as $product)
                    <div class="p-3 {{ $product->days_until_stockout < 7 ? 'bg-red-50 border border-red-200' : 'bg-amber-50' }} rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-gray-900 font-medium text-sm truncate">{{ $product->name }}</p>
                            @if($product->days_until_stockout < 7)
                            <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">URGENT</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $product->daily_avg_sales }}/hari • Stok: {{ $product->stock_quantity }}</span>
                            <span class="{{ $product->days_until_stockout < 7 ? 'text-red-600 font-bold' : 'text-amber-600' }}">
                                {{ $product->days_until_stockout < 999 ? $product->days_until_stockout . ' hari lagi' : 'Stabil' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Tidak ada produk fast moving</p>
                    @endforelse
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Low Stock Alert</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($lowStockProducts as $product)
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-gray-900 font-medium text-sm truncate">{{ $product->name }}</p>
                            <span class="text-sm font-bold {{ $product->stock_quantity <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>Min: {{ $product->min_stock_level }}</span>
                            <a href="{{ route('inventory.products.edit', $product->id) }}" class="text-[#006BFF] hover:underline">Restock</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Semua stok aman</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ==================== SALES PATTERNS ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Busiest Days -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Hari Paling Ramai</h3>
                <div class="h-56">
                    <canvas id="busiestDaysChart"></canvas>
                </div>
                @if($busiestDays->count() > 0)
                <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold text-[#006BFF]">{{ $busiestDays->sortByDesc('total_transactions')->first()->day_name }}</span> 
                        adalah hari tersibuk dengan {{ $busiestDays->sortByDesc('total_transactions')->first()->total_transactions }} transaksi
                    </p>
                </div>
                @endif
                </div>

            <!-- Busiest Hours -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Jam Paling Ramai</h3>
                @if($busiestHours->count() > 0)
                <div class="h-56">
                    <canvas id="busiestHoursChart"></canvas>
                </div>
                @php $peakHour = $busiestHours->sortByDesc('transactions')->first(); @endphp
                <div class="mt-3 p-3 bg-violet-50 rounded-lg">
                    <p class="text-sm text-gray-700">
                        Jam <span class="font-semibold text-violet-600">{{ $peakHour['label'] }}</span> adalah waktu tersibuk
                    </p>
                </div>
                @else
                <div class="h-56 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada data transaksi</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ==================== PROFIT MARGIN & CUSTOMER ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Most Profitable -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Produk Paling Untung</h3>
                <div class="space-y-3">
                    @forelse($mostProfitableProducts as $product)
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-gray-900 font-medium text-sm truncate">{{ $product->name }}</p>
                        <div class="flex items-center justify-between mt-1 text-xs">
                            <span class="text-gray-500">{{ $product->total_sold }} terjual</span>
                            <span class="text-green-600 font-bold">+Rp {{ number_format($product->total_profit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Belum ada data</p>
                    @endforelse
                    </div>
                </div>

            <!-- Customer Type -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Pelanggan Baru vs Repeat</h3>
                <div class="h-40 mb-4">
                    <canvas id="customerTypeChart"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <p class="text-xl font-bold text-[#006BFF]">{{ $newCustomersThisMonth }}</p>
                        <p class="text-xs text-gray-600">Pelanggan Baru</p>
                    </div>
                    <div class="text-center p-3 bg-violet-50 rounded-lg">
                        <p class="text-xl font-bold text-violet-600">{{ $repeatCustomers }}</p>
                        <p class="text-xs text-gray-600">Repeat Customer</p>
                    </div>
                </div>
            </div>

            <!-- Top Loyal Customers -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Pelanggan Loyal</h3>
                <div class="space-y-3">
                    @forelse($loyalCustomers as $index => $customer)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-violet-100 text-violet-700 rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-medium text-sm truncate">{{ $customer->name }}</p>
                            <p class="text-gray-500 text-xs">{{ $customer->purchase_frequency }} transaksi</p>
                        </div>
                        <div class="text-right">
                            <p class="text-violet-600 font-semibold text-sm">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Belum ada pelanggan loyal</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ==================== MONTHLY PERFORMANCE & EXPENSE ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Monthly Performance (12 Months) -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Performa Bulanan (12 Bulan Terakhir)</h3>
                    @if($strongestMonth)
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                        Terbaik: {{ $strongestMonth->month_name }}
                    </span>
                    @endif
                </div>
                @if($monthlyPerformance->count() > 0)
                <div class="h-64">
                    <canvas id="monthlyPerformanceChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="text-center p-2 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-600">Total Penjualan</p>
                        <p class="text-sm font-bold text-[#006BFF]">Rp {{ number_format($monthlyPerformance->sum('sales'), 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-2 bg-violet-50 rounded-lg">
                        <p class="text-xs text-gray-600">Total Transaksi</p>
                        <p class="text-sm font-bold text-violet-600">{{ number_format($monthlyPerformance->sum('transactions')) }}</p>
                    </div>
                    <div class="text-center p-2 bg-teal-50 rounded-lg">
                        <p class="text-xs text-gray-600">Rata-rata/Bulan</p>
                        <p class="text-sm font-bold text-teal-600">Rp {{ number_format($monthlyPerformance->avg('sales'), 0, ',', '.') }}</p>
                    </div>
                </div>
                @else
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada data penjualan</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Expense Breakdown -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Pengeluaran Bulan Ini</h3>
                @if($expenseByCategory->count() > 0)
                <div class="h-56">
                    <canvas id="expenseBreakdownChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($expenseByCategory->take(3) as $expense)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">{{ $expense['category'] }}</span>
                        <span class="text-sm font-semibold text-red-600">Rp {{ number_format($expense['total'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="h-56 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada pengeluaran bulan ini</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ==================== RECENT ACTIVITY & QUICK ACTIONS ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Activity -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Aktivitas Terbaru</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-green-600 text-xs font-medium">Live</span>
                    </div>
                </div>
                <div class="space-y-3 max-h-72 overflow-y-auto">
                        @foreach($recentSales->take(5) as $sale)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-9 h-9 bg-blue-50 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 text-sm font-medium">{{ $sale->invoice_number }}</p>
                            <p class="text-gray-500 text-xs">{{ $sale->customer->name ?? 'Guest' }} • Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                            </div>
                        <span class="text-gray-400 text-xs">{{ $sale->created_at->diffForHumans() }}</span>
                        </div>
                        @endforeach
                        
                        @if($lowStockProducts->count() > 0)
                    <div class="flex items-center space-x-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                            <p class="text-yellow-800 text-sm font-medium">Low Stock Alert</p>
                            <p class="text-yellow-600 text-xs">{{ $lowStockProducts->count() }} produk perlu restock</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('finance.sales.create') }}" class="flex items-center space-x-3 p-4 bg-gray-50 hover:bg-blue-50 rounded-lg border border-gray-200 hover:border-[#006BFF] transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                        <span class="text-sm font-medium text-gray-900">Penjualan Baru</span>
                        </a>
                        
                    <a href="{{ route('inventory.products.create') }}" class="flex items-center space-x-3 p-4 bg-gray-50 hover:bg-violet-50 rounded-lg border border-gray-200 hover:border-violet-500 transition-colors">
                        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        <span class="text-sm font-medium text-gray-900">Tambah Produk</span>
                        </a>
                        
                    <a href="{{ route('customers.create') }}" class="flex items-center space-x-3 p-4 bg-gray-50 hover:bg-teal-50 rounded-lg border border-gray-200 hover:border-teal-500 transition-colors">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        <span class="text-sm font-medium text-gray-900">Pelanggan Baru</span>
                        </a>
                        
                    <a href="{{ route('finance.expenses.create') }}" class="flex items-center space-x-3 p-4 bg-gray-50 hover:bg-rose-50 rounded-lg border border-gray-200 hover:border-rose-500 transition-colors">
                        <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        <span class="text-sm font-medium text-gray-900">Catat Pengeluaran</span>
                        </a>
                    </div>
                </div>
            </div>

    </div><!-- End mobile-padding -->
    </div><!-- End mobile-dashboard-content -->

    <!-- Financial Chart Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                                    label: function(context) {
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
                            x: {
                                display: false,
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                display: false,
                                grid: {
                                    display: false
                                },
                                min: 0
                            }
                        }
                    }
                });
            }
        });
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.color = '#6B7280';
        Chart.defaults.borderColor = '#E5E7EB';
        Chart.defaults.font.family = 'Inter, sans-serif';

        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('salesChart')?.getContext('2d');
            if (salesCtx) {
                window.salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesTrend->pluck('date')),
                datasets: [{
                    label: 'Sales',
                    data: @json($salesTrend->pluck('total')),
                    borderColor: '#006BFF',
                            backgroundColor: 'rgba(0, 107, 255, 0.08)',
                            borderWidth: 2,
                    fill: true,
                            tension: 0.3,
                    pointBackgroundColor: '#006BFF',
                            pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                            legend: { display: false },
                    tooltip: {
                                backgroundColor: '#1F2937',
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                }
                    }
                },
                scales: {
                            x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                        }
                    }
                });
            }

            // Busiest Days Chart
            const busiestDaysCtx = document.getElementById('busiestDaysChart')?.getContext('2d');
            if (busiestDaysCtx) {
                new Chart(busiestDaysCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($busiestDays->pluck('day_name')),
                        datasets: [{
                            label: 'Transaksi',
                            data: @json($busiestDays->pluck('total_transactions')),
                            backgroundColor: '#006BFF',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                    }
                });
            }

            // Busiest Hours Chart
            const busiestHoursCtx = document.getElementById('busiestHoursChart')?.getContext('2d');
            if (busiestHoursCtx) {
                const busiestHoursData = @json($busiestHours->pluck('transactions'));
                if (busiestHoursData.length > 0) {
                    new Chart(busiestHoursCtx, {
                        type: 'bar',
            data: {
                            labels: @json($busiestHours->pluck('label')),
                datasets: [{
                                label: 'Transaksi',
                                data: busiestHoursData,
                                backgroundColor: '#8B5CF6',
                                borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } }, y: { beginAtZero: true } }
                        }
                    });
                }
            }

            // Monthly Performance Chart (12 Months)
            const monthlyPerfCtx = document.getElementById('monthlyPerformanceChart')?.getContext('2d');
            if (monthlyPerfCtx) {
                const monthlyData = @json($monthlyPerformance->pluck('sales'));
                if (monthlyData.length > 0) {
                    new Chart(monthlyPerfCtx, {
            type: 'bar',
            data: {
                            labels: @json($monthlyPerformance->pluck('month')),
                datasets: [{
                                label: 'Penjualan',
                                data: monthlyData,
                                backgroundColor: '#006BFF',
                                borderRadius: 6,
                                barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                                legend: { display: false },
                    tooltip: {
                                    backgroundColor: '#1F2937',
                                    padding: 10,
                        callbacks: {
                            label: function(context) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                                    grid: { display: false },
                        ticks: {
                                        maxRotation: 45, 
                                        minRotation: 45,
                                        font: { size: 11 }
                        }
                    },
                    y: {
                                    beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });
                }
            }

            // Expense Breakdown Chart
            const expenseCtx = document.getElementById('expenseBreakdownChart')?.getContext('2d');
            if (expenseCtx) {
                const expenseData = @json($expenseByCategory->pluck('total'));
                if (expenseData.length > 0) {
                    new Chart(expenseCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($expenseByCategory->pluck('category')),
                            datasets: [{
                                data: expenseData,
                                backgroundColor: ['#EF4444', '#F59E0B', '#10B981', '#006BFF', '#8B5CF6', '#EC4899'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, font: { size: 11 } } },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                        }
                                    }
                                }
                            },
                            cutout: '55%'
                        }
                    });
                }
            }

            // Customer Type Chart
            const customerTypeCtx = document.getElementById('customerTypeChart')?.getContext('2d');
            if (customerTypeCtx) {
                new Chart(customerTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Baru', 'Repeat'],
                        datasets: [{
                            data: [{{ $newCustomersThisMonth }}, {{ $repeatCustomers }}],
                            backgroundColor: ['#006BFF', '#8B5CF6'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        cutout: '65%'
            }
                });
            }
        }

        // Alpine.js Dashboard Component
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardAnalytics', () => ({
                chartPeriod: 'all',
                chartLoading: false,

                async updateChartPeriod(period) {
                    if (this.chartPeriod === period) return;
                    this.chartPeriod = period;
                    this.chartLoading = true;

                    try {
                        let days = 9999;
                        if (period !== 'all') {
                            days = parseInt(period.replace('d', ''));
                        }

                        const response = await fetch(`/analytics/sales-chart?period=${days}`);
                        const data = await response.json();

                        if (window.salesChart) {
                            window.salesChart.data.labels = data.map(item => item.date);
                            window.salesChart.data.datasets[0].data = data.map(item => item.total);
                            window.salesChart.update('none');
                        }
                    } catch (error) {
                        console.error('Error updating chart:', error);
                    } finally {
                        this.chartLoading = false;
                    }
                }
            }));
        });
    </script>
    @endpush

    <!-- Bottom Navigation (Mobile Only) -->
    <!-- Bottom Navigation (Mobile Only) -->
    <div class="bottom-nav mobile-only">
        <a href="{{ route('dashboard') }}" class="bottom-nav-item active">
            <svg class="bottom-nav-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path>
            </svg>
            <span class="bottom-nav-label">Home</span>
        </a>
        <a href="{{ route('finance.sales.index') }}" class="bottom-nav-item">
            <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="bottom-nav-label">Activity</span>
        </a>
        <a href="{{ route('analytics') }}" class="bottom-nav-item">
            <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <span class="bottom-nav-label">Assistant</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="bottom-nav-item">
            <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="bottom-nav-label">Settings</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="bottom-nav-item">
            <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="bottom-nav-label">Profile</span>
        </a>
    </div>
</x-app-layout>
