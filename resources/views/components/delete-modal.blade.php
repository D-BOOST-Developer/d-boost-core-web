@props(['title' => 'Delete Confirmation', 'message' => 'Are you sure you want to delete this item?', 'action' => '#'])

<div x-data="{ 
        showModal: false, 
        modalTitle: 'Delete Confirmation',
        modalMessage: 'Are you sure you want to delete this item?',
        formAction: '#'
     }" 
     x-show="showModal" 
     x-cloak 
     id="deleteModal"
     @open-delete-modal.window="
        modalTitle = $event.detail.title;
        modalMessage = $event.detail.message;
        formAction = $event.detail.action;
        showModal = true;
     "
     class="fixed inset-0 z-50 overflow-y-auto" 
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" 
         @click="showModal = false"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative rounded-xl shadow-2xl max-w-md w-full mx-auto"
             style="background-color: #1A1A24; border: 1px solid #2A2A3A;"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Header -->
            <div class="p-6 pb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(239, 68, 68, 0.15);">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold" style="color: #F1F0FF;" x-text="modalTitle">{{ $title }}</h3>
                        <p class="text-sm mt-1" style="color: #A9A8C3;" x-text="modalMessage">{{ $message }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 pb-6">
                <div class="rounded-lg p-4 mb-6" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25);">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h4 class="text-red-400 font-medium text-sm mb-1">Warning</h4>
                            <p class="text-red-300 text-xs">
                                This action cannot be undone. The data will be permanently deleted from the system.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-3">
                    <button type="button" @click="showModal = false"
                            class="flex-1 font-medium py-2 px-4 rounded-lg transition-all duration-200"
                            style="background-color: #252535; color: #F1F0FF; border: 1px solid #3A3A4A;">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1" :action="formAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200">
                            Delete
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
