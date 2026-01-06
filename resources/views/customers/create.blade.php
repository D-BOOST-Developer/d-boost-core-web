<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Add New Customer') }}
            </h2>
            <a href="{{ route('customers.index') }}" 
               class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Customers</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Customer Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Full Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="Enter customer full name">
                                @error('name')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('email') border-red-500 @enderror"
                                       placeholder="Enter email address">
                                @error('email')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-400 mb-2">Phone Number</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                       class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('phone') border-red-500 @enderror"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gender & Age -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-400 mb-2">Gender</label>
                                    <select name="gender" id="gender"
                                            class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('gender') border-red-500 @enderror">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="age" class="block text-sm font-medium text-gray-400 mb-2">Age</label>
                                    <input type="number" name="age" id="age" value="{{ old('age') }}" min="1" max="120"
                                           class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('age') border-red-500 @enderror"
                                           placeholder="Age">
                                    @error('age')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-400 mb-2">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                                       class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('date_of_birth') border-red-500 @enderror">
                                @error('date_of_birth')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-400 mb-2">City</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}"
                                       class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('city') border-red-500 @enderror"
                                       placeholder="Enter city">
                                @error('city')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-400 mb-2">Full Address</label>
                                <textarea name="address" id="address" rows="4"
                                          class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#00FF9C] focus:border-transparent @error('address') border-red-500 @enderror"
                                          placeholder="Enter full address">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="rounded bg-gray-700 border-gray-600 text-[#00FF9C] focus:ring-[#00FF9C] focus:ring-offset-gray-800">
                                    <span class="ml-2 text-gray-300">Customer is active</span>
                                </label>
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-blue-400 font-medium text-sm mb-1">Customer Information</h4>
                                        <p class="text-blue-300 text-xs">
                                            Customer data will be used for analytics and insights. 
                                            Purchase history and statistics will be automatically calculated based on sales transactions.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-700">
                        <a href="{{ route('customers.index') }}" 
                           class="bg-gray-600 hover:bg-gray-500 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-[#00FF9C] hover:bg-[#00FF9C]/80 text-gray-900 font-semibold py-2 px-6 rounded-lg transition-all duration-200">
                            Create Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
