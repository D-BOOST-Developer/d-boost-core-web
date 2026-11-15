<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#F1F0FF] leading-tight">
            Edit Marketplace Link
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-6">
            <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-6 shadow-lg">
                <form action="{{ route('storefront.links.update', $link) }}" method="POST" enctype="multipart/form-data" x-data="linkForm()">
                    @csrf
                    @method('PUT')

                    <!-- Link Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Link Type *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors" :class="linkType === 'product' ? 'border-[#A78BFA] bg-[#A78BFA]/10 text-[#F1F0FF]' : 'border-[#2A2A3A] bg-[#111118] text-[#A9A8C3]'">
                                <input type="radio" name="link_type" value="product" x-model="linkType" class="mr-3 text-[#A78BFA] focus:ring-[#A78BFA]" {{ old('link_type', $link->link_type) === 'product' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-medium">Specific Product</p>
                                    <p class="text-sm text-[#7E7D9C]">Link to specific product</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors" :class="linkType === 'marketplace_profile' ? 'border-[#A78BFA] bg-[#A78BFA]/10 text-[#F1F0FF]' : 'border-[#2A2A3A] bg-[#111118] text-[#A9A8C3]'">
                                <input type="radio" name="link_type" value="marketplace_profile" x-model="linkType" class="mr-3 text-[#A78BFA] focus:ring-[#A78BFA]" {{ old('link_type', $link->link_type) === 'marketplace_profile' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-medium">Marketplace Profile</p>
                                    <p class="text-sm text-[#7E7D9C]">Link to store/profile</p>
                                </div>
                            </label>
                        </div>
                        @error('link_type')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Link URL *</label>
                        <input type="url" name="url" value="{{ old('url', $link->url) }}" required
                               placeholder="https://shopee.co.id/product-name" 
                               class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C] @error('url') border-red-500 @enderror">
                        @error('url')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[#7E7D9C] text-sm mt-1">Platform will be detected automatically from URL</p>
                    </div>

                    <!-- Display Name -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Display Name *</label>
                        <input type="text" name="display_name" value="{{ old('display_name', $link->display_name) }}" required
                               placeholder="Product or store name" 
                               class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C] @error('display_name') border-red-500 @enderror">
                        @error('display_name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  placeholder="Brief description about product/store..."
                                  class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C] @error('description') border-red-500 @enderror">{{ old('description', $link->description) }}</textarea>
                        @error('description')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Price (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-[#7E7D9C]">Rp</span>
                            <input type="number" name="price" value="{{ old('price', $link->price) }}" min="0" step="1000"
                                   placeholder="0"
                                   class="w-full bg-[#111118] border border-[#2A2A3A] text-[#F1F0FF] rounded-lg pl-12 pr-4 py-2 focus:ring-2 focus:ring-[#A78BFA] placeholder-[#7E7D9C] @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#A9A8C3] mb-2">Product Image/Logo</label>
                        
                        @if($link->hasImage())
                            <div class="mb-4">
                                <p class="text-sm text-[#A9A8C3] mb-2">Current image:</p>
                                <img src="{{ $link->image_url }}" alt="{{ $link->display_name }}" class="w-32 h-32 object-cover rounded-lg border border-[#2A2A3A]">
                            </div>
                        @endif

                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-[#2A2A3A] bg-[#111118] rounded-xl hover:border-[#A78BFA] transition" 
                             x-data="imagePreview()">
                            <div class="space-y-1 text-center">
                                <div x-show="!imageUrl" class="mx-auto h-12 w-12 text-[#7E7D9C]">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div x-show="imageUrl" class="mx-auto max-w-xs">
                                    <img :src="imageUrl" class="rounded-lg">
                                </div>
                                <div class="flex text-sm text-[#A9A8C3]">
                                    <label class="relative cursor-pointer bg-transparent rounded-md font-medium text-[#A78BFA] hover:text-[#C4B5FD]">
                                        <span>Upload new image</span>
                                        <input type="file" name="image" class="sr-only" accept="image/*" @change="previewImage($event)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-[#7E7D9C]">PNG, JPG, WEBP up to 2MB</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $link->is_active) ? 'checked' : '' }} 
                                   class="rounded border-[#2A2A3A] bg-[#111118] text-[#A78BFA] focus:ring-[#A78BFA]">
                            <span class="ml-2 text-sm text-[#A9A8C3]">Activate this link</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-[#2A2A3A]">
                        <a href="{{ route('storefront.links.index') }}" 
                           class="px-4 py-2 border border-[#2A2A3A] rounded-lg text-[#A9A8C3] hover:bg-[#252535] hover:text-[#F1F0FF] transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#A78BFA] hover:bg-[#9333EA] text-[#0A0A0F] rounded-lg font-bold shadow-lg shadow-[#A78BFA]/20 transition-all">
                            Update Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function linkForm() {
            return {
                linkType: '{{ old('link_type', $link->link_type) }}'
            }
        }

        function imagePreview() {
            return {
                imageUrl: null,
                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageUrl = URL.createObjectURL(file);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
