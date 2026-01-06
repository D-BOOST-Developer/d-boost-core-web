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
                    {{ $role->display_name }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                @if($role->is_system)
                    <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-700 rounded-full">
                        System Role
                    </span>
                @endif
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $role->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                </span>
                <a href="{{ route('settings.roles.edit', $role) }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Role
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Role Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Role Info Card -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $role->display_name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $role->description ?? 'No description available.' }}</p>
                            
                            <div class="flex items-center space-x-6 text-sm">
                                <div>
                                    <span class="text-gray-600">Role ID:</span>
                                    <span class="text-gray-900 font-mono ml-2">{{ $role->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Users:</span>
                                    <span class="text-gray-900 ml-2">{{ $role->userRoles->where('is_active', true)->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Menus:</span>
                                    <span class="text-gray-900 ml-2">{{ $role->roleMenus->where('is_active', true)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users with this Role -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Users with this Role</h4>
                            <a href="{{ route('settings.users.index') }}?role={{ $role->id }}" 
                               class="text-sm text-blue-600 hover:text-blue-700 transition-colors">
                                View All
                            </a>
                        </div>
                        
                        <div class="p-6">
                            @if($role->userRoles->where('is_active', true)->count() > 0)
                                <div class="space-y-4">
                                    @foreach($role->userRoles->where('is_active', true)->take(10) as $userRole)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-[#006BFF] to-cyan-400 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-sm font-bold">{{ substr($userRole->user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">{{ $userRole->user->name }}</h5>
                                                    <p class="text-sm text-gray-600">{{ $userRole->user->email }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-3">
                                                @if($userRole->assigned_at)
                                                    <span class="text-xs text-gray-600">
                                                        Assigned {{ $userRole->assigned_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                                <a href="{{ route('settings.users.show', $userRole->user) }}" 
                                                   class="text-blue-600 hover:text-blue-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($role->userRoles->where('is_active', true)->count() > 10)
                                        <div class="text-center pt-4">
                                            <a href="{{ route('settings.users.index') }}?role={{ $role->id }}" 
                                               class="inline-flex items-center px-4 py-2 text-sm bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                                View All {{ $role->userRoles->where('is_active', true)->count() }} Users
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Assigned</h3>
                                    <p class="text-gray-600 mb-4">This role doesn't have any users assigned yet.</p>
                                    <a href="{{ route('settings.users.index') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                        Assign Users
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Accessible Menus -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Accessible Menus & Modules</h4>
                            <a href="{{ route('settings.roles.permissions', $role) }}" 
                               class="inline-flex items-center px-4 py-2 text-sm bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Manage Permissions
                            </a>
                        </div>
                        
                        <div class="p-6">
                            @php
                                $menusByModule = $role->roleMenus->where('is_active', true)->groupBy(function($roleMenu) {
                                    return $roleMenu->menu->module->display_name ?? 'Other';
                                });
                            @endphp
                            
                            @if($menusByModule->count() > 0)
                                <div class="space-y-6">
                                    @foreach($menusByModule as $moduleName => $roleMenus)
                                        <div>
                                            <h5 class="text-sm font-semibold text-gray-700 uppercase mb-3">{{ $moduleName }}</h5>
                                            <div class="space-y-2">
                                                @foreach($roleMenus as $roleMenu)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                        <div class="flex items-center space-x-3">
                                                            <div>
                                                                <h6 class="font-medium text-gray-900">{{ $roleMenu->menu->display_name }}</h6>
                                                                <p class="text-xs text-gray-600">{{ $roleMenu->menu->description ?? $roleMenu->menu->route }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-center space-x-2">
                                                            @php
                                                                // Ensure permissions is always an array
                                                                $permissions = $roleMenu->permissions;
                                                                if (is_string($permissions)) {
                                                                    $permissions = json_decode($permissions, true) ?? [];
                                                                }
                                                                $permissions = is_array($permissions) ? $permissions : [];
                                                            @endphp
                                                            @if(!empty($permissions))
                                                                @foreach($permissions as $permission)
                                                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                                                        {{ $permission }}
                                                                    </span>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Menus Assigned</h3>
                                    <p class="text-gray-600 mb-4">This role doesn't have access to any menus yet.</p>
                                    <a href="{{ route('settings.roles.permissions', $role) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                        Configure Permissions
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Role Statistics -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Users</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $role->userRoles->where('is_active', true)->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Accessible Menus</span>
                                <span class="text-2xl font-bold text-green-700">{{ $role->roleMenus->where('is_active', true)->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Active Modules</span>
                                <span class="text-2xl font-bold text-blue-700">{{ $menusByModule->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Role Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Role Details</h4>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <div class="text-gray-900">{{ $role->created_at->format('M d, Y \a\t H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Last Updated:</span>
                                <div class="text-gray-900">{{ $role->updated_at->format('M d, Y \a\t H:i') }}</div>
                            </div>
                            @if($role->creator)
                                <div>
                                    <span class="text-gray-600">Created By:</span>
                                    <div class="text-gray-900">{{ $role->creator->name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ route('settings.roles.edit', $role) }}" 
                               class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Role
                            </a>
                            <a href="{{ route('settings.roles.permissions', $role) }}" 
                               class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Manage Permissions
                            </a>
                            <a href="{{ route('settings.users.index') }}?role={{ $role->id }}" 
                               class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                View Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

