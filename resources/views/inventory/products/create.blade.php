<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Add New Product') }}
            </h2>
            <a href="{{ route('inventory.products.index') }}"
                class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Products</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <form method="POST" action="{{ route('inventory.products.store') }}" enctype="multipart/form-data"
                    x-data="{ imagePreview: null }">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Product Name -->
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Name
                                    *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('name') border-red-500 @enderror"
                                    placeholder="Enter product name">
                                @error('name')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label for="sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SKU
                                    *</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('sku') border-red-500 @enderror"
                                    placeholder="Enter product SKU">
                                @error('sku')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category
                                    *</label>
                                <select name="category" id="category" required
                                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('category') border-red-500 @enderror">
                                    <option value="">Select Category</option>
                                    <option value="Beverages" {{ old('category') == 'Beverages' ? 'selected' : '' }}>
                                        Beverages</option>
                                    <option value="Snacks" {{ old('category') == 'Snacks' ? 'selected' : '' }}>Snacks
                                    </option>
                                    <option value="Health" {{ old('category') == 'Health' ? 'selected' : '' }}>Health
                                    </option>
                                    <option value="Fashion" {{ old('category') == 'Fashion' ? 'selected' : '' }}>Fashion
                                    </option>
                                    <option value="Food" {{ old('category') == 'Food' ? 'selected' : '' }}>Food</option>
                                    <option value="Handicraft" {{ old('category') == 'Handicraft' ? 'selected' : '' }}>
                                        Handicraft</option>
                                    <option value="Service" {{ old('category') == 'Service' ? 'selected' : '' }}>Service
                                    </option>
                                    <option value="Vehicle" {{ old('category') == 'Vehicle' ? 'selected' : '' }}>Vehicle
                                    </option>
                                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('category')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <textarea name="description" id="description" rows="4"
                                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('description') border-red-500 @enderror"
                                    placeholder="Enter product description">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Product Image -->
                            <div>
                                <label for="image"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product
                                    Image</label>
                                <div
                                    class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center bg-gray-50 dark:bg-gray-700/30">
                                    <div x-show="!imagePreview">
                                        <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="text-gray-700 dark:text-gray-300 mb-2">Click to upload image</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                    <div x-show="imagePreview" class="relative">
                                        <img :src="imagePreview" class="max-h-48 mx-auto rounded-lg">
                                        <button type="button" @click="imagePreview = null; $refs.imageInput.value = ''"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="file" name="image" id="image" x-ref="imageInput" accept="image/*"
                                        class="hidden"
                                        @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => imagePreview = e.target.result; reader.readAsDataURL(file); }">
                                </div>
                                <label for="image"
                                    class="block w-full mt-2 bg-gray-600 hover:bg-gray-500 text-white text-center py-2 rounded-lg cursor-pointer transition-all duration-200">
                                    Choose Image
                                </label>
                                @error('image')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price & Cost -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selling
                                        Price *</label>
                                    <input type="number" name="price" id="price" value="{{ old('price') }}" required
                                        min="0" step="0.01"
                                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('price') border-red-500 @enderror"
                                        placeholder="0.00">
                                    @error('price')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cost"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cost
                                        Price *</label>
                                    <input type="number" name="cost" id="cost" value="{{ old('cost') }}" required
                                        min="0" step="0.01"
                                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('cost') border-red-500 @enderror"
                                        placeholder="0.00">
                                    @error('cost')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="stock_quantity"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock
                                        Quantity *</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity"
                                        value="{{ old('stock_quantity', 0) }}" required min="0"
                                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('stock_quantity') border-red-500 @enderror"
                                        placeholder="0">
                                    @error('stock_quantity')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="min_stock_level"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Min
                                        Stock Level *</label>
                                    <input type="number" name="min_stock_level" id="min_stock_level"
                                        value="{{ old('min_stock_level', 10) }}" required min="0"
                                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#006BFF] focus:border-transparent @error('min_stock_level') border-red-500 @enderror"
                                        placeholder="10">
                                    @error('min_stock_level')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                        class="rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-[#006BFF] focus:ring-[#006BFF] focus:ring-offset-0">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Product is active</span>
                                </label>
                            </div>

                            <!-- Storefront Publish -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_published_to_storefront" value="1" {{ old('is_published_to_storefront') ? 'checked' : '' }}
                                        class="rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-[#006BFF] focus:ring-[#006BFF] focus:ring-offset-0">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Tampilkan di Toko Web
                                        (Storefront)</span>
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">Aktifkan untuk menampilkan
                                    produk ini di katalog publik storefront Anda.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('inventory.products.index') }}"
                            class="bg-gray-600 hover:bg-gray-500 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-200">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>