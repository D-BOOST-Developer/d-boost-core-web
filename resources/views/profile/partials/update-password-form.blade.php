<section>
    <div class="profile-card p-6">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-[#F1F0FF] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                {{ __('Update Password') }}
            </h2>
            <p class="mt-1 text-sm text-[#A9A8C3]">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
        </header>

        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="update_password_current_password" class="info-label block">{{ __('Current Password') }}</label>
                    <input type="password" 
                           id="update_password_current_password" 
                           name="current_password" 
                           autocomplete="current-password"
                           class="input-field w-full">
                    @error('current_password', 'updatePassword')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="update_password_password" class="info-label block">{{ __('New Password') }}</label>
                    <input type="password" 
                           id="update_password_password" 
                           name="password" 
                           autocomplete="new-password"
                           class="input-field w-full">
                    @error('password', 'updatePassword')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="update_password_password_confirmation" class="info-label block">{{ __('Confirm Password') }}</label>
                    <input type="password" 
                           id="update_password_password_confirmation" 
                           name="password_confirmation" 
                           autocomplete="new-password"
                           class="input-field w-full">
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password Tips -->
            <div class="bg-[#111118] border border-[#2A2A3A] rounded-lg p-4">
                <h4 class="text-sm font-semibold text-[#A78BFA] mb-2">Password Requirements:</h4>
                <ul class="text-xs text-[#A9A8C3] space-y-1">
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        At least 8 characters long
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mix of uppercase and lowercase letters
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Include numbers and special characters
                    </li>
                </ul>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary">
                    {{ __('Update Password') }}
                </button>

                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }"
                       x-show="show"
                       x-transition
                       x-init="setTimeout(() => show = false, 3000)"
                       class="text-sm text-emerald-400 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('Password Updated!') }}
                    </p>
                @endif
            </div>
        </form>
    </div>
</section>
