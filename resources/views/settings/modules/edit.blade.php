<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('settings.modules.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Modules
                </a>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Module: ') . $module->display_name }}
                </h2>
            </div>
            @if($module->is_system)
                <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-700 rounded-full">
                    System Module
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('settings.modules.update', $module) }}" method="POST" x-data="moduleForm()">
                @csrf
                @method('PUT')
                
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Module Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Update the settings for this module.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Module Name & Display Name -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Module Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       x-model="form.name"
                                       value="{{ old('name', $module->name) }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('name') border-red-500 @enderror"
                                       {{ $module->is_system ? 'readonly' : '' }}
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @if($module->is_system)
                                    <p class="mt-1 text-xs text-yellow-700">System module names cannot be changed</p>
                                @else
                                    <p class="mt-1 text-xs text-gray-500">Unique identifier for the module</p>
                                @endif
                            </div>

                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Display Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="display_name" 
                                       name="display_name" 
                                       x-model="form.display_name"
                                       value="{{ old('display_name', $module->display_name) }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('display_name') border-red-500 @enderror"
                                       required>
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                      class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $module->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon & Color -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                                    Icon (SVG Path)
                                </label>
                                <textarea id="icon" 
                                          name="icon" 
                                          rows="3"
                                          x-model="form.icon"
                                          class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('icon') border-red-500 @enderror font-mono text-sm">{{ old('icon', $module->icon) }}</textarea>
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Module Color <span class="text-red-400">*</span>
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" 
                                           id="color" 
                                           name="color" 
                                           x-model="form.color"
                                           value="{{ old('color', $module->color) }}"
                                           class="w-12 h-10 bg-white border border-gray-300 rounded-lg cursor-pointer">
                                    <input type="text" 
                                           x-model="form.color"
                                           class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('color') border-red-500 @enderror"
                                           pattern="^#[0-9A-Fa-f]{6}$"
                                           required>
                                </div>
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Sort Order & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sort Order <span class="text-red-400">*</span>
                                </label>
                                <input type="number" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', $module->sort_order) }}"
                                       min="0"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('sort_order') border-red-500 @enderror"
                                       required>
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', $module->is_active) ? 'checked' : '' }}
                                           class="w-4 h-4 text-[#006BFF] bg-white border-gray-300 rounded focus:ring-[#006BFF] focus:ring-2">
                                        <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                            </div>
                        </div>

                        <!-- Module Stats -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Module Statistics</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $module->menus->count() }}</div>
                                    <div class="text-sm text-gray-600">Total Menus</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $module->menus->where('is_active', true)->count() }}</div>
                                    <div class="text-sm text-gray-600">Active Menus</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $module->created_at->format('M Y') }}</div>
                                    <div class="text-sm text-gray-600">Created</div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Preview</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="max-w-sm">
                                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                                        <!-- Preview Icon -->
                                        <div class="flex items-center justify-center w-16 h-16 rounded-2xl mb-4 transition-all duration-300"
                                             :style="`background: linear-gradient(135deg, ${form.color}20, ${form.color}10)`">
                                            <template x-if="form.icon">
                                                <svg class="w-8 h-8 transition-all duration-300" 
                                                     :style="`color: ${form.color}`" 
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="form.icon"></path>
                                                </svg>
                                            </template>
                                            <template x-if="!form.icon && form.display_name">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-lg"
                                                     :style="`background-color: ${form.color}`"
                                                     x-text="form.display_name.charAt(0).toUpperCase()">
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Preview Title -->
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2" 
                                            x-text="form.display_name || 'Module Name'"></h3>
                                        
                                        <!-- Preview Description -->
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2" 
                                           x-text="form.description || 'Module description will appear here...'"></p>
                                        
                                        <!-- Preview Button -->
                                        <button type="button" 
                                                class="w-full flex items-center justify-center px-4 py-3 rounded-lg transition-all duration-200 text-sm font-medium"
                                                :style="`background: linear-gradient(to right, ${form.color}20, ${form.color}10); border: 1px solid ${form.color}30; color: ${form.color}`">
                                            <span>Open Module</span>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('settings.modules.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:text-gray-900 hover:border-gray-400 transition-colors duration-200">
                                Cancel
                            </a>
                            <a href="{{ route('settings.modules.show', $module) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:text-gray-900 hover:border-gray-400 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Details
                            </a>
                        </div>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Module
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function moduleForm() {
            return {
                form: {
                    name: '{{ old("name", $module->name) }}',
                    display_name: '{{ old("display_name", $module->display_name) }}',
                    description: '{{ old("description", $module->description) }}',
                    icon: '{{ old("icon", $module->icon) }}',
                    color: '{{ old("color", $module->color) }}'
                }
            }
        }
    </script>
</x-app-layout>
