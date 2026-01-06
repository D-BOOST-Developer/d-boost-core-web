<x-guest-layout>
    <div>
        <!-- Header -->
        <div class="text-center mb-5">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
            <p class="text-gray-600 text-sm">Enter your email to receive reset instructions</p>
        </div>

        <!-- Info Message -->
        <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-[#006BFF] mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-700 text-sm">
                    Forgot your password? No problem. Just enter your email address and we'll send you a password reset link that will allow you to choose a new one.
                </p>
            </div>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700 text-sm">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent transition-all duration-200"
                           placeholder="Enter your email address">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-[#006BFF] to-blue-600 hover:from-[#0056cc] hover:to-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:ring-offset-2 shadow-lg shadow-blue-500/30">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send Reset Link
                </span>
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-5 text-center">
            <p class="text-gray-600 text-sm">
                Remember your password? 
                <a href="{{ route('login') }}" class="text-[#006BFF] hover:text-[#0056cc] font-medium transition-colors duration-200">
                    Back to Login
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
