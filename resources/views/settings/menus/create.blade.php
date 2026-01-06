<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('settings.menus.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Menus
                </a>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Create New Menu') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('settings.menus.store') }}" method="POST">
                @csrf
                
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Menu Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Create a new menu under a module.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Module <span class="text-red-500">*</span></label>
                                <select name="module_id" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('module_id') border-red-500 @enderror" required>
                                    <option value="">Select module</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module->id }}" {{ optional($selectedModule)->id === $module->id ? 'selected' : '' }}>{{ $module->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('module_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Parent Menu</label>
                                <select name="parent_id" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('parent_id') border-red-500 @enderror">
                                    <option value="">(none)</option>
                                    @foreach($parentMenus as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="unique_name" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('name') border-red-500 @enderror" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Display Name <span class="text-red-500">*</span></label>
                                <input type="text" name="display_name" value="{{ old('display_name') }}" placeholder="Readable Name" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('display_name') border-red-500 @enderror" required>
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('description') border-red-500 @enderror" placeholder="Short description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Route</label>
                                <input type="text" name="route" value="{{ old('route') }}" placeholder="e.g., products.index" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('route') border-red-500 @enderror">
                                @error('route')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Icon (SVG path)</label>
                                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="SVG path d attribute" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('icon') border-red-500 @enderror">
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order <span class="text-red-500">*</span></label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('sort_order') border-red-500 @enderror" required>
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Active</label>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-[#006BFF] bg-white border-gray-300 rounded focus:ring-[#006BFF] focus:ring-2">
                                        <span class="ml-2 text-sm text-gray-700">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-end space-x-3">
                        <a href="{{ route('settings.menus.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:text-gray-900 hover:border-gray-400 transition-colors duration-200">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Create Menu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
