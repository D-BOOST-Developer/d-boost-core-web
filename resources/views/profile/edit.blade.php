<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#F1F0FF] leading-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <!-- Custom Styles -->
    <style>
        .profile-container {
            background-color: #0A0A0F;
            min-height: calc(100vh - 80px);
        }
        
        .profile-header {
            background: linear-gradient(135deg, #1E1B2E 0%, #161626 50%, #111118 100%);
            border: 1px solid #2A2A3A;
            border-top: none;
            border-radius: 0 0 24px 24px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }
        
        .profile-card {
            background: #1A1A24;
            border: 1px solid #2A2A3A;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            border-color: rgba(167, 139, 250, 0.3);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        
        .section-tab {
            color: #A9A8C3;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            font-family: 'Space Grotesk', sans-serif;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .section-tab:hover {
            color: #A78BFA;
        }
        
        .section-tab.active {
            color: #A78BFA;
            border-bottom-color: #A78BFA;
            background: rgba(167, 139, 250, 0.08);
        }
        
        .input-field {
            background: #111118;
            border: 1px solid #2A2A3A;
            color: #F1F0FF;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.9375rem;
        }

        .input-field option {
            background-color: #111118;
            color: #F1F0FF;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #A78BFA;
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #A78BFA 0%, #7C3AED 100%);
            color: #0A0A0F;
            font-weight: 700;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(167, 139, 250, 0.4);
            color: #000;
        }
        
        .btn-secondary {
            background: #111118;
            color: #A9A8C3;
            font-weight: 500;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid #2A2A3A;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background: #252535;
            color: #F1F0FF;
            border-color: #3A3A4E;
        }
        
        .upload-zone {
            border: 2px dashed #2A2A3A;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #111118;
            color: #A9A8C3;
        }
        
        .upload-zone:hover {
            border-color: #A78BFA;
            background: #161622;
        }
        
        .upload-zone.dragover {
            border-color: #A78BFA;
            background: #1E1B2E;
        }
        
        .avatar-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #A78BFA 0%, #7C3AED 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #1A1A24;
            box-shadow: 0 4px 14px rgba(167, 139, 250, 0.3);
        }
        
        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #A9A8C3;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.375rem;
        }
        
        .info-value {
            font-size: 0.9375rem;
            color: #F1F0FF;
            font-weight: 500;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-primary {
            background: rgba(167, 139, 250, 0.15);
            color: #A78BFA;
            border: 1px solid rgba(167, 139, 250, 0.3);
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .danger-zone {
            background: #1A1A24;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .maps-preview {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #2A2A3A;
        }
        
        /* Success toast */
        .toast-success {
            animation: slideInRight 0.5s ease-out, fadeOut 0.5s ease-in 2.5s forwards;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>

    <div class="profile-container py-6" x-data="profilePage()">
        <!-- Success Toasts -->
        @if (session('status'))
            <div class="fixed top-20 right-6 z-50 toast-success">
                <div class="bg-[#162D24] border border-emerald-500/40 text-emerald-300 px-6 py-4 rounded-xl shadow-xl shadow-emerald-950/50 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">
                        @switch(session('status'))
                            @case('profile-updated')
                                Profile updated successfully!
                                @break
                            @case('business-profile-updated')
                                Business profile updated successfully!
                                @break
                            @case('logo-updated')
                                Business logo updated successfully!
                                @break
                            @case('qris-updated')
                                QRIS barcode updated successfully!
                                @break
                            @case('password-updated')
                                Password updated successfully!
                                @break
                            @default
                                Changes saved successfully!
                        @endswitch
                    </span>
                </div>
            </div>
        @endif

        <div class="w-full px-2 sm:px-3 lg:px-4">
            <!-- Profile Header -->
            <div class="profile-header p-8 mb-6 relative">
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                    <!-- Avatar -->
                    <div class="avatar-container relative">
                        @if($businessProfile && $businessProfile->business_logo_url)
                            <img src="{{ $businessProfile->business_logo_url }}" alt="Business Logo" class="w-full h-full object-cover rounded-full">
                        @else
                            <span class="text-[#0A0A0F] text-4xl font-extrabold">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                        <button @click="activeTab = 'business'" class="absolute bottom-0 right-0 w-8 h-8 bg-[#1A1A24] border border-[#2A2A3A] rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- User Info -->
                    <div class="text-center md:text-left text-white">
                        @if($businessProfile)
                        <h1 class="text-2xl md:text-3xl font-bold mb-1 text-[#F1F0FF]">{{ $businessProfile->business_name }}</h1>
                            <p class="text-[#A9A8C3] text-lg mb-2">{{ $user->name }}</p>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                                <span class="badge" style="background: rgba(167, 139, 250, 0.2); color: #C4B5FD; border: 1px solid rgba(167, 139, 250, 0.3);">
                                    {{ $businessProfile->business_category }}
                                </span>
                                @if($businessProfile->umkmCategory)
                                    <span class="badge" style="background: rgba(255,255,255,0.1); color: #F1F0FF; border: 1px solid rgba(255,255,255,0.2);">
                                        {{ $businessProfile->umkmCategory->name }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-[#A9A8C3]">{{ $user->email }}</p>
                        @endif
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="md:ml-auto grid grid-cols-2 gap-4">
                        <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl px-4 py-3 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="text-xs font-semibold text-[#A9A8C3] uppercase tracking-wide">Member Since</div>
                            </div>
                            <div class="text-lg font-bold text-[#F1F0FF]">{{ $user->created_at->format('M Y') }}</div>
                        </div>
                        <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl px-4 py-3 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div class="text-xs font-semibold text-[#A9A8C3] uppercase tracking-wide">Role</div>
                            </div>
                            <div class="text-lg font-bold text-[#A78BFA]">{{ $user->getPrimaryRoleName() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="profile-card mb-6">
                <div class="flex overflow-x-auto scrollbar-hide border-b border-[#2A2A3A]">
                    <button @click="activeTab = 'personal'" 
                            :class="{ 'active': activeTab === 'personal' }" 
                            class="section-tab whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Info
                    </button>
                    <button @click="activeTab = 'business'" 
                            :class="{ 'active': activeTab === 'business' }" 
                            class="section-tab whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Business
                    </button>
                    <button @click="activeTab = 'payment'" 
                            :class="{ 'active': activeTab === 'payment' }" 
                            class="section-tab whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Payment
                    </button>
                    <button @click="activeTab = 'security'" 
                            :class="{ 'active': activeTab === 'security' }" 
                            class="section-tab whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Security
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="space-y-6">
                <!-- Personal Information Tab -->
                <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Business Tab -->
                <div x-show="activeTab === 'business'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @include('profile.partials.update-business-information-form')
                </div>

                <!-- Payment Tab -->
                <div x-show="activeTab === 'payment'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @include('profile.partials.update-payment-information-form')
                </div>

                <!-- Security Tab -->
                <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="space-y-6">
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function profilePage() {
            return {
                activeTab: 'personal',
            }
        }
        
        // Global callback for Google Maps API
        function initGoogleMaps() {
            console.log('Google Maps API loaded successfully');
            // Trigger Alpine.js to initialize the map
            window.dispatchEvent(new CustomEvent('google-maps-loaded'));
        }
    </script>
    
    <!-- Google Maps JavaScript API with proper callback -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&callback=initGoogleMaps" async defer></script>
</x-app-layout>
