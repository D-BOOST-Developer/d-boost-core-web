<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('settings.users.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Users
                </a>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit User') }}: {{ $user->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('settings.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Update user account details and roles.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Name & Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('name') border-red-500 @enderror"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('email') border-red-500 @enderror"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Password (leave blank to keep current)
                                </label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm New Password
                                </label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent">
                            </div>
                        </div>

                        <!-- Roles -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assign Roles
                            </label>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3 max-h-60 overflow-y-auto border border-gray-200">
                                @php
                                    $userRoleIds = $user->userRoles->where('is_active', true)->pluck('role_id')->toArray();
                                @endphp
                                @forelse($availableRoles as $role)
                                    <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                        <input type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}"
                                               {{ (old('roles') && in_array($role->id, old('roles'))) || (!old('roles') && in_array($role->id, $userRoleIds)) ? 'checked' : '' }}
                                               class="w-4 h-4 text-[#006BFF] bg-white border-gray-300 rounded focus:ring-[#006BFF] focus:ring-2">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $role->display_name }}</div>
                                            @if($role->description)
                                                <div class="text-xs text-gray-600">{{ $role->description }}</div>
                                            @endif
                                        </div>
                                        @if($role->is_system)
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">System</span>
                                        @endif
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-600">No roles available.</p>
                                @endforelse
                            </div>
                            @error('roles')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-end space-x-3">
                        <a href="{{ route('settings.users.show', $user) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:text-gray-900 hover:border-gray-400 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

