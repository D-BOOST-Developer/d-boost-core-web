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

                        {{-- Actions --}}
                        <div class="space-y-2 pt-4 border-t">
                            <button type="button" @click="saveConfig()" :disabled="saving"
                                class="w-full bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium py-2 px-4 rounded-lg disabled:opacity-50">
                                <span x-show="!saving">Save Configuration</span>
                                <span x-show="saving">Saving…</span>
                            </button>
                            <button type="button" @click="publishStorefront()"
                                class="w-full border-2 border-[#006BFF] hover:bg-[#006BFF] text-[#006BFF] hover:text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                <span
                                    x-text="config.is_published ? 'Unpublish Storefront' : 'Publish Storefront'"></span>
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
                        is_published: {{ $config->is_published ? 'true' : 'false' }}
                                },
                    saving: false,

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
                                showToast(data.message, 'success');
                                if (data.is_published && data.public_url) {
                                    setTimeout(() => {
                                        if (confirm('Open public storefront in new tab?')) {
                                            window.open(data.public_url, '_blank');
                                        }
                                    }, 500);
                                }
                            } else {
                                showToast(data.message || 'Failed to update publish status', 'error');
                            }
                        } catch (error) {
                            showToast('Error: ' + error.message, 'error');
                        }
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>