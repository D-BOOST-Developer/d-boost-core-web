<section x-data="paymentForm()">
    <!-- Bank Information Card -->
    <div class="profile-card p-6 mb-6">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-[#F1F0FF] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                {{ __('Bank Information') }}
            </h2>
            <p class="mt-1 text-sm text-[#A9A8C3]">
                {{ __('Add your bank account details for payment purposes.') }}
            </p>
        </header>

        <form method="post" action="{{ route('profile.business.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <!-- Hidden fields to preserve existing business data -->
            <input type="hidden" name="business_name" value="{{ $businessProfile->business_name ?? '' }}">
            <input type="hidden" name="business_category" value="{{ $businessProfile->business_category ?? '' }}">
            <input type="hidden" name="owner_name" value="{{ $businessProfile->owner_name ?? '' }}">
            <input type="hidden" name="contact_email" value="{{ $businessProfile->contact_email ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bank_name" class="info-label block">{{ __('Bank Name') }}</label>
                    <select id="bank_name" 
                            name="bank_name" 
                            class="input-field w-full">
                        <option value="">Select Bank</option>
                        @foreach($banks as $code => $name)
                            <option value="{{ $code }}" {{ old('bank_name', $businessProfile->bank_name ?? '') === $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bank_name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bank_account_holder" class="info-label block">{{ __('Account Holder Name') }}</label>
                    <input type="text" 
                           id="bank_account_holder" 
                           name="bank_account_holder" 
                           value="{{ old('bank_account_holder', $businessProfile->bank_account_holder ?? '') }}" 
                           class="input-field w-full"
                           placeholder="Name as shown on bank account">
                    @error('bank_account_holder')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="bank_account_number" class="info-label block">{{ __('Account Number') }}</label>
                    <input type="text" 
                           id="bank_account_number" 
                           name="bank_account_number" 
                           value="{{ old('bank_account_number', $businessProfile->bank_account_number ?? '') }}" 
                           class="input-field w-full"
                           placeholder="Enter your bank account number">
                    @error('bank_account_number')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bank Info Preview -->
            @if($businessProfile && $businessProfile->bank_name)
            <div class="bg-[#111118] rounded-xl p-5 border border-[#2A2A3A]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#252535] border border-[#3A3A4E] rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-[#F1F0FF]">{{ $banks[$businessProfile->bank_name] ?? $businessProfile->bank_name }}</div>
                        <div class="text-sm text-[#A9A8C3]">{{ $businessProfile->bank_account_holder }}</div>
                        <div class="text-sm text-[#7E7D9C] font-mono">{{ $businessProfile->bank_account_number }}</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary">
                    {{ __('Save Bank Info') }}
                </button>
            </div>
        </form>
    </div>

    <!-- QRIS Barcode Card -->
    <div class="profile-card p-6">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-[#F1F0FF] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                {{ __('QRIS Payment') }}
            </h2>
            <p class="mt-1 text-sm text-[#A9A8C3]">
                {{ __('Upload your QRIS barcode for easy payment acceptance.') }}
            </p>
        </header>

        <div class="flex flex-col md:flex-row items-start gap-6">
            <!-- Current QRIS Preview -->
            <div class="flex-shrink-0">
                <div class="w-48 h-48 rounded-xl border border-[#2A2A3A] overflow-hidden bg-[#111118] flex items-center justify-center">
                    @if($businessProfile && $businessProfile->qris_image_url)
                        <img src="{{ $businessProfile->qris_image_url }}" alt="QRIS Barcode" class="w-full h-full object-contain p-2">
                    @else
                        <div class="text-center p-4">
                            <svg class="w-16 h-16 mx-auto text-[#7E7D9C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <p class="text-xs text-[#7E7D9C] mt-2">No QRIS uploaded</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Upload Form -->
            <form method="post" action="{{ route('profile.upload.qris') }}" enctype="multipart/form-data" class="flex-1" id="qrisUploadForm">
                @csrf
                <div class="upload-zone" 
                     @dragover.prevent="$el.classList.add('dragover')" 
                     @dragleave.prevent="$el.classList.remove('dragover')"
                     @drop.prevent="handleQrisDrop($event)"
                     @click="$refs.qrisInput.click()">
                    <input type="file" name="qris_barcode" x-ref="qrisInput" accept="image/*" class="hidden" @change="submitQrisForm()">
                    <svg class="w-10 h-10 mx-auto text-[#7E7D9C] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm text-[#A9A8C3] font-medium">Drop your QRIS barcode here or click to browse</p>
                    <p class="text-xs text-[#7E7D9C] mt-1">PNG, JPG, WEBP up to 2MB</p>
                </div>
                
                <div class="mt-4 bg-[#111118] border border-amber-500/30 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-[#A9A8C3]">
                            <p class="font-medium text-amber-300">QRIS Tips:</p>
                            <ul class="text-xs mt-1 space-y-1">
                                <li>• Screenshot or download your QRIS from your bank app</li>
                                <li>• Make sure the QR code is clear and readable</li>
                                <li>• QRIS can be scanned by any e-wallet or mobile banking app</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function paymentForm() {
            return {
                handleQrisDrop(event) {
                    event.target.classList.remove('dragover');
                    const file = event.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const input = this.$refs.qrisInput;
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        this.submitQrisForm();
                    }
                },
                
                submitQrisForm() {
                    document.getElementById('qrisUploadForm').submit();
                }
            }
        }
    </script>
</section>
