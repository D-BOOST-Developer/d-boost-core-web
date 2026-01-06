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
                    {{ $user->name }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('settings.users.edit', $user) }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- User Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- User Info Card -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <!-- User Avatar -->
                                <div class="w-20 h-20 bg-gradient-to-br from-[#006BFF] to-cyan-400 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $user->name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $user->email }}</p>
                                    
                                    <div class="flex items-center space-x-6 text-sm">
                                        <div>
                                            <span class="text-gray-600">Member Since:</span>
                                            <span class="text-gray-900 ml-2">{{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Last Updated:</span>
                                            <span class="text-gray-900 ml-2">{{ $user->updated_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Roles -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">Assigned Roles</h4>
                        </div>
                        
                        <div class="p-6">
                            @if($user->userRoles->where('is_active', true)->count() > 0)
                                <div class="space-y-4">
                                    @foreach($user->userRoles->where('is_active', true) as $userRole)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center space-x-3">
                                                <div>
                                                    <h5 class="font-medium text-gray-900">{{ $userRole->role->display_name }}</h5>
                                                    <p class="text-sm text-gray-600">{{ $userRole->role->description ?? $userRole->role->name }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-3">
                                                @if($userRole->role->is_system)
                                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">System</span>
                                                @endif
                                                <a href="{{ route('settings.roles.show', $userRole->role) }}" 
                                                   class="text-blue-600 hover:text-blue-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Roles Assigned</h3>
                                    <p class="text-gray-600 mb-4">This user doesn't have any roles assigned yet.</p>
                                    <a href="{{ route('settings.users.edit', $user) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                                        Assign Roles
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- User Statistics -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Roles</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $user->userRoles->where('is_active', true)->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">User Details</h4>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Email Verified:</span>
                                <div class="text-gray-900">
                                    @if($user->email_verified_at)
                                        <span class="text-green-700">Yes</span> ({{ $user->email_verified_at->format('M d, Y') }})
                                    @else
                                        <span class="text-red-700">No</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <div class="text-gray-900">{{ $user->created_at->format('M d, Y \a\t H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Last Updated:</span>
                                <div class="text-gray-900">{{ $user->updated_at->format('M d, Y \a\t H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ route('settings.users.edit', $user) }}" 
                               class="w-full flex items-center px-4 py-3 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit User
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

