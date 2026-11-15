@props(['action' => '#'])

<div x-data="{ 
        showModal: false, 
        formAction: '{{ $action }}',
        rejectionReason: ''
     }" x-show="showModal" x-cloak id="rejectModal" @open-reject-modal.window="
        formAction = $event.detail.action;
        rejectionReason = '';
        showModal = true;
     " class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="showModal = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative rounded-xl shadow-2xl max-w-md w-full mx-auto"
            style="background-color: #1A1A24; border: 1px solid #2A2A3A;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95" @click.away="showModal = false">

            <!-- Header -->
            <div class="p-6 pb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(239, 68, 68, 0.15);">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold" style="color: #F1F0FF;">Reject Transaction</h3>
                        <p class="text-sm mt-1" style="color: #A9A8C3;">Please provide a reason for rejection</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" :action="formAction">
                @csrf

                <div class="px-6 pb-4">
                    <label for="rejection_reason" class="block text-sm font-medium mb-2" style="color: #A9A8C3;">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" x-model="rejectionReason" rows="4" required
                        placeholder="Enter the reason for rejecting this transaction..."
                        class="w-full rounded-lg px-3 py-2 resize-none"
                        style="background-color: #111118; border: 1px solid #3A3A4A; color: #F1F0FF;"></textarea>
                    <p class="mt-2 text-xs" style="color: #7E7D9C;">
                        <svg class="w-4 h-4 inline" style="color: #6B6A8A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        This reason will be visible to the transaction creator
                    </p>
                </div>

                <!-- Actions -->
                <div class="px-6 pb-6">
                    <div class="flex space-x-3">
                        <button type="button" @click="showModal = false"
                            class="flex-1 font-medium py-2.5 px-4 rounded-lg transition-all duration-200"
                            style="background-color: #252535; color: #F1F0FF; border: 1px solid #3A3A4A;">
                            Cancel
                        </button>
                        <button type="submit" :disabled="!rejectionReason.trim()"
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
    [x-cloak] {
        display: none !important;
    }
</style>