<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customize Storefront
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <div class="bg-white border border-gray-200 rounded-xl p-8" x-data="builderData()"
                @submit.prevent="saveConfig">

                {{-- MONŌ active notice --}}
                <div class="mb-6 px-4 py-3 bg-gray-900 text-white text-sm rounded-lg flex items-start gap-3">
                    <span class="font-serif text-lg leading-none">○</span>
                    <div>
                        <p class="font-medium">Tampilan minimalis MONŌ aktif untuk semua storefront.</p>
                        <p class="text-xs text-white/70 mt-0.5">Semua kustomisasi (judul, warna aksen, teks, footer)
                            berlaku dengan layout MONŌ. Pratinjau langsung di samping.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left: Controls --}}
                    <div class="lg:col-span-1 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900">Settings</h3>

                        {{-- Storefront Title --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storefront Title *</label>
                            <input type="text" x-model="config.storefront_title" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                        </div>

                        {{-- Tagline --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                            <input type="text" x-model="config.tagline" placeholder="Slogan bisnis Anda…"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                        </div>

                        {{-- Accent colors --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Accent Colors</label>
                            <p class="text-xs text-gray-500 mb-3">Digunakan sebagai aksen halus pada cart badge, focus
                                ring, dan elemen interaktif.</p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Primary Accent</span>
                                    <input type="color" x-model="config.primary_color"
                                        class="h-10 w-20 rounded cursor-pointer">
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Secondary Accent</span>
                                    <input type="color" x-model="config.secondary_color"
                                        class="h-10 w-20 rounded cursor-pointer">
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Text Accent</span>
                                    <input type="color" x-model="config.text_color"
                                        class="h-10 w-20 rounded cursor-pointer">
                                </div>
                            </div>
                        </div>

                        {{-- Layout --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grid Columns</label>
                            <select x-model="config.grid_columns"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                                <option value="2">2 Columns</option>
                                <option value="3">3 Columns</option>
                                <option value="4">4 Columns</option>
                                <option value="5">5 Columns</option>
                            </select>
                        </div>

                        {{-- Card Style --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Style</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="config.card_style = 'shadow'"
                                    :class="config.card_style === 'shadow' ? 'border-[#006BFF] bg-blue-50' : 'border-gray-300'"
                                    class="border-2 rounded-lg p-3 text-xs font-medium">Shadow</button>
                                <button type="button" @click="config.card_style = 'bordered'"
                                    :class="config.card_style === 'bordered' ? 'border-[#006BFF] bg-blue-50' : 'border-gray-300'"
                                    class="border-2 rounded-lg p-3 text-xs font-medium">Bordered</button>
                                <button type="button" @click="config.card_style = 'flat'"
                                    :class="config.card_style === 'flat' ? 'border-[#006BFF] bg-blue-50' : 'border-gray-300'"
                                    class="border-2 rounded-lg p-3 text-xs font-medium">Flat</button>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Landing Page Content</label>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Header Text</label>
                                    <textarea x-model="config.header_text" rows="2"
                                        placeholder="Tagline panjang di hero (opsional)…"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">About Text</label>
                                    <textarea x-model="config.about_text" rows="3"
                                        placeholder="Ceritakan tentang bisnis Anda…"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Newsletter CTA</label>
                                    <input type="text" x-model="config.cta_text"
                                        placeholder="Misal: Koleksi baru setiap minggu."
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF]">
                                </div>
                            </div>
                        </div>

                        {{-- Footer Content --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Footer / Contact Info</label>
                            <textarea x-model="config.footer_content" rows="3"
                                placeholder="Alamat, jam operasional, dll…"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]"></textarea>
                        </div>

                        {{-- Publish Status Badge --}}
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg"
                            :class="config.is_published ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200'">
                            <span class="w-2 h-2 rounded-full flex-shrink-0"
                                :class="config.is_published ? 'bg-green-500 animate-pulse' : 'bg-gray-400'"></span>
                            <span class="text-sm font-medium"
                                :class="config.is_published ? 'text-green-700' : 'text-gray-600'"
                                x-text="config.is_published ? 'Status: Published ✓' : 'Status: Draft'"></span>
                        </div>

                        {{-- Public URL (shown when published) --}}
                        <div x-show="config.is_published && config.public_url" x-cloak
                            class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-xs font-semibold text-blue-800 mb-1.5">🔗 Link Publik Storefront:</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 bg-white border border-blue-200 rounded px-2 py-1 text-xs text-gray-700 truncate"
                                    x-text="config.public_url"></code>
                                <button type="button" @click="copyPublicUrl()"
                                    class="flex-shrink-0 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded font-medium transition">
                                    Copy
                                </button>
                            </div>
                            <a :href="config.public_url" target="_blank"
                                class="inline-flex items-center gap-1 mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Buka Storefront Publik
                            </a>
                        </div>

                        {{-- Actions --}}
                        <div class="space-y-2 pt-4 border-t">
                            <button type="button" @click="saveConfig()" :disabled="saving"
                                class="w-full bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium py-2 px-4 rounded-lg disabled:opacity-50 transition-opacity">
                                <span x-show="!saving">Save Configuration</span>
                                <span x-show="saving" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Saving…
                                </span>
                            </button>
                            <button type="button" @click="publishStorefront()" :disabled="publishing"
                                class="w-full border-2 font-medium py-2 px-4 rounded-lg transition-all disabled:opacity-50"
                                :class="config.is_published
                                    ? 'border-red-400 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600'
                                    : 'border-[#006BFF] text-[#006BFF] hover:bg-[#006BFF] hover:text-white'">
                                <span x-show="!publishing"
                                    x-text="config.is_published ? '⊘ Unpublish Storefront' : '▶ Publish Storefront'"></span>
                                <span x-show="publishing" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span x-text="config.is_published ? 'Unpublishing…' : 'Publishing…'"></span>
                                </span>
                            </button>
                            <a href="{{ route('storefront.preview') }}" target="_blank"
                                class="block w-full text-center border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg">
                                Open Preview in New Tab
                            </a>
                            <button type="button" @click="reloadPreview()"
                                class="w-full text-center border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium py-2 px-4 rounded-lg text-sm">
                                ↻ Reload Preview
                            </button>
                        </div>
                    </div>

                    {{-- Right: Live MONŌ Preview --}}
                    <div class="lg:col-span-2">
                        <div class="sticky top-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Live Preview · MONŌ</h3>
                                <span
                                    class="text-xs px-2.5 py-1 rounded-full bg-gray-900 text-white tracking-wider uppercase">
                                    ○ MONŌ
                                </span>
                            </div>

                            <div class="border-2 border-gray-200 rounded-2xl overflow-hidden bg-gray-100 shadow-xl">
                                <iframe x-ref="previewFrame" src="{{ route('storefront.preview') }}"
                                    class="w-full h-[760px] bg-white" loading="lazy"></iframe>
                            </div>

                            <p class="text-xs text-gray-500 mt-3 text-center">
                                💡 Simpan konfigurasi terlebih dahulu, lalu klik <span class="font-medium">Reload
                                    Preview</span> untuk memuat perubahan terbaru.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function builderData() {
                const textBlocks = @json($config->text_blocks ?? '{}');
                const parsedBlocks = typeof textBlocks === 'string' ? JSON.parse(textBlocks) : textBlocks;

                return {
                    config: {
                        storefront_title: @json($config->storefront_title ?? ''),
                        tagline: @json($config->tagline ?? ''),
                        primary_color: @json($config->primary_color ?? '#0a0a0a'),
                        secondary_color: @json($config->secondary_color ?? '#888888'),
                        text_color: @json($config->text_color ?? '#0a0a0a'),
                        grid_columns: {{ $config->grid_columns ?? 4 }},
                        card_style: @json($config->card_style ?? 'flat'),
                        header_text: parsedBlocks.header_text || '',
                        about_text: parsedBlocks.about_text || '',
                        cta_text: parsedBlocks.cta_text || '',
                        footer_content: @json($config->footer_content ?? ''),
                        is_published: {{ ($config && $config->is_published) ? 'true' : 'false' }},
                        public_url: @json($config && $config->is_published ? $config->public_url : null),
                    },
                    saving: false,
                    publishing: false,

                    reloadPreview() {
                        if (this.$refs.previewFrame) {
                            this.$refs.previewFrame.src = this.$refs.previewFrame.src;
                        }
                    },

                    async saveConfig() {
                        this.saving = true;
                        try {
                            const response = await fetch('{{ route('storefront.builder.update') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(this.config)
                            });
                            const data = await response.json();
                            if (data.success) {
                                showToast(data.message, 'success');
                                this.reloadPreview();
                            } else {
                                showToast(data.message || 'Failed to save configuration', 'error');
                            }
                        } catch (error) {
                            showToast('Error: ' + error.message, 'error');
                        } finally {
                            this.saving = false;
                        }
                    },

                    async publishStorefront() {
                        this.publishing = true;
                        try {
                            const action = this.config.is_published ? 'unpublish' : 'publish';
                            const response = await fetch('{{ route('storefront.builder.publish') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ action })
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.config.is_published = data.is_published;
                                this.config.public_url = data.public_url || null;
                                showToast(data.message, 'success');
                            } else {
                                showToast(data.message || 'Failed to update publish status', 'error');
                            }
                        } catch (error) {
                            showToast('Error: ' + error.message, 'error');
                        } finally {
                            this.publishing = false;
                        }
                    },

                    copyPublicUrl() {
                        if (!this.config.public_url) return;
                        navigator.clipboard.writeText(this.config.public_url).then(() => {
                            showToast('Link berhasil disalin ke clipboard!', 'success');
                        }).catch(() => {
                            // Fallback
                            const el = document.createElement('textarea');
                            el.value = this.config.public_url;
                            document.body.appendChild(el);
                            el.select();
                            document.execCommand('copy');
                            document.body.removeChild(el);
                            showToast('Link berhasil disalin!', 'success');
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>