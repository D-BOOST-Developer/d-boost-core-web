@props([
    'title' => 'Confirmation', 
    'message' => 'Are you sure you want to proceed?', 
    'confirmText' => 'Confirm',
    'confirmColor' => 'blue',
    'icon' => 'check',
    'action' => '#'
])

@php
$colorClasses = [
    'blue' => [
        'bg' => 'bg-blue-50',
        'icon' => 'text-blue-600',
        'button' => 'bg-[#006BFF] hover:bg-[#0056cc]'
    ],
    'green' => [
        'bg' => 'bg-green-50',
        'icon' => 'text-green-600',
        'button' => 'bg-green-600 hover:bg-green-700'
    ],
    'yellow' => [
        'bg' => 'bg-yellow-50',
        'icon' => 'text-yellow-600',
        'button' => 'bg-yellow-600 hover:bg-yellow-700'
    ],
    'red' => [
        'bg' => 'bg-red-50',
        'icon' => 'text-red-600',
        'button' => 'bg-red-600 hover:bg-red-700'
    ],
];

$colors = $colorClasses[$confirmColor] ?? $colorClasses['blue'];

$icons = [
    'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'question' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-.667-1.964-.667-2.732 0L4.082 16c-.77 1.333.192 3 1.732 3z',
    'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
];

$iconPath = $icons[$icon] ?? $icons['check'];
@endphp

<div x-data="{ 
        showModal: false, 
        modalTitle: '{{ $title }}',
        modalMessage: '{{ $message }}',
        confirmText: '{{ $confirmText }}',
        formAction: '{{ $action }}'
     }" 
     id="confirmModal"
     @open-confirm-modal.window="
        modalTitle = $event.detail.title || '{{ $title }}';
        modalMessage = $event.detail.message || '{{ $message }}';
        confirmText = $event.detail.confirmText || '{{ $confirmText }}';
        formAction = $event.detail.action;
        showModal = true;
     "
     x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="showModal = false"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative rounded-xl shadow-2xl max-w-md w-full mx-auto"
             style="background-color: #1A1A24; border: 1px solid #2A2A3A;"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.away="showModal = false">
            
            <!-- Header -->
            <div class="p-6 pb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 {{ $colors['bg'] }} rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold" style="color: #F1F0FF;" x-text="modalTitle"></h3>
                        <p class="text-sm mt-1" style="color: #A9A8C3;" x-text="modalMessage"></p>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="px-6 pb-6">
                <div class="flex space-x-3">
                    <button type="button" @click="showModal = false"
                            class="flex-1 font-medium py-2.5 px-4 rounded-lg transition-all duration-200"
                            style="background-color: #252535; color: #F1F0FF; border: 1px solid #3A3A4A;">
                        Cancel
                    </button>
                    <form method="POST" class="flex-1" :action="formAction">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $colors['button'] }} text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200"
                                x-text="confirmText">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

