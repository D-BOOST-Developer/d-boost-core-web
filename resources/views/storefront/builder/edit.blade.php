<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#F1F0FF] leading-tight">
            Customize Storefront
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-8 shadow-lg" x-data="builderData()"
                @submit.prevent="saveConfig">

                {{-- MONŌ active notice --}}
                <div class="mb-6 px-4 py-3 bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] text-sm rounded-lg flex items-start gap-3">
                    <span class="font-serif text-lg leading-none text-[#A78BFA]">○</span>
                    <div>
                        <p class="font-medium text-[#F1F0FF]">Tampilan minimalis MONŌ aktif untuk semua storefront.</p>
                        <p class="text-xs text-[#A9A8C3] mt-0.5">Semua kustomisasi (judul, warna aksen, teks, footer)
                            berlaku dengan layout MONŌ. Pratinjau langsung di samping.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left: Controls --}}
                    <div class="lg:col-span-1 space-y-6">
                        <h3 class="text-lg font-bold text-[#F1F0FF]">Settings</h3>

                        {{-- Storefront Title --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Storefront Title *</label>
                            <input type="text" x-model="config.storefront_title" required
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA]">
                        </div>

                        {{-- Tagline --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Tagline</label>
                            <input type="text" x-model="config.tagline" placeholder="Slogan bisnis Anda…"
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C]">
                        </div>

                        {{-- Accent colors --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-1">Accent Colors</label>
                            <p class="text-xs text-[#7E7D9C] mb-3">Digunakan sebagai aksen halus pada cart badge, focus
                                ring, dan elemen interaktif.</p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-[#A9A8C3]">Primary Accent</span>
                                    <input type="color" x-model="config.primary_color"
                                        class="h-10 w-20 rounded cursor-pointer bg-[#111118] border border-[#2A2A3A]">
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-[#A9A8C3]">Secondary Accent</span>
                                    <input type="color" x-model="config.secondary_color"
                                        class="h-10 w-20 rounded cursor-pointer bg-[#111118] border border-[#2A2A3A]">
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-[#A9A8C3]">Text Accent</span>
                                    <input type="color" x-model="config.text_color"
                                        class="h-10 w-20 rounded cursor-pointer bg-[#111118] border border-[#2A2A3A]">
                                </div>
                            </div>
                        </div>

                        {{-- Layout --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Grid Columns</label>
                            <select x-model="config.grid_columns"
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA]">
                                <option value="2">2 Columns</option>
                                <option value="3">3 Columns</option>
                                <option value="4">4 Columns</option>
                                <option value="5">5 Columns</option>
                            </select>
                        </div>

                        {{-- Card Style --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Card Style</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="config.card_style = 'shadow'"
                                    :class="config.card_style === 'shadow' ? 'border-[#A78BFA] bg-[#A78BFA]/15 text-[#F1F0FF]' : 'border-[#2A2A3A] bg-[#111118] text-[#A9A8C3]'"
                                    class="border-2 rounded-lg p-3 text-xs font-medium transition-colors">Shadow</button>
                                <button type="button" @click="config.card_style = 'bordered'"
                                    :class="config.card_style === 'bordered' ? 'border-[#A78BFA] bg-[#A78BFA]/15 text-[#F1F0FF]' : 'border-[#2A2A3A] bg-[#111118] text-[#A9A8C3]'"
                                    class="border-2 rounded-lg p-3 text-xs font-medium transition-colors">Bordered</button>
                                <button type="button" @click="config.card_style = 'flat'"
                                    :class="config.card_style === 'flat' ? 'border-[#A78BFA] bg-[#A78BFA]/15 text-[#F1F0FF]' : 'border-[#2A2A3A] bg-[#111118] text-[#A9A8C3]'"
                                    class="border-2 rounded-lg p-3 text-xs font-medium transition-colors">Flat</button>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-3">Landing Page Content</label>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-[#A9A8C3] mb-1">Header Text</label>
                                    <textarea x-model="config.header_text" rows="2"
                                        placeholder="Tagline panjang di hero (opsional)…"
                                        class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs text-[#A9A8C3] mb-1">About Text</label>
                                    <textarea x-model="config.about_text" rows="3"
                                        placeholder="Ceritakan tentang bisnis Anda…"
                                        class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs text-[#A9A8C3] mb-1">Newsletter CTA</label>
                                    <input type="text" x-model="config.cta_text"
                                        placeholder="Misal: Koleksi baru setiap minggu."
                                        class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C]">
                                </div>
                            </div>
                        </div>

                        {{-- Footer Content --}}
                        <div>
                            <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Footer / Contact Info</label>
                            <textarea x-model="config.footer_content" rows="3"
                                placeholder="Alamat, jam operasional, dll…"
                                class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C]"></textarea>
                        </div>

                        {{-- Publish Status Badge --}}
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg"
                            :class="config.is_published ? 'bg-emerald-950/40 border border-emerald-800/40' : 'bg-[#111118] border border-[#2A2A3A]'">
                            <span class="w-2 h-2 rounded-full flex-shrink-0"
                                :class="config.is_published ? 'bg-emerald-400 animate-pulse' : 'bg-gray-500'"></span>
                            <span class="text-sm font-medium"
                                :class="config.is_published ? 'text-emerald-400' : 'text-[#7E7D9C]'"
                                x-text="config.is_published ? 'Status: Published ✓' : 'Status: Draft'"></span>
                        </div>

                        {{-- Public URL (shown when published) --}}
                        <div x-show="config.is_published && config.public_url" x-cloak
                            class="bg-[#111118] border border-[#A78BFA]/30 rounded-lg p-3">
                            <p class="text-xs font-semibold text-[#A78BFA] mb-1.5">🔗 Link Publik Storefront:</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 bg-[#0A0A0F] border border-[#2A2A3A] rounded px-2 py-1 text-xs text-emerald-300 truncate font-mono"
                                    x-text="config.public_url"></code>
                                <button type="button" @click="copyPublicUrl()"
                                    class="flex-shrink-0 px-2.5 py-1 bg-[#A78BFA] hover:bg-[#9333EA] text-[#0A0A0F] text-xs rounded font-bold transition">
                                    Copy
                                </button>
                            </div>
                            <a :href="config.public_url" target="_blank"
                                class="inline-flex items-center gap-1 mt-2 text-xs text-[#A78BFA] hover:text-[#C4B5FD] font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Buka Storefront Publik
                            </a>
                        </div>

                        {{-- Actions --}}
                        <div class="space-y-2 pt-4 border-t border-[#2A2A3A]">
                            <button type="button" @click="saveConfig()" :disabled="saving"
                                class="w-full bg-[#A78BFA] hover:bg-[#9333EA] text-[#0A0A0F] font-bold py-2 px-4 rounded-lg disabled:opacity-50 transition-opacity shadow-lg shadow-[#A78BFA]/20">
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
                                    ? 'border-red-400 text-red-400 hover:bg-red-950/40'
                                    : 'border-[#A78BFA] text-[#A78BFA] hover:bg-[#A78BFA]/10'">
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
                                class="block w-full text-center border border-[#2A2A3A] bg-[#111118] hover:bg-[#252535] text-[#A9A8C3] hover:text-[#F1F0FF] font-medium py-2 px-4 rounded-lg transition-colors">
                                Open Preview in New Tab
                            </a>
                            <button type="button" @click="reloadPreview()"
                                class="w-full text-center border border-[#2A2A3A] bg-[#111118] hover:bg-[#252535] text-[#A9A8C3] hover:text-[#F1F0FF] font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                ↻ Reload Preview
                            </button>
                        </div>
                    </div>

                    {{-- Right: Live MONŌ Preview --}}
                    <div class="lg:col-span-2">
                        <div class="sticky top-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-[#F1F0FF]">Live Preview · MONŌ</h3>
                                <span
                                    class="text-xs px-2.5 py-1 rounded-full bg-[#252535] text-[#A78BFA] border border-[#3A3A4E] tracking-wider uppercase font-semibold">
                                    ○ MONŌ
                                </span>
                            </div>

                            <div class="border-2 border-[#2A2A3A] rounded-2xl overflow-hidden bg-[#111118] shadow-2xl">
                                <iframe x-ref="previewFrame" src="{{ route('storefront.preview') }}"
                                    class="w-full h-[760px] bg-[#0E0E16]" loading="lazy"></iframe>
                            </div>

                            <p class="text-xs text-[#7E7D9C] mt-3 text-center">
                                💡 Simpan konfigurasi terlebih dahulu, lalu klik <span class="font-medium text-[#A9A8C3]">Reload
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
                                    'Accept': 'application/json',
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