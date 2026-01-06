<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Audit Log Details') }}
            </h2>
            <a href="{{ route('audit-logs.index') }}" 
               class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Audit Logs</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Audit Log Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                    {{ ucfirst($auditLog->action) }} {{ ucfirst($auditLog->table_name) }}
                                </h1>
                                <p class="text-gray-600">{{ $auditLog->performed_at->format('F d, Y \a\t H:i:s') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-sm rounded-full 
                                    {{ $auditLog->action === 'create' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                       ($auditLog->action === 'update' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                        'bg-red-100 text-red-700 border border-red-200') }}">
                                    {{ ucfirst($auditLog->action) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-600 text-sm">User</p>
                                    <div class="flex items-center mt-1">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-800 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white text-sm font-bold">{{ $auditLog->user ? substr($auditLog->user->name, 0, 1) : 'S' }}</span>
                                        </div>
                                        <div>
                                            <p class="text-gray-900 font-medium">{{ $auditLog->user->name ?? 'System' }}</p>
                                            <p class="text-gray-500 text-sm">{{ $auditLog->user->role ?? 'system' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-gray-600 text-sm">Table</p>
                                    <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-700 border border-purple-200">
                                        {{ ucfirst($auditLog->table_name) }}
                                    </span>
                                </div>

                                <div>
                                    <p class="text-gray-600 text-sm">Record ID</p>
                                    <p class="text-gray-900 font-mono text-sm">{{ $auditLog->record_id }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-600 text-sm">IP Address</p>
                                    <p class="text-gray-700 font-medium">{{ $auditLog->ip_address ?: 'N/A' }}</p>
                                </div>

                                <div>
                                    <p class="text-gray-600 text-sm">User Agent</p>
                                    <p class="text-gray-700 text-sm break-all">{{ $auditLog->user_agent ?: 'N/A' }}</p>
                                </div>

                                <div>
                                    <p class="text-gray-600 text-sm">Timestamp</p>
                                    <p class="text-gray-900 font-medium">{{ $auditLog->performed_at->format('M d, Y H:i:s') }}</p>
                                    <p class="text-gray-500 text-xs">{{ $auditLog->performed_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Old Values -->
                    @if($auditLog->old_values)
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                                Old Values
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-x-auto"><code>{{ json_encode(is_string($auditLog->old_values) ? json_decode($auditLog->old_values) : $auditLog->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endif

                    <!-- New Values -->
                    @if($auditLog->new_values)
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Values
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-x-auto"><code>{{ json_encode(is_string($auditLog->new_values) ? json_decode($auditLog->new_values) : $auditLog->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endif

                    <!-- Changes Comparison -->
                    @if($auditLog->old_values && $auditLog->new_values)
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                                Changes Summary
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $oldData = is_string($auditLog->old_values) ? json_decode($auditLog->old_values, true) : $auditLog->old_values;
                                    $newData = is_string($auditLog->new_values) ? json_decode($auditLog->new_values, true) : $auditLog->new_values;
                                    $changes = [];
                                    
                                    if (is_array($oldData) && is_array($newData)) {
                                        foreach ($newData as $key => $newValue) {
                                            if (isset($oldData[$key]) && $oldData[$key] != $newValue) {
                                                $changes[$key] = [
                                                    'old' => $oldData[$key],
                                                    'new' => $newValue
                                                ];
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if(count($changes) > 0)
                                    @foreach($changes as $field => $change)
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <h4 class="text-gray-900 font-medium mb-2">{{ ucwords(str_replace('_', ' ', $field)) }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-red-600 text-sm mb-1">Before:</p>
                                                    <p class="text-gray-700 bg-red-50 p-2 rounded border border-red-200 text-sm">
                                                        {{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-green-600 text-sm mb-1">After:</p>
                                                    <p class="text-gray-700 bg-green-50 p-2 rounded border border-green-200 text-sm">
                                                        {{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-600 text-center py-4">No field changes detected.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Action Summary -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Action Summary</h3>
                        <div class="space-y-4">
                            <div class="text-center p-4 rounded-lg 
                                {{ $auditLog->action === 'create' ? 'bg-green-50 border border-green-200' : 
                                   ($auditLog->action === 'update' ? 'bg-blue-50 border border-blue-200' : 
                                    'bg-red-50 border border-red-200') }}">
                                <p class="text-2xl font-bold 
                                    {{ $auditLog->action === 'create' ? 'text-green-700' : 
                                       ($auditLog->action === 'update' ? 'text-blue-700' : 'text-red-700') }}">
                                    {{ ucfirst($auditLog->action) }}
                                </p>
                                <p class="text-gray-600 text-sm">Action Type</p>
                            </div>
                        </div>
                    </div>

                    <!-- Related Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600">Table</p>
                                <p class="text-gray-900 font-medium">{{ $auditLog->table_name }}</p>
                            </div>
                            
                            <div>
                                <p class="text-gray-600">Record ID</p>
                                <p class="text-gray-900 font-mono">{{ $auditLog->record_id }}</p>
                            </div>

                            @if($auditLog->user)
                                <div>
                                    <p class="text-gray-600">Performed by</p>
                                    <p class="text-gray-900 font-medium">{{ $auditLog->user->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $auditLog->user->email }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-gray-600">When</p>
                                <p class="text-gray-900 font-medium">{{ $auditLog->performed_at->format('M d, Y H:i:s') }}</p>
                                <p class="text-gray-500 text-xs">{{ $auditLog->performed_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Technical Details</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600">IP Address</p>
                                <p class="text-gray-900 font-mono">{{ $auditLog->ip_address ?: 'N/A' }}</p>
                            </div>
                            
                            @if($auditLog->user_agent)
                                <div>
                                    <p class="text-gray-600">User Agent</p>
                                    <p class="text-gray-700 text-xs break-all">{{ $auditLog->user_agent }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-gray-600">Log ID</p>
                                <p class="text-gray-900 font-mono text-xs">{{ $auditLog->id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Navigation</h3>
                        <div class="space-y-3">
                            <a href="{{ route('audit-logs.index') }}" 
                               class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition-all duration-200 text-center block">
                                Back to Audit Logs
                            </a>
                            
                            <a href="{{ route('audit-logs.index', ['table' => $auditLog->table_name]) }}" 
                               class="w-full bg-purple-600 hover:bg-purple-500 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 text-center block">
                                View {{ ucfirst($auditLog->table_name) }} Logs
                            </a>

                            @if($auditLog->user)
                                <a href="{{ route('audit-logs.index', ['user' => $auditLog->user->id]) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 text-center block">
                                    View {{ $auditLog->user->name }}'s Logs
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
