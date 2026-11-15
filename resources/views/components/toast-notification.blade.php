<!-- Modern Toast Notification Component -->
<div x-data="toastManager()" 
     @show-toast.window="showToast($event.detail)"
     class="fixed top-4 right-4 z-50 space-y-3">
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             class="max-w-sm w-full shadow-2xl rounded-xl overflow-hidden border-l-4"
             style="background-color: #1A1A24; border-top: 1px solid #2A2A3A; border-right: 1px solid #2A2A3A; border-bottom: 1px solid #2A2A3A;"
             :class="{
                 'border-green-500': toast.type === 'success',
                 'border-red-500': toast.type === 'error',
                 'border-blue-500': toast.type === 'info',
                 'border-yellow-500': toast.type === 'warning'
             }">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg x-show="toast.type === 'success'" class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <!-- Error Icon -->
                        <svg x-show="toast.type === 'error'" class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <!-- Info Icon -->
                        <svg x-show="toast.type === 'info'" class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <!-- Warning Icon -->
                        <svg x-show="toast.type === 'warning'" class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold" style="color: #F1F0FF;" x-text="toast.title"></p>
                        <p class="mt-1 text-sm" style="color: #A9A8C3;" x-text="toast.message"></p>
                    </div>
                    <button @click="removeToast(toast.id)" class="ml-4 flex-shrink-0 text-[#7E7D9C] hover:text-[#F1F0FF]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="h-1" style="background-color: #252535;">
                <div class="h-full transition-all duration-[3000ms] ease-linear"
                     :class="{
                         'bg-green-500': toast.type === 'success',
                         'bg-red-500': toast.type === 'error',
                         'bg-blue-500': toast.type === 'info',
                         'bg-yellow-500': toast.type === 'warning'
                     }"
                     :style="`width: ${toast.show ? '0%' : '100%'}`">
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        nextId: 1,
        
        showToast(detail) {
            const id = this.nextId++;
            const toast = {
                id,
                type: detail.type || 'info',
                title: detail.title || 'Notification',
                message: detail.message || '',
                show: true
            };
            
            this.toasts.push(toast);
            
            setTimeout(() => {
                this.removeToast(id);
            }, 3000);
        },
        
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 300);
            }
        }
    }
}

// Global function to show toast
function showToast(message, type = 'info', title = null) {
    const titles = {
        success: 'Success',
        error: 'Error',
        info: 'Information',
        warning: 'Warning'
    };
    
    window.dispatchEvent(new CustomEvent('show-toast', {
        detail: {
            type,
            title: title || titles[type],
            message
        }
    }));
}
</script>
