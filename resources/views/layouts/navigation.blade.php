<!-- Sidebar Navigation -->
<div x-data="navigationComponent()" 
     x-init="init()"
     class="flex h-screen navigation-container"
     :class="sidebarExpanded ? '' : 'sidebar-collapsed'"
     style="contain: layout style; background-color: #0A0A0F;">
    <!-- Sidebar -->
    <div :class="sidebarExpanded ? 'w-64' : 'w-16'" 
         class="sidebar-transition flex flex-col relative z-[1000] sidebar-container"
         style="background-color: #0E0E16; border-right: 1px solid #2A2A3A;">
        
        <!-- Logo Section -->
        <div class="flex items-center justify-center h-16" style="border-bottom: 1px solid #2A2A3A;">
            <div class="flex items-center space-x-3">
                @php
                    $sidebarProfile = Auth::user()->businessProfile;
                    $sidebarLogo = $sidebarProfile && $sidebarProfile->business_logo_url 
                        ? $sidebarProfile->business_logo_url
                        : asset('images/D-BOOST.png');
                @endphp
                
                <img src="{{ $sidebarLogo }}" 
                     alt="Logo" 
                     class="w-8 h-8 rounded-lg object-cover shadow-sm ring-1 ring-gray-700/50">
                     
                <div x-show="sidebarExpanded" x-transition class="flex flex-col">
                    <span class="font-bold text-base leading-tight tracking-tight" style="color: #F1F0FF;">D-BOOST</span>
                    <span class="text-[#A78BFA] font-semibold text-[10px] uppercase tracking-wider leading-none">Partners</span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-2 py-4 space-y-2">
            @php
                $navigationService = app(\App\Services\NavigationService::class);
                $currentRoute = request()->route()->getName();
                $currentModule = $navigationService->getCurrentModule($currentRoute);
                
                // If we're in a specific module, show only that module's menus
                if ($currentModule) {
                    $moduleMenus = $navigationService->getModuleNavigation(Auth::user(), $currentModule);
                } else {
                    // Fallback to all navigation for settings or other pages
                    $navigation = $navigationService->getNavigationForUser(Auth::user());
                }
            @endphp
            
            @if($currentModule)
                <!-- Current Module Header -->
                <div x-show="sidebarExpanded" x-transition class="px-2 py-2 mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" 
                             style="background: linear-gradient(135deg, {{ $currentModule->color }}20, {{ $currentModule->color }}10);">
                            @if($currentModule->icon)
                                <svg class="w-4 h-4" style="color: {{ $currentModule->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentModule->icon }}"></path>
                                </svg>
                            @else
                                <div class="w-4 h-4 rounded flex items-center justify-center text-white font-bold text-xs"
                                     style="background-color: {{ $currentModule->color }}">
                                    {{ substr($currentModule->display_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white">{{ $currentModule->display_name }}</div>
                            <div class="text-xs text-gray-400">Module</div>
                        </div>
                    </div>
                </div>

                <!-- Back to Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center text-sm font-medium rounded-lg transition-all duration-200 text-[#A9A8C3] hover:bg-[#1A1A24] hover:text-[#A78BFA] mb-4"
                   :class="sidebarExpanded ? 'px-2 py-3 justify-start' : 'px-0 py-3 justify-center'">
                    <div class="flex items-center justify-center" :class="sidebarExpanded ? 'w-6 h-6' : 'w-full'">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarExpanded" x-transition class="ml-3">Back to Modules</span>
                </a>

                <!-- Module Menus -->
                @foreach($moduleMenus as $menu)
                    @php
                        $isActive = request()->routeIs($menu->route . '*') || 
                                   ($menu->route && request()->routeIs($menu->route));
                        $activeClass = $isActive ? 'bg-[#A78BFA]/15 text-[#A78BFA] border border-[#A78BFA]/25' : 'text-[#A9A8C3] hover:bg-[#1A1A24] hover:text-[#A78BFA]';
                    @endphp
                    
                    <a href="{{ $menu->getUrl() }}" 
                       class="group flex items-center text-sm font-medium rounded-lg transition-all duration-200 {{ $activeClass }}"
                       :class="sidebarExpanded ? 'px-2 py-3 justify-start' : 'px-0 py-3 justify-center'">
                        <div class="flex items-center justify-center" :class="sidebarExpanded ? 'w-6 h-6' : 'w-full'">
                            @if($menu->icon)
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon }}"></path>
                                </svg>
                            @else
                                <div class="w-6 h-6 flex-shrink-0 rounded flex items-center justify-center" style="background-color: #2A2A3A;">
                                    <span class="text-xs font-bold text-[#A9A8C3]">{{ substr($menu->display_name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <span x-show="sidebarExpanded" x-transition class="ml-3">{{ $menu->display_name }}</span>
                    </a>
                @endforeach
            @else
                <!-- Settings or Other Pages Navigation -->
                @if(isset($navigation))
                    @foreach($navigation as $moduleData)
                        @php
                            $module = $moduleData['module'];
                            $menus = $moduleData['menus'];
                        @endphp
                        
                        <!-- Module Section -->
                        @if($menus->count() > 1)
                            <div x-show="sidebarExpanded" x-transition class="px-2 py-2">
                                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #6B6A8A;">
                                    {{ $module->display_name }}
                                </div>
                            </div>
                        @endif
                        
                        <!-- Module Menus -->
                        @foreach($menus as $menu)
                            @php
                                $isActive = request()->routeIs($menu->route . '*') || 
                                           ($menu->route && request()->routeIs($menu->route));
                                $activeClass = $isActive ? 'bg-[#A78BFA]/15 text-[#A78BFA] border border-[#A78BFA]/25' : 'text-[#A9A8C3] hover:bg-[#1A1A24] hover:text-[#A78BFA]';
                            @endphp
                            
                            <a href="{{ $menu->getUrl() }}" 
                               class="group flex items-center text-sm font-medium rounded-lg transition-all duration-200 {{ $activeClass }}"
                               :class="sidebarExpanded ? 'px-2 py-3 justify-start' : 'px-0 py-3 justify-center'">
                                <div class="flex items-center justify-center" :class="sidebarExpanded ? 'w-6 h-6' : 'w-full'">
                                    @if($menu->icon)
                                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon }}"></path>
                                        </svg>
                                    @else
                                        <div class="w-6 h-6 flex-shrink-0 rounded flex items-center justify-center" style="background-color: #2A2A3A;">
                                            <span class="text-xs font-bold text-[#A9A8C3]">{{ substr($menu->display_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <span x-show="sidebarExpanded" x-transition class="ml-3">{{ $menu->display_name }}</span>
                            </a>
                        @endforeach
                    @endforeach
                @endif
                
                <!-- Settings Menu (Super Admin only) -->
                @if(Auth::user()->isSuperAdmin())
                    <div x-show="sidebarExpanded" x-transition class="px-2 py-2 mt-4">
                        <div class="text-xs font-semibold uppercase tracking-wider" style="color: #6B6A8A;">
                            System
                        </div>
                    </div>
                    
                    <a href="{{ route('settings.modules.index') }}" 
                       class="group flex items-center text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-[#A78BFA]/15 text-[#A78BFA] border border-[#A78BFA]/25' : 'text-[#A9A8C3] hover:bg-[#1A1A24] hover:text-[#A78BFA]' }}"
                       :class="sidebarExpanded ? 'px-2 py-3 justify-start' : 'px-0 py-3 justify-center'">
                        <div class="flex items-center justify-center" :class="sidebarExpanded ? 'w-6 h-6' : 'w-full'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span x-show="sidebarExpanded" x-transition class="ml-3">Settings</span>
                    </a>
                @endif
            @endif
        </nav>

        <!-- User Section -->
        <div style="border-top: 1px solid #2A2A3A;" :class="sidebarExpanded ? 'p-4' : 'p-2'">
            <div x-data="userMenuComponent()" class="relative">
                <button @click="toggleUserMenu()" 
                        class="w-full flex items-center text-sm font-medium text-[#A9A8C3] rounded-lg hover:bg-[#1A1A24] hover:text-[#F1F0FF] transition-all duration-200"
                        :class="sidebarExpanded ? 'px-2 py-3 justify-start' : 'px-0 py-3 justify-center'">
                    <div class="flex items-center justify-center" :class="sidebarExpanded ? 'w-8 h-8' : 'w-full'">
                        <div class="w-8 h-8 bg-gradient-to-br from-[#A78BFA] to-[#7C3AED] rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div x-show="sidebarExpanded" x-transition class="ml-3 flex-1 text-left">
                        <div class="text-sm font-medium" style="color: #F1F0FF;">{{ Auth::user()->name }}</div>
                        <div class="text-xs" style="color: #6B6A8A;">{{ Auth::user()->getPrimaryRoleName() }}</div>
                    </div>
                    <svg x-show="sidebarExpanded" x-transition class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- User Dropdown - Dynamic positioning -->
                <div x-show="userMenuOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute backdrop-blur-sm rounded-lg shadow-2xl user-dropdown"
                     :class="sidebarExpanded ? 'bottom-full left-0 w-full mb-2' : 'fixed top-1/2 -translate-y-1/2 left-20 w-56'"
                     x-cloak
                     style="z-index: 9999 !important; background-color: #1A1A24; border: 1px solid #2A2A3A;">
                    <!-- User Info Header (only when collapsed) -->
                    <div x-show="!sidebarExpanded" class="px-4 py-3" style="border-bottom: 1px solid #2A2A3A;">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#A78BFA] to-[#7C3AED] rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate" style="color: #F1F0FF;">{{ Auth::user()->name }}</div>
                                <div class="text-xs" style="color: #6B6A8A;">{{ Auth::user()->getPrimaryRoleName() }}</div>
                                <div class="text-xs truncate" style="color: #6B6A8A;">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-[#A9A8C3] hover:bg-[#252535] hover:text-[#F1F0FF] transition-colors">
                            <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Profile Settings</span>
                        </a>
                        
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm text-[#A9A8C3] hover:bg-[#252535] hover:text-[#F1F0FF] transition-colors">
                            <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <div class="my-1" style="border-top: 1px solid #2A2A3A;"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition-colors">
                                <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
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
                    class="w-full flex items-center justify-center px-2 py-2 text-[#6B6A8A] hover:text-[#A78BFA] hover:bg-[#1A1A24] rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 transform transition-transform duration-200" 
                     :class="sidebarExpanded ? 'rotate-180' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden" style="background-color: #0A0A0F;">
        <!-- Top Header -->
        <header class="shadow-sm" style="background-color: #111118; border-bottom: 1px solid #2A2A3A;">
            <div class="px-6 py-4">
                @isset($header)
                    {{ $header }}
                @endisset
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto" style="background-color: #0A0A0F;">
            {{ $slot }}
        </main>
    </div>
</div>

<script>
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

                // Ensure proper positioning when opening in collapsed sidebar
                if (this.userMenuOpen) {
                    this.$nextTick(() => {
                        const dropdown = this.$el.querySelector('.user-dropdown');
                        const navigation = this.$el;

                        if (dropdown && navigation.classList.contains('sidebar-collapsed')) {
                            // Double-check positioning after render
                            const rect = dropdown.getBoundingClientRect();
                            const navRect = navigation.getBoundingClientRect();

                            // If popup is still inside sidebar area, force it outside
                            if (rect.left < navRect.right + 10) {
                                dropdown.style.left = (navRect.right + 10) + 'px';
                                dropdown.style.right = 'auto';
                            }
                        }
                    });
                }
            },

            closeUserMenu() {
                this.userMenuOpen = false;
            }
        }
    }
</script>
