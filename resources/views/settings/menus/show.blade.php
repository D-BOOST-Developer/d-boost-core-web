<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('settings.menus.index') }}" 
                   class="inline-flex items-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Menus
                </a>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ $menu->display_name }}
                </h2>
            </div>
            <a href="{{ route('settings.menus.edit', $menu) }}" 
               class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Menu
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-6 space-y-4">
                            <div>
                                <div class="text-sm text-gray-600">Module</div>
                                <div class="text-gray-900 font-medium">{{ $menu->module?->display_name ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Parent Menu</div>
                                <div class="text-gray-900 font-medium">{{ $menu->parent?->display_name ?? '(none)' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Route</div>
                                <div class="text-gray-900 font-mono">{{ $menu->route ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Sort Order</div>
                                <div class="text-gray-900 font-medium">{{ $menu->sort_order }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Active</div>
                                <div class="text-gray-900 font-medium">{{ $menu->is_active ? 'Yes' : 'No' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-700/50">
                            <h4 class="text-lg font-semibold text-white">Children Menus</h4>
                        </div>
                        <div class="p-6">
                            @if($menu->children->count() > 0)
                                <div class="space-y-2">
                                    @foreach($menu->children as $child)
                                        <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-700/30">
                                            <div>
                                                <div class="text-white font-medium">{{ $child->display_name }}</div>
                                                <div class="text-xs text-gray-400">{{ $child->route ?? '-' }}</div>
                                            </div>
                                            <a href="{{ route('settings.menus.show', $child) }}" class="text-blue-400 hover:text-blue-300 text-sm">View</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-gray-400">No children.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700/50 p-6">
                        <h4 class="text-lg font-semibold text-white mb-4">Details</h4>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-400">Created:</span>
                                <div class="text-white">{{ $menu->created_at->format('M d, Y \\a\\t H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-400">Last Updated:</span>
                                <div class="text-white">{{ $menu->updated_at->format('M d, Y \\a\\t H:i') }}</div>
                            </div>
                            @if($menu->creator)
                                <div>
                                    <span class="text-gray-400">Created By:</span>
                                    <div class="text-white">{{ $menu->creator->name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ route('settings.menus.edit', $menu) }}" class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
