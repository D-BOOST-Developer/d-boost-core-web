<section class="space-y-6">
    <div class="danger-zone profile-card p-6">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-red-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                {{ __('Danger Zone') }}
            </h2>
            <p class="mt-1 text-sm text-red-700/80">
                {{ __('Irreversible and destructive actions. Proceed with caution.') }}
            </p>
        </header>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-white/50 rounded-xl border border-red-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Delete Account') }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('Permanently delete your account and all associated data.') }}</p>
            </div>

            <button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                {{ __('Delete Account') }}
            </button>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="mb-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">
                    {{ __('Are you absolutely sure?') }}
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed max-w-md mx-auto">
                    {{ __('This action cannot be undone. This will permanently delete your account, business profile, and all associated data from our servers.') }}
                </p>
            </div>

            <div class="mb-6">
                <label for="password" class="info-label block">{{ __('Enter your password to confirm') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="input-field w-full bg-white text-gray-900"
                    placeholder="{{ __('Password') }}"
                />
                @error('password', 'userDeletion')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        x-on:click="$dispatch('close')" 
                        class="btn-secondary">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" 
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-all duration-200">
                    {{ __('Yes, Delete My Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
