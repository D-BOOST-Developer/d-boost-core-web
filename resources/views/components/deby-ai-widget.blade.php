<!-- DEBY AI Floating Chat Widget -->
<div x-data="debyAIWidget()" x-init="init()" style="position: fixed; bottom: 24px; right: 24px; z-index: 99999;">
    <!-- Floating Button - Blue Color with Modern Animation -->
    <button 
        @click="toggleChat()"
        class="w-16 h-16 rounded-full bg-blue-600 hover:bg-blue-700 shadow-lg hover:shadow-2xl transform hover:scale-110 active:scale-95 transition-all duration-300 flex items-center justify-center text-white font-bold text-2xl cursor-pointer relative group"
        :class="{ 'ring-4 ring-blue-400/50 animate-pulse': isOpen }"
        style="box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);"
        title="DEBY AI"
    >
        <!-- Tooltip - Buka Chat (Top) -->
        <div x-show="!isOpen" class="absolute bottom-full right-0 mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            Mulai Chat dengan DEBY AI
        </div>

        <!-- Tooltip - Tutup Chat (Left) -->
        <div x-show="isOpen" class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            Tutup Chat
        </div>
        <svg x-show="!isOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <svg x-show="isOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Popup - Modern Design -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 transform translate-y-8 scale-90"
        x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 transform translate-y-8 scale-90"
        class="absolute bottom-24 right-0 w-full h-[600px] bg-white rounded-3xl shadow-2xl flex flex-col overflow-hidden border border-gray-100"
        style="position: absolute; bottom: 100px; right: 0; width: 560px; height: 600px; z-index: 99998; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);"
    >
        <!-- Header - Light Mode with Blue Accent -->
        <div class="bg-gradient-to-r from-white to-blue-50 border-b border-gray-100 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 ring-2 ring-blue-300/50 flex items-center justify-center text-white shadow-md">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3M8 7h8a2 2 0 012 2v6a4 4 0 01-4 4h-4a4 4 0 01-4-4V9a2 2 0 012-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M15 12h.01" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-800">DEBY AI</h3>
                    <p class="text-xs text-gray-500">AI Business Assistant</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showDeleteConfirm = true" class="hover:bg-red-100 p-2 rounded-lg transition text-red-600 hover:text-red-700" title="Hapus riwayat chat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button @click="toggleChat()" class="hover:bg-gray-200 p-2 rounded-lg transition text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-white" id="messagesContainer">
            <!-- Welcome Message -->
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 ring-2 ring-blue-300/50 flex-shrink-0 flex items-center justify-center text-white shadow-md">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3M8 7h8a2 2 0 012 2v6a4 4 0 01-4 4h-4a4 4 0 01-4-4V9a2 2 0 012-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M15 12h.01" />
                    </svg>
                </div>
                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                    <p class="text-sm text-gray-800 font-medium">
                        Halo! 👋 Saya DEBY AI, asisten bisnis Anda.
                    </p>
                    <p class="text-xs text-gray-600 mt-2">Saya siap membantu Anda dengan:</p>
                    <ul class="text-xs text-gray-700 mt-2 space-y-1 ml-2">
                        <li>✓ Menambah produk dan mengatur stok</li>
                        <li>✓ Mencatat pengeluaran dan laporan</li>
                        <li>✓ Melihat statistik penjualan</li>
                        <li>✓ Menjawab pertanyaan fitur</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-3 italic">💡 Coba tanya: "Bagaimana cara menambah produk?"</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions - Pure DB queries, no AI involvement -->
        <div class="border-t border-gray-100 px-4 py-3 bg-white overflow-x-auto">
            <div class="flex gap-2 pb-2" style="min-width: min-content;">
                <button @click="quickAction('revenue', '💰 Pemasukan hari ini')" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs rounded-full whitespace-nowrap transition-all active:scale-95 font-medium shadow-sm hover:shadow-md" title="Lihat total pendapatan hari ini">
                    💰 Pemasukan
                </button>
                <button @click="quickAction('expenses', '💸 Pengeluaran hari ini')" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-full whitespace-nowrap transition-all active:scale-95 font-medium shadow-sm hover:shadow-md" title="Lihat total pengeluaran hari ini">
                    💸 Pengeluaran
                </button>
                <button @click="quickAction('stock', '📦 Posisi stok saat ini')" class="px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs rounded-full whitespace-nowrap transition-all active:scale-95 font-medium shadow-sm hover:shadow-md" title="Lihat status stok produk">
                    📦 Stok
                </button>
                <button @click="quickAction('sales-today', '🛍️ Transaksi hari ini')" class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs rounded-full whitespace-nowrap transition-all active:scale-95 font-medium shadow-sm hover:shadow-md" title="Lihat daftar penjualan hari ini">
                    🛍️ Transaksi
                </button>
                <button @click="quickAction('products', '📋 Daftar produk')" class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs rounded-full whitespace-nowrap transition-all active:scale-95 font-medium shadow-sm hover:shadow-md" title="Lihat semua produk yang tersedia">
                    📋 Produk
                </button>
                <button @click="quickAction('low-stock', '⚠️ Produk stok rendah')" class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs rounded-full whitespace-nowrap transition-all active:scale-95 font-medium shadow-sm hover:shadow-md" title="Lihat produk dengan stok rendah">
                    ⚠️ Alert
                </button>
            </div>
        </div>

        <!-- Input Area - Modern Design -->
        <div class="border-t border-gray-100 p-4 bg-gradient-to-t from-blue-50 to-white">
            <div class="flex gap-2">
                <input 
                    type="text"
                    id="messageInput"
                    placeholder="Ketik pertanyaan Anda..."
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-800 placeholder-gray-400 transition-all"
                    @keydown.enter="sendMessage()"
                    :disabled="isLoading || isHistoryLoading"
                />
                <button 
                    @click="sendMessage()"
                    :disabled="isLoading || isHistoryLoading"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-2xl transition-all disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-sm shadow-md hover:shadow-lg flex items-center justify-center"
                    title="Kirim pesan (Enter)"
                >
                    <!-- Send Icon -->
                    <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <!-- Loading Spinner -->
                    <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 text-right mt-2">💡 Deby AI sudah otomatis tahu data bisnismu — tanyakan apa saja!</p>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100000]" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm mx-4" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-800">Hapus Riwayat Chat?</h3>
                    <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            
            <p class="text-sm text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus semua riwayat chat? Data yang dihapus tidak dapat dipulihkan.
            </p>
            
            <div class="flex gap-3">
                <button @click="showDeleteConfirm = false" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                    Batal
                </button>
                <button @click="deleteAllChatHistory()" :disabled="isDeleting" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition font-medium flex items-center justify-center gap-2">
                    <svg x-show="!isDeleting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <svg x-show="isDeleting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isDeleting ? 'Menghapus...' : 'Hapus Semua'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('debyAIWidget', () => ({
        isOpen: false,
        isLoading: false,
        isTyping: false,
        isDeleting: false,
        isHistoryLoading: false,
        messages: [],
        chatHistoryLoaded: false,
        showDeleteConfirm: false,

        init() {
            console.log('DEBY AI Widget initialized');
            this.isOpen = false;
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                // Load chat history hanya sekali saat pertama kali dibuka
                if (!this.chatHistoryLoaded) {
                    this.isHistoryLoading = true;
                    setTimeout(() => this.loadChatHistory(), 300);
                }
                // Focus input setelah popup terbuka
                setTimeout(() => {
                    const input = document.getElementById('messageInput');
                    if (input) input.focus();
                }, 400);
            }
        },


        async sendMessage() {
            const input = document.getElementById('messageInput');
            if (!input) return;

            const message = input.value.trim();
            if (!message || this.isLoading || this.isHistoryLoading) return;

            this.isLoading = true;
            input.value = '';

            this.addMessageToUI('user', message);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const token = csrfToken ? csrfToken.content : '';

                this.isTyping = true;
                this.addTypingIndicator();

                const baseDelay = 800;
                const adaptiveDelay = Math.min(message.length * 20, 2500);
                const typingDelay = baseDelay + adaptiveDelay;

                const response = await fetch('/api/deby-ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ message: message }),
                });

                const data = await response.json();

                await new Promise(resolve => setTimeout(resolve, typingDelay));
                this.removeTypingIndicator();
                this.isTyping = false;

                if (data.success) {
                    const meta = data.metadata || {};
                    this.addMessageToUI(
                        'assistant',
                        data.message,
                        meta.table_data || null,
                        meta.action_link || null,
                        meta.action_label || null
                    );

                    this.messages.push({ role: 'user', message: message });
                    this.messages.push({ role: 'assistant', message: data.message, metadata: meta });
                } else {
                    this.addMessageToUI('assistant', data.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('DEBY AI Error:', error);
                this.removeTypingIndicator();
                this.isTyping = false;
                this.addMessageToUI('assistant', 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi. 😔');
            } finally {
                this.isLoading = false;
                setTimeout(() => {
                    const input = document.getElementById('messageInput');
                    if (input) input.focus();
                }, 100);
            }
        },

        async quickAction(type, userLabel) {
            if (this.isLoading || this.isHistoryLoading) return;

            this.isLoading = true;
            this.addMessageToUI('user', userLabel);

            try {
                this.isTyping = true;
                this.addTypingIndicator();

                const response = await fetch(`/api/deby-ai/quick/${type}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });

                await new Promise(resolve => setTimeout(resolve, 400));
                const data = await response.json();

                this.removeTypingIndicator();
                this.isTyping = false;

                if (data.success) {
                    const meta = data.metadata || {};
                    this.addMessageToUI(
                        'assistant',
                        data.message,
                        meta.table_data || null,
                        meta.action_link || null,
                        meta.action_label || null
                    );

                    this.messages.push({ role: 'user', message: userLabel });
                    this.messages.push({ role: 'assistant', message: data.message, metadata: meta });
                } else {
                    this.addMessageToUI('assistant', data.message || 'Tidak dapat memuat data.');
                }
            } catch (error) {
                console.error('Quick Action Error:', error);
                this.removeTypingIndicator();
                this.isTyping = false;
                this.addMessageToUI('assistant', 'Maaf, gagal mengambil data. Silakan coba lagi.');
            } finally {
                this.isLoading = false;
            }
        },

        addTypingIndicator() {
            const container = document.getElementById('messagesContainer');
            if (!container) return;

            const typingEl = document.createElement('div');
            typingEl.id = 'typingIndicator';
            typingEl.className = 'flex gap-3 animate-fadeIn';
            typingEl.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 ring-2 ring-blue-300/50 flex-shrink-0 flex items-center justify-center text-white shadow-md">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3M8 7h8a2 2 0 012 2v6a4 4 0 01-4 4h-4a4 4 0 01-4-4V9a2 2 0 012-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M15 12h.01" />
                    </svg>
                </div>
                <div class="bg-gray-100 rounded-2xl p-3 shadow-md">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                    </div>
                </div>
            `;

            container.appendChild(typingEl);
            container.scrollTop = container.scrollHeight;
        },

        removeTypingIndicator() {
            const typingEl = document.getElementById('typingIndicator');
            if (typingEl) {
                typingEl.remove();
            }
        },

        addMessageToUI(role, message, tableData = null, actionLink = null, actionLabel = null) {
            const container = document.getElementById('messagesContainer');
            if (!container) return;

            const messageEl = document.createElement('div');
            messageEl.className = 'flex gap-3 animate-fadeIn';

            if (role === 'user') {
                messageEl.innerHTML = `
                    <div class="flex-1"></div>
                    <div class="bg-blue-600 text-white rounded-2xl p-3 shadow-md max-w-sm hover:shadow-lg transition-shadow">
                        <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">${this.escapeHtml(message)}</p>
                    </div>
                `;
            } else {
                // Format message dengan markdown-like styling
                const formattedMessage = this.formatMessage(message);
                
                let tableHtml = '';
                // Ensure tableData is an array and has content
                if (tableData && Array.isArray(tableData) && tableData.length > 0) {
                    // Validate that each row is an array
                    const validRows = tableData.filter(row => Array.isArray(row) && row.length > 0);
                    
                    if (validRows.length > 0) {
                        tableHtml = `
                            <div class="mt-3 overflow-x-auto">
                                <table class="text-xs border-collapse w-full">
                                    <tbody>
                                        ${validRows.map((row, idx) => `
                                            <tr class="${idx === 0 ? 'bg-blue-100 font-bold' : idx === validRows.length - 1 ? 'bg-gray-100 font-bold border-t border-gray-300' : 'border-b border-gray-200'}">
                                                ${row.map(cell => `<td class="px-2 py-1 text-gray-800">${this.escapeHtml(String(cell))}</td>`).join('')}
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                }

                let actionHtml = '';
                const safeLink = typeof actionLink === 'string' && (actionLink.startsWith('/') || actionLink.startsWith('http'));
                if (safeLink && actionLabel) {
                    const safeHref = this.escapeHtml(actionLink);
                    const safeLabel = this.escapeHtml(actionLabel);
                    actionHtml = `
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <a href="${safeHref}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                ${safeLabel}
                            </a>
                        </div>
                    `;
                }

                messageEl.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 ring-2 ring-blue-300/50 flex-shrink-0 flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3M8 7h8a2 2 0 012 2v6a4 4 0 01-4 4h-4a4 4 0 01-4-4V9a2 2 0 012-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M15 12h.01" />
                        </svg>
                    </div>
                    <div class="bg-gray-100 rounded-2xl p-3 shadow-md max-w-md hover:shadow-lg transition-shadow">
                        <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap break-words">${formattedMessage}</div>
                        ${tableHtml}
                        ${actionHtml}
                    </div>
                `;
            }

            container.appendChild(messageEl);
            // Smooth scroll to bottom
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 50);
        },

        formatMessage(message) {
            // Escape HTML first
            let formatted = this.escapeHtml(message);
            
            // Format bold text **text** -> <strong>text</strong>
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900">$1</strong>');
            
            // Format italic text *text* -> <em>text</em>
            formatted = formatted.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
            
            // Format line breaks
            formatted = formatted.replace(/\n/g, '<br>');
            
            // Format bullet points
            formatted = formatted.replace(/^• (.*?)$/gm, '<span class="block ml-2">• $1</span>');
            
            return formatted;
        },

        async loadChatHistory() {
            try {
                const response = await fetch('/api/deby-ai/chat-history');
                
                if (!response.ok) {
                    console.error(`HTTP error! status: ${response.status}`);
                    this.chatHistoryLoaded = true;
                    return;
                }
                
                const data = await response.json();
                
                console.log('📥 Chat history response:', data);
                
                if (data.success && data.history && data.history.length > 0) {
                    console.log(`📊 Total messages to load: ${data.count}`);
                    console.log('📋 Message sequence:', data.history.map((m, i) => `${i+1}. ${m.role} (${m.created_at})`).join(' → '));
                    
                    // Load previous messages from database (append to existing welcome message)
                    data.history.forEach((msg, index) => {
                        // Extract metadata if exists
                        let tableData = null;
                        let actionLink = null;
                        let actionLabel = null;
                        
                        if (msg.metadata && typeof msg.metadata === 'object') {
                            tableData = msg.metadata.table_data || null;
                            actionLink = msg.metadata.action_link || null;
                            actionLabel = msg.metadata.action_label || null;
                        }
                        
                        // Add message to UI with table data if available
                        this.addMessageToUI(msg.role, msg.message, tableData, actionLink, actionLabel);
                        
                        console.log(`📨 [${index + 1}/${data.count}] ${msg.role.toUpperCase()} - ${msg.message.substring(0, 50)}... (Table: ${tableData ? 'Yes' : 'No'}, Created: ${msg.created_at})`);
                    });
                    
                        console.log(`✅ Successfully loaded ${data.count} chat messages from database`);
                } else {
                    console.log('📭 No chat history found in database');
                }
            } catch (error) {
                console.error('Error loading chat history from database:', error);
            } finally {
                this.chatHistoryLoaded = true;
                this.isHistoryLoading = false;
            }
        },

        async saveChatLog(role, message, thinkingMode = false, metadata = null) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const token = csrfToken ? csrfToken.content : '';

                const payload = {
                    role: role,
                    message: message,
                    thinking_mode: thinkingMode,
                };
                if (metadata !== null) {
                    payload.metadata = metadata;
                }

                console.log(`💾 Saving chat log - Role: ${role}, Message: "${message.substring(0, 50)}...", Thinking: ${thinkingMode}, Has metadata: ${metadata ? 'Yes' : 'No'}`);

                const response = await fetch('/api/deby-ai/save-log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    console.error(`HTTP error! status: ${response.status}`);
                    return false;
                }

                const data = await response.json();
                
                if (data.success) {
                    console.log(`✅ Chat log saved successfully - ${role.toUpperCase()}`);
                    return true;
                } else {
                    console.warn('⚠️ Failed to save chat log:', data.message);
                    return false;
                }
            } catch (error) {
                console.error('Error saving chat log:', error);
                return false;
            }
        },

        async deleteAllChatHistory() {
            this.isDeleting = true;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const token = csrfToken ? csrfToken.content : '';

                const response = await fetch('/api/deby-ai/chat-history', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                console.log('Delete response:', data);
                
                if (data.success) {
                    console.log(`🗑️ Deleted ${data.deleted_count} chat messages`);
                    
                    // Clear messages from UI
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.innerHTML = `
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 ring-2 ring-blue-300/50 flex-shrink-0 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3M8 7h8a2 2 0 012 2v6a4 4 0 01-4 4h-4a4 4 0 01-4-4V9a2 2 0 012-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M15 12h.01" />
                                    </svg>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                    <p class="text-sm text-gray-800 font-medium">
                                        Halo! 👋 Saya DEBY AI, asisten bisnis Anda.
                                    </p>
                                    <p class="text-xs text-gray-600 mt-2">Riwayat chat telah dihapus. Mari mulai percakapan baru!</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    this.messages = [];
                    this.showDeleteConfirm = false;
                    this.chatHistoryLoaded = false; // Reset flag agar bisa load history lagi
                    
                } else {
                    console.error('Delete failed:', data);
                }
            } catch (error) {
                console.error('Error deleting chat history:', error);
            } finally {
                this.isDeleting = false;
            }
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
    }));
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-slideUp {
    animation: slideUp 0.5s ease-out;
}

#messagesContainer {
    scroll-behavior: smooth;
}

#messagesContainer::-webkit-scrollbar {
    width: 6px;
}

#messagesContainer::-webkit-scrollbar-track {
    background: transparent;
}

#messagesContainer::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#messagesContainer::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions for all elements */
* {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>