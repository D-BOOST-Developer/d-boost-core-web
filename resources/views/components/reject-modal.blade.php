@props(['action' => '#'])

<div x-data="{ 
        showModal: false, 
        formAction: '{{ $action }}',
        rejectionReason: ''
     }" 
     x-show="showModal" 
     x-cloak 
     id="rejectModal"
     @open-reject-modal.window="
        formAction = $event.detail.action;
        rejectionReason = '';
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
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="showModal = false"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-2xl max-w-md w-full mx-auto"
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
                        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Reject Transaction</h3>
                        <p class="text-gray-600 text-sm mt-1">Please provide a reason for rejection</p>
                    </div>
                </div>
            </div>
            
            <!-- Form -->
            <form method="POST" :action="formAction">
                @csrf
                
                <div class="px-6 pb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="rejection_reason"
                        name="rejection_reason" 
                        x-model="rejectionReason"
                        rows="4"
                        required
                        placeholder="Enter the reason for rejecting this transaction..."
                        class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                    ></textarea>
                    <p class="mt-2 text-xs text-gray-500">
                        <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        This reason will be visible to the transaction creator
                    </p>
                </div>
                
                <!-- Actions -->
                <div class="px-6 pb-6">
                    <div class="flex space-x-3">
                        <button type="button" @click="showModal = false"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="!rejectionReason.trim()"
                                class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200">
                            Reject Transaction
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

