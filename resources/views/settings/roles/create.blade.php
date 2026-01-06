<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('settings.roles.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Roles
                </a>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Create New Role') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('settings.roles.store') }}" method="POST">
                @csrf
                
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Role Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Create a new role with specific permissions.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Role Name & Display Name -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Role Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="e.g., manager"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-600">Unique identifier (lowercase, underscores allowed)</p>
                            </div>

                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Display Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="display_name" 
                                       name="display_name" 
                                       value="{{ old('display_name') }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('display_name') border-red-500 @enderror"
                                       placeholder="e.g., Manager"
                                       required>
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Human-readable name</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Brief description of this role...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-[#006BFF] bg-white border-gray-300 rounded focus:ring-[#006BFF] focus:ring-2">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-600">Whether this role is available for assignment</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-end space-x-3">
                        <a href="{{ route('settings.roles.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:text-gray-900 hover:border-gray-400 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Create Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

