<x-guest-layout>
    <div x-data="{ showPassword: false }">
        <!-- Welcome Message -->
        <div class="text-center mb-5">
            <h2 class="text-xl font-semibold mb-2" style="color: #F1F0FF;">Welcome Back!</h2>
            <p class="text-sm" style="color: #A9A8C3;">Sign in to access your dashboard</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg" style="background-color: rgba(167,139,250,0.15); border: 1px solid rgba(167,139,250,0.25);">
                <p class="text-sm text-[#A78BFA]">{{ session('status') }}</p>
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Google Sign-In Button -->
        <a href="{{ route('auth.google') }}" 
           class="w-full flex items-center justify-center gap-3 font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#A78BFA] focus:ring-offset-2 shadow-sm hover:shadow-md mb-4" style="background-color: #252535; border: 2px solid #3A3A4A; color: #F1F0FF;" onmouseover="this.style.borderColor='#A78BFA'" onmouseout="this.style.borderColor='#3A3A4A'">
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span>Sign in with Google</span>
        </a>

        <!-- Divider -->
        <div class="relative mb-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#2A2A3A]"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 text-[#6B6A8A]" style="background-color: #1A1A24;">OR</span>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium mb-1" style="color: #A9A8C3;">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: #4A4A6A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-[#A78BFA] focus:border-[#A78BFA]"
                           style="background-color: #111118; border: 1px solid #3A3A4A; color: #F1F0FF;"
                           placeholder="Enter your email">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1" style="color: #A9A8C3;">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: #4A4A6A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input id="password" 
                           :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="w-full pl-10 pr-12 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-[#A78BFA] focus:border-[#A78BFA]"
                           style="background-color: #111118; border: 1px solid #3A3A4A; color: #F1F0FF;"
                           placeholder="Enter your password">
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg x-show="!showPassword" class="h-5 w-5 hover:text-[#A9A8C3]" style="color: #4A4A6A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="showPassword" class="h-5 w-5 hover:text-[#A9A8C3]" style="color: #4A4A6A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="h-4 w-4 text-[#A78BFA] border-gray-600 rounded focus:ring-[#A78BFA] focus:ring-2" style="background-color: #111118;">
                    <span class="ml-2 text-sm" style="color: #A9A8C3;">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-sm text-[#A78BFA] hover:text-[#C4B5FD] transition-colors duration-200">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-[#A78BFA] to-[#7C3AED] hover:from-[#9333EA] hover:to-[#6D28D9] text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#A78BFA] focus:ring-offset-2 shadow-lg shadow-purple-500/30">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sign In to Dashboard
                </span>
            </button>
        </form>

        <!-- Additional Links -->
        @if (Route::has('register'))
            <div class="mt-5 text-center">
                <p class="text-sm" style="color: #6B6A8A;">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-[#A78BFA] hover:text-[#C4B5FD] font-medium transition-colors duration-200">
                        Create Account
                    </a>
                </p>
            </div>
        @endif
    </div>
</x-guest-layout>
