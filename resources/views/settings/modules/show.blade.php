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
                    {{ $module->display_name }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                @if($module->is_system)
                    <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-700 rounded-full">
                        System Module
                    </span>
                @endif
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $module->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $module->is_active ? 'Active' : 'Inactive' }}
                </span>
                <a href="{{ route('settings.modules.edit', $module) }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Module
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Module Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Module Info Card -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <!-- Module Icon -->
                                <div class="w-20 h-20 rounded-2xl flex items-center justify-center flex-shrink-0" 
                                     style="background: linear-gradient(135deg, {{ $module->color }}20, {{ $module->color }}10);">
                                    @if($module->icon)
                                        <svg class="w-10 h-10" style="color: {{ $module->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module->icon }}"></path>
                                        </svg>
                                    @else
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                                             style="background-color: {{ $module->color }}">
                                            {{ substr($module->display_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $module->display_name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $module->description ?? 'No description available.' }}</p>
                                    
                                    <div class="flex items-center space-x-6 text-sm">
                                        <div>
                                            <span class="text-gray-600">Module ID:</span>
                                            <span class="text-gray-900 font-mono">{{ $module->name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Sort Order:</span>
                                            <span class="text-gray-900">{{ $module->sort_order }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Color:</span>
                                            <div class="inline-flex items-center space-x-2">
                                                <div class="w-4 h-4 rounded-full" style="background-color: {{ $module->color }}"></div>
                                                <span class="text-gray-900 font-mono">{{ $module->color }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Module Menus -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Module Menus</h4>
                            <a href="{{ route('settings.menus.create') }}?module={{ $module->id }}" 
                               class="inline-flex items-center px-3 py-2 text-sm bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Menu
                            </a>
                        </div>
                        
                        <div class="p-6">
                            @if($module->menus->count() > 0)
                                <div class="space-y-4">
                                    @foreach($module->menus->sortBy('sort_order') as $menu)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center space-x-3">
                                                @if($menu->icon)
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" 
                                                         style="background: linear-gradient(135deg, {{ $module->color }}20, {{ $module->color }}10);">
                                                        <svg class="w-4 h-4" style="color: {{ $module->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon }}"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="font-medium text-gray-900">{{ $menu->display_name }}</h5>
                                                    <p class="text-sm text-gray-600">{{ $menu->description ?? $menu->route }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-3">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <span class="text-xs text-gray-600">Order: {{ $menu->sort_order }}</span>
                                                <a href="{{ route('settings.menus.edit', $menu) }}" 
                                                   class="text-gray-600 hover:text-gray-900 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    <h5 class="text-lg font-medium text-gray-900 mb-2">No menus found</h5>
                                    <p class="text-gray-600 mb-4">This module doesn't have any menus yet.</p>
                                    <a href="{{ route('settings.menus.create') }}?module={{ $module->id }}" 
                                       class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create First Menu
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Module Statistics -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Menus</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $module->menus->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Active Menus</span>
                                <span class="text-2xl font-bold text-green-700">{{ $module->menus->where('is_active', true)->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">System Menus</span>
                                <span class="text-2xl font-bold text-blue-700">{{ $module->menus->where('is_system', true)->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Module Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Module Details</h4>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <div class="text-gray-900">{{ $module->created_at->format('M d, Y \a\t H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Last Updated:</span>
                                <div class="text-gray-900">{{ $module->updated_at->format('M d, Y \a\t H:i') }}</div>
                            </div>
                            @if($module->creator)
                                <div>
                                    <span class="text-gray-600">Created By:</span>
                                    <div class="text-gray-900">{{ $module->creator->name }}</div>
                                </div>
                            @endif
                            @if($module->updater && $module->updater->id !== $module->creator?->id)
                                <div>
                                    <span class="text-gray-600">Updated By:</span>
                                    <div class="text-gray-900">{{ $module->updater->name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Module Preview -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Module Preview</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="bg-white rounded-xl border border-gray-200 p-4">
                                <!-- Preview Icon -->
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl mb-3" 
                                     style="background: linear-gradient(135deg, {{ $module->color }}20, {{ $module->color }}10);">
                                    @if($module->icon)
                                        <svg class="w-6 h-6" style="color: {{ $module->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module->icon }}"></path>
                                        </svg>
                                    @else
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white font-bold"
                                             style="background-color: {{ $module->color }}">
                                            {{ substr($module->display_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Preview Title -->
                                <h5 class="font-semibold text-gray-900 mb-2">{{ $module->display_name }}</h5>
                                
                                <!-- Preview Description -->
                                <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $module->description ?? 'Module description...' }}</p>
                                
                                <!-- Preview Button -->
                                <button class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200"
                                        style="background: linear-gradient(to right, {{ $module->color }}20, {{ $module->color }}10); border: 1px solid {{ $module->color }}30; color: {{ $module->color }}">
                                    <span>Open Module</span>
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ route('settings.modules.edit', $module) }}" 
                               class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Module
                            </a>
                            
                            <a href="{{ route('settings.menus.create') }}?module={{ $module->id }}" 
                               class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Menu
                            </a>
                            
                            @if(!$module->is_system)
                                <button onclick="confirmDelete('{{ $module->id }}')" 
                                        class="w-full flex items-center px-4 py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Module
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(moduleId) {
            if (confirm('Are you sure you want to delete this module? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/settings/modules/${moduleId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
