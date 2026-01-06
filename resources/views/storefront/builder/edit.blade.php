<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customize Landing Page
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <div class="bg-white border border-gray-200 rounded-xl p-8" x-data="builderData()" @submit.prevent="saveConfig">
                

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left: Controls -->
                        <div class="lg:col-span-1 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900">Settings</h3>

                            <!-- Storefront Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Storefront Title *</label>
                                <input type="text" x-model="config.storefront_title" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                            </div>

                            <!-- Tagline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                                <input type="text" x-model="config.tagline"
                                       placeholder="Your business slogan..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                            </div>

                            <!-- Colors -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Theme Colors</label>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Primary Color</span>
                                        <input type="color" x-model="config.primary_color" 
                                               class="h-10 w-20 rounded cursor-pointer">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Secondary Color</span>
                                        <input type="color" x-model="config.secondary_color" 
                                               class="h-10 w-20 rounded cursor-pointer">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Text Color</span>
                                        <input type="color" x-model="config.text_color" 
                                               class="h-10 w-20 rounded cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <!-- Layout -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grid Columns</label>
                                <select x-model="config.grid_columns" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]">
                                    <option value="2">2 Columns</option>
                                    <option value="3">3 Columns</option>
                                    <option value="4">4 Columns</option>
                                </select>
                            </div>

                            <!-- Card Style -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Style</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="config.card_style = 'shadow'"
                                            :class="config.card_style === 'shadow' ? 'border-[#006BFF] bg-blue-50' : 'border-gray-300'"
                                            class="border-2 rounded-lg p-3 text-xs font-medium">
                                        Shadow
                                    </button>
                                    <button type="button" @click="config.card_style = 'bordered'"
                                            :class="config.card_style === 'bordered' ? 'border-[#006BFF] bg-blue-50' : 'border-gray-300'"
                                            class="border-2 rounded-lg p-3 text-xs font-medium">
                                        Bordered
                                    </button>
                                    <button type="button" @click="config.card_style = 'flat'"
                                            :class="config.card_style === 'flat' ? 'border-[#006BFF] bg-blue-50' : 'border-gray-300'"
                                            class="border-2 rounded-lg p-3 text-xs font-medium">
                                        Flat
                                    </button>
                                </div>
                            </div>

                            <!-- Content Sections -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Landing Page Content</label>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Header Text</label>
                                        <textarea x-model="config.header_text" rows="2"
                                                  placeholder="Additional text below tagline..."
                                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF]"></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">About Text</label>
                                        <textarea x-model="config.about_text" rows="3"
                                                  placeholder="Tell about your business..."
                                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF]"></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Call-to-Action Text</label>
                                        <input type="text" x-model="config.cta_text"
                                               placeholder="Call to action for customers..."
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#006BFF]">
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Content -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Footer Content</label>
                                <textarea x-model="config.footer_content" rows="3"
                                          placeholder="Contact info, address, etc..."
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF]"></textarea>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2 pt-4 border-t">
                                <button type="button" @click="saveConfig()"
                                        :disabled="saving"
                                        class="w-full bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium py-2 px-4 rounded-lg disabled:opacity-50">
                                    <span x-show="!saving">Save Configuration</span>
                                    <span x-show="saving">Saving...</span>
                                </button>

                                <button type="button" @click="publishStorefront()"
                                        class="w-full border-2 border-[#006BFF] hover:bg-[#006BFF] text-[#006BFF] hover:text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    <span x-text="config.is_published ? 'Unpublish Storefront' : 'Publish Storefront'"></span>
                                </button>

                                <a href="{{ route('storefront.preview') }}" target="_blank"
                                   class="block w-full text-center border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg">
                                    Preview
                                </a>
                            </div>
                        </div>

                        <!-- Right: Preview -->
                        <div class="lg:col-span-2">
                            <div class="sticky top-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-900">Preview Landing Page</h3>
                                    <span class="text-xs text-gray-500">Live Preview</span>
                                </div>
                                
                                <div class="border-2 border-gray-300 rounded-xl overflow-hidden bg-white shadow-lg max-h-[800px] overflow-y-auto">
                                    <!-- Preview Header dengan Gradient -->
                                    <div class="relative overflow-hidden min-h-[300px] flex items-center"
                                         :style="`background: linear-gradient(-45deg, ${config.primary_color}, ${config.secondary_color || '#10B981'}, ${config.primary_color}dd, ${config.secondary_color || '#10B981'}dd); background-size: 400% 400%;`">
                                        
                                        <!-- Decorative circles -->
                                        <div class="absolute top-5 right-5 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                                        <div class="absolute bottom-5 left-5 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                                        
                                        <div class="relative w-full text-center px-6 py-12 z-10">
                                            <h1 class="text-4xl font-extrabold text-white mb-3 drop-shadow-lg" 
                                                x-text="config.storefront_title || 'My Storefront'"></h1>
                                            <p class="text-lg text-white/95 mb-2 drop-shadow" 
                                               x-text="config.tagline || 'Your tagline here'"></p>
                                            
                                            <p class="text-sm text-white/90 max-w-lg mx-auto mt-3" 
                                               x-show="config.header_text"
                                               x-text="config.header_text"></p>
                                               
                                            <div class="mt-6">
                                                <div class="inline-block px-6 py-2 bg-white text-gray-900 font-semibold rounded-full shadow-lg text-sm">
                                                    View Our Products ↓
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Wave divider -->
                                        <div class="absolute bottom-0 left-0 w-full">
                                            <svg viewBox="0 0 1440 60" fill="none" class="w-full">
                                                <path d="M0 0L60 5C120 10 240 20 360 23C480 27 600 23 720 22C840 20 960 20 1080 23C1200 27 1320 33 1380 37L1440 40V60H0V0Z" fill="white"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- About Section Preview -->
                                    <div class="bg-white px-6 py-8" x-show="config.about_text">
                                        <div class="text-center max-w-2xl mx-auto">
                                            <h2 class="text-2xl font-bold text-gray-900 mb-3">About Us</h2>
                                            <p class="text-sm text-gray-700" x-text="config.about_text"></p>
                                        </div>
                                    </div>

                                    <!-- Products Section Preview -->
                                    <div class="bg-gray-50 px-6 py-8">
                                        <div class="text-center mb-6">
                                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Our Products</h2>
                                            <p class="text-sm text-gray-600">Complete collection of quality products</p>
                                        </div>
                                        
                                        <div :class="`grid gap-4 ${config.grid_columns == 2 ? 'grid-cols-2' : config.grid_columns == 3 ? 'grid-cols-3' : 'grid-cols-4'}`">
                                            @foreach($links->take(6) as $link)
                                            <div class="bg-white rounded-xl overflow-hidden transition-all hover:shadow-lg"
                                                 :class="{
                                                     'shadow-xl': config.card_style === 'shadow',
                                                     'border-2 border-gray-200': config.card_style === 'bordered',
                                                     'shadow-sm': config.card_style === 'flat'
                                                 }">
                                                <!-- Image -->
                                                <div class="relative w-full aspect-square bg-gradient-to-br from-gray-100 to-gray-50 overflow-hidden">
                                                    @if($link->hasImage())
                                                    <img src="{{ $link->image_url }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                                                    @else
                                                    <div class="flex items-center justify-center h-full text-4xl">
                                                        {{ $link->platform_icon }}
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Content -->
                                                <div class="p-3">
                                                    <h3 class="font-bold text-xs mb-1 line-clamp-2" 
                                                        :style="`color: ${config.text_color || '#1F2937'}`">
                                                        {{ $link->display_name }}
                                                    </h3>
                                                    
                                                    <div class="flex items-center justify-between text-xs mb-2">
                                                        @if($link->price)
                                                        <p class="font-bold" :style="`color: ${config.primary_color}`">
                                                            {{ $link->formatted_price }}
                                                        </p>
                                                        @endif
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-medium">
                                                            {{ ucfirst($link->platform) }}
                                                        </span>
                                                    </div>
                                                    
                                                    <button class="w-full py-1.5 rounded-lg text-white font-semibold text-xs shadow"
                                                            :style="`background: ${config.primary_color}`">
                                                        Buy Now
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- CTA Section Preview -->
                                    <div class="px-6 py-8 text-center text-white relative overflow-hidden"
                                         x-show="config.cta_text"
                                         :style="`background: linear-gradient(-45deg, ${config.primary_color}, ${config.secondary_color || '#10B981'})`">
                                        <div class="relative z-10">
                                            <h2 class="text-2xl font-bold mb-3" x-text="config.cta_text"></h2>
                                            <p class="text-sm mb-4 opacity-90">Don't miss our best offers!</p>
                                                <div class="inline-block px-6 py-2 bg-white text-gray-900 font-bold rounded-full shadow-xl text-sm">
                                                Start Shopping
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Preview -->
                                    <div class="bg-gray-900 text-white px-6 py-6">
                                        <div class="grid grid-cols-2 gap-4 text-xs mb-4">
                                            <div>
                                                <h3 class="font-bold mb-1 text-sm" x-text="config.storefront_title || 'Store Name'"></h3>
                                                <p class="text-gray-400 text-[10px]" x-text="config.tagline || 'Your tagline'"></p>
                                            </div>
                                            
                                            <div x-show="config.footer_content">
                                                <h4 class="font-semibold mb-1 text-xs">Contact</h4>
                                                <div class="text-gray-400 text-[10px]" x-text="config.footer_content"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-t border-gray-800 pt-3 text-center text-gray-500 text-[10px]">
                                            <p>© 2025 <span x-text="config.storefront_title || 'Your Store'"></span>. All rights reserved.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-500 mt-3 text-center">
                                    💡 Preview will update automatically when you change settings
                                </p>
                            </div>
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
                    storefront_title: '{{ $config->storefront_title ?? '' }}',
                    tagline: '{{ $config->tagline ?? '' }}',
                    primary_color: '{{ $config->primary_color ?? '#3B82F6' }}',
                    secondary_color: '{{ $config->secondary_color ?? '#10B981' }}',
                    text_color: '{{ $config->text_color ?? '#1F2937' }}',
                    grid_columns: {{ $config->grid_columns ?? 3 }},
                    card_style: '{{ $config->card_style ?? 'shadow' }}',
                    header_text: parsedBlocks.header_text || '',
                    about_text: parsedBlocks.about_text || '',
                    cta_text: parsedBlocks.cta_text || '',
                    footer_content: '{{ $config->footer_content ?? '' }}',
                    is_published: {{ $config->is_published ? 'true' : 'false' }}
                },
                saving: false,

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
