<section>
    <div class="profile-card p-6">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-[#F1F0FF] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                {{ __('Personal Information') }}
            </h2>
            <p class="mt-1 text-sm text-[#A9A8C3]">
                {{ __("Update your account's profile information and email address.") }}
            </p>
        </header>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="info-label block">{{ __('Full Name') }}</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           class="input-field w-full">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="info-label block">{{ __('Email Address') }}</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required 
                           autocomplete="username"
                           class="input-field w-full">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 p-3 bg-amber-950/40 border border-amber-800/40 rounded-lg">
                            <p class="text-sm text-amber-200">
                                {{ __('Your email address is unverified.') }}
                                <button form="send-verification" class="underline text-[#A78BFA] hover:text-[#C4B5FD] font-medium">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-emerald-400">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Info (Read Only) -->
            <div class="bg-[#111118] border border-[#2A2A3A] rounded-xl p-4 mt-6">
                <h3 class="text-sm font-semibold text-[#A9A8C3] mb-3">Account Details</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="info-label">Account ID</div>
                        <div class="info-value text-sm font-mono text-[#F1F0FF]">{{ substr($user->id, 0, 8) }}...</div>
                    </div>
                    <div>
                        <div class="info-label">Role</div>
                        <div class="info-value text-[#F1F0FF]">{{ $user->getPrimaryRoleName() }}</div>
                    </div>
                    <div>
                        <div class="info-label">Created</div>
                        <div class="info-value text-[#F1F0FF]">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="info-label">Status</div>
                        <div>
                            <span class="badge badge-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary">
                    {{ __('Save Changes') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }"
                       x-show="show"
                       x-transition
                       x-init="setTimeout(() => show = false, 3000)"
                       class="text-sm text-emerald-400 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('Saved!') }}
                    </p>
                @endif
            </div>
        </form>
    </div>
</section>
