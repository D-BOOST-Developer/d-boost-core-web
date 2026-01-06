<section x-data="businessProfileForm()">
    <!-- Logo Upload Form (Separate from main form) -->
    <form method="post" action="{{ route('profile.upload.logo') }}" enctype="multipart/form-data" id="logoUploadForm" class="hidden">
        @csrf
        <input type="file" name="business_logo" id="logoFileInput" accept="image/*">
    </form>

    <!-- Business Info Card -->
    <div class="profile-card p-6 mb-6">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#006BFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ __('Business Information') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Update your business details, logo, and location.') }}
            </p>
        </header>

        <form method="post" action="{{ route('profile.business.update') }}" class="space-y-6" id="businessUpdateForm">
            @csrf
            @method('patch')

            <!-- Business Logo Upload -->
            <div>
                <label class="info-label block mb-3">{{ __('Business Logo') }}</label>
                <div class="flex items-start gap-6">
                    <!-- Current Logo Preview -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-xl border-2 border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                            @if($businessProfile && $businessProfile->business_logo_url)
                                <img src="{{ $businessProfile->business_logo_url }}" alt="Business Logo" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Upload Zone (triggers the hidden form) -->
                    <div class="flex-1">
                        <div class="upload-zone" 
                             @dragover.prevent="$el.classList.add('dragover')" 
                             @dragleave.prevent="$el.classList.remove('dragover')"
                             @drop.prevent="handleLogoDrop($event)"
                             @click="triggerLogoUpload()">
                            <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-gray-600 font-medium">Drop your logo here or click to browse</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP up to 2MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="business_name" class="info-label block">{{ __('Business Name') }}</label>
                    <input type="text" 
                           id="business_name" 
                           name="business_name" 
                           value="{{ old('business_name', $businessProfile->business_name ?? '') }}" 
                           required
                           class="input-field w-full bg-white text-gray-900">
                    @error('business_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="business_category" class="info-label block">{{ __('Business Category') }}</label>
                    <input type="text" 
                           id="business_category" 
                           name="business_category" 
                           value="{{ old('business_category', $businessProfile->business_category ?? '') }}" 
                           required
                           class="input-field w-full bg-white text-gray-900"
                           placeholder="e.g., Food & Beverage, Fashion, Technology">
                    @error('business_category')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="owner_name" class="info-label block">{{ __('Owner Name') }}</label>
                    <input type="text" 
                           id="owner_name" 
                           name="owner_name" 
                           value="{{ old('owner_name', $businessProfile->owner_name ?? '') }}" 
                           required
                           class="input-field w-full bg-white text-gray-900">
                    @error('owner_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_email" class="info-label block">{{ __('Business Email') }}</label>
                    <input type="email" 
                           id="contact_email" 
                           name="contact_email" 
                           value="{{ old('contact_email', $businessProfile->contact_email ?? '') }}" 
                           required
                           class="input-field w-full bg-white text-gray-900">
                    @error('contact_email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_phone" class="info-label block">{{ __('Business Phone') }}</label>
                    <input type="text" 
                           id="contact_phone" 
                           name="contact_phone" 
                           value="{{ old('contact_phone', $businessProfile->contact_phone ?? '') }}" 
                           class="input-field w-full bg-white text-gray-900"
                           placeholder="+62 812 3456 7890">
                    @error('contact_phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- UMKM Category (Read Only) -->
                <div>
                    <label class="info-label block">{{ __('UMKM Category') }}</label>
                    <div class="input-field w-full bg-gray-50 text-gray-700 flex items-center gap-2">
                        @if($businessProfile && $businessProfile->umkmCategory)
                            <span class="badge badge-primary">{{ $businessProfile->umkmCategory->code }}</span>
                            <span>{{ $businessProfile->umkmCategory->name }}</span>
                        @else
                            <span class="text-gray-400">Not categorized</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Business Address -->
            <div>
                <label for="business_address" class="info-label block">{{ __('Business Address') }}</label>
                <textarea id="business_address" 
                          name="business_address" 
                          rows="3"
                          class="input-field w-full bg-white text-gray-900"
                          placeholder="Enter your complete business address">{{ old('business_address', $businessProfile->business_address ?? '') }}</textarea>
                @error('business_address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Google Maps Link -->
            <div>
                <label for="google_maps_link" class="info-label block">{{ __('Google Maps Link') }}</label>
                <input type="text" 
                       id="google_maps_link" 
                       name="google_maps_link" 
                       value="{{ old('google_maps_link', $businessProfile->google_maps_link ?? '') }}" 
                       x-model="mapsLink"
                       @change="handleManualInput()"
                       @paste="setTimeout(() => handleManualInput(), 100)"
                       class="input-field w-full bg-white text-gray-900"
                       placeholder="https://maps.google.com/?q=-6.2088,106.8456 atau klik pada map di bawah">
                <p class="text-xs text-gray-400 mt-1">
                    💡 <strong>Tip:</strong> Paste link Google Maps atau <strong>klik pada map</strong> untuk memilih lokasi
                </p>
                @error('google_maps_link')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- Interactive Maps Preview -->
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">📍 Business Location</span>
                        <span x-show="!mapsLink || mapsLink.trim() === ''" class="text-xs text-gray-400">Default: Jakarta, Indonesia</span>
                        <span x-show="mapsLink && mapsLink.trim() !== ''" class="text-xs text-green-600 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span x-text="getCoordinatesDisplay()"></span>
                        </span>
                    </div>
                    <div class="maps-preview rounded-xl overflow-hidden border-2 border-gray-200 shadow-sm hover:border-blue-300 transition-colors" style="min-height: 400px;">
                        <div id="interactive-map" style="width: 100%; height: 400px;">
                            <!-- Loading indicator -->
                            <div class="map-loading" style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); flex-direction: column; gap: 16px;">
                                <div style="width: 48px; height: 48px; border: 4px solid #BFDBFE; border-top-color: #006BFF; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <p style="color: #006BFF; font-weight: 500; font-size: 14px;">Loading interactive map...</p>
                            </div>
                            <style>
                                @keyframes spin {
                                    to { transform: rotate(360deg); }
                                }
                            </style>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Klik pada map untuk memilih lokasi, atau drag marker merah untuk menyesuaikan posisi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary">
                    {{ __('Save Business Info') }}
                </button>

                @if (session('status') === 'business-profile-updated')
                    <p x-data="{ show: true }"
                       x-show="show"
                       x-transition
                       x-init="setTimeout(() => show = false, 3000)"
                       class="text-sm text-green-600 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('Saved successfully!') }}
                    </p>
                @endif
            </div>
        </form>
    </div>

    <!-- Business Stats Card -->
    @if($businessProfile)
    <div class="profile-card p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Business Metrics</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <div class="info-label text-blue-600">Business Age</div>
                <div class="text-2xl font-bold text-blue-700">{{ round($businessProfile->business_age_month / 12, 1) }}</div>
                <div class="text-xs text-blue-600">Years</div>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <div class="info-label text-green-600">Revenue Range</div>
                <div class="text-lg font-bold text-green-700">{{ $businessProfile->monthly_revenue_range }}</div>
                <div class="text-xs text-green-600">Monthly</div>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 text-center">
                <div class="info-label text-purple-600">Category</div>
                <div class="text-lg font-bold text-purple-700">{{ $businessProfile->umkmCategory->code ?? 'N/A' }}</div>
                <div class="text-xs text-purple-600">UMKM Type</div>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 text-center">
                <div class="info-label text-amber-600">Status</div>
                <div class="text-lg font-bold text-amber-700">{{ $businessProfile->onboarding_completed ? 'Complete' : 'Pending' }}</div>
                <div class="text-xs text-amber-600">Onboarding</div>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Global variables for Google Maps
        let map;
        let marker;
        
        function businessProfileForm() {
            return {
                mapsLink: '{{ $businessProfile->google_maps_link ?? '' }}',
                
                // Default Jakarta coordinates
                defaultLat: -6.2088,
                defaultLng: 106.8456,
                
                // Current coordinates
                currentLat: null,
                currentLng: null,
                
                // Extract coordinates from various Google Maps URL formats
                extractCoordinates(url) {
                    if (!url || url.trim() === '') {
                        return null;
                    }
                    
                    const trimmedUrl = url.trim();
                    console.log('Extracting coordinates from:', trimmedUrl);
                    
                    // Method 1: @lat,lng format
                    let match = trimmedUrl.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
                    if (match) {
                        console.log('Found via @ pattern:', match[1], match[2]);
                        return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
                    }
                    
                    // Method 2: ?q=lat,lng format
                    match = trimmedUrl.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
                    if (match) {
                        console.log('Found via q= pattern:', match[1], match[2]);
                        return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
                    }
                    
                    // Method 3: ?ll=lat,lng format
                    match = trimmedUrl.match(/[?&]ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
                    if (match) {
                        console.log('Found via ll= pattern:', match[1], match[2]);
                        return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
                    }
                    
                    console.log('No coordinates found in URL');
                    return null;
                },
                
                // Generate standard format URL from coordinates
                generateMapsUrl(lat, lng) {
                    return `https://maps.google.com/?q=${lat},${lng}`;
                },
                
                // Get coordinates display for UI
                getCoordinatesDisplay() {
                    if (this.currentLat && this.currentLng) {
                        return `${this.currentLat.toFixed(6)}, ${this.currentLng.toFixed(6)}`;
                    }
                    return 'Custom location set';
                },
                
                // Handle manual input from user
                handleManualInput() {
                    console.log('Manual input changed:', this.mapsLink);
                    const coords = this.extractCoordinates(this.mapsLink);
                    
                    if (coords) {
                        this.currentLat = coords.lat;
                        this.currentLng = coords.lng;
                        this.updateMapPosition(coords.lat, coords.lng);
                    } else if (!this.mapsLink || this.mapsLink.trim() === '') {
                        // Reset to Jakarta if input is cleared
                        this.currentLat = null;
                        this.currentLng = null;
                        this.updateMapPosition(this.defaultLat, this.defaultLng);
                    }
                },
                
                // Update map position and marker
                updateMapPosition(lat, lng) {
                    if (!map) return;
                    
                    const position = { lat: lat, lng: lng };
                    map.setCenter(position);
                    map.setZoom(15);
                    
                    if (marker) {
                        marker.setPosition(position);
                    } else {
                        this.createMarker(position);
                    }
                },
                
                // Create draggable marker
                createMarker(position) {
                    marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        draggable: true,
                        title: 'Drag to adjust location',
                        animation: google.maps.Animation.DROP
                    });
                    
                    // Update input when marker is dragged
                    marker.addListener('dragend', (event) => {
                        const lat = event.latLng.lat();
                        const lng = event.latLng.lng();
                        this.updateInputFromMap(lat, lng);
                    });
                },
                
                // Update input field from map interaction
                updateInputFromMap(lat, lng) {
                    this.currentLat = lat;
                    this.currentLng = lng;
                    this.mapsLink = this.generateMapsUrl(lat, lng);
                    console.log('Updated input from map:', this.mapsLink);
                },
                
                // Initialize Google Maps
                initMap() {
                    // Determine initial position
                    let initialLat = this.defaultLat;
                    let initialLng = this.defaultLng;
                    
                    const coords = this.extractCoordinates(this.mapsLink);
                    if (coords) {
                        initialLat = coords.lat;
                        initialLng = coords.lng;
                        this.currentLat = initialLat;
                        this.currentLng = initialLng;
                    }
                    
                    // Clear loading indicator
                    const mapContainer = document.getElementById('interactive-map');
                    if (mapContainer) {
                        mapContainer.innerHTML = '';
                    }
                    
                    // Create map
                    map = new google.maps.Map(document.getElementById('interactive-map'), {
                        center: { lat: initialLat, lng: initialLng },
                        zoom: coords ? 15 : 11,
                        mapTypeControl: true,
                        streetViewControl: true,
                        fullscreenControl: true,
                        zoomControl: true
                    });
                    
                    // Add marker if we have coordinates
                    if (coords) {
                        this.createMarker({ lat: initialLat, lng: initialLng });
                    }
                    
                    // Add click listener to map
                    map.addListener('click', (event) => {
                        const lat = event.latLng.lat();
                        const lng = event.latLng.lng();
                        
                        if (marker) {
                            marker.setPosition(event.latLng);
                        } else {
                            this.createMarker(event.latLng);
                        }
                        
                        this.updateInputFromMap(lat, lng);
                    });
                    
                    console.log('Interactive map initialized');
                },
                
                triggerLogoUpload() {
                    document.getElementById('logoFileInput').click();
                },
                
                handleLogoDrop(event) {
                    event.target.classList.remove('dragover');
                    const file = event.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const input = document.getElementById('logoFileInput');
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        this.submitLogoForm();
                    }
                },
                
                submitLogoForm() {
                    document.getElementById('logoUploadForm').submit();
                },
                
                init() {
                    // Listen for file input changes
                    document.getElementById('logoFileInput').addEventListener('change', () => {
                        this.submitLogoForm();
                    });
                    
                    // Wait for Google Maps API to load
                    const tryInitMap = (retryCount = 0, maxRetries = 5) => {
                        if (typeof google !== 'undefined' && google.maps) {
                            console.log('Google Maps API ready, initializing map...');
                            try {
                                this.initMap();
                                console.log('Map initialized successfully');
                            } catch (error) {
                                console.error('Error initializing map:', error);
                                // Show error message in map container
                                const mapContainer = document.getElementById('interactive-map');
                                if (mapContainer) {
                                    mapContainer.innerHTML = `
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f9fafb; color: #6b7280; flex-direction: column; gap: 12px;">
                                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p style="font-size: 14px;">Failed to load map. Please refresh the page.</p>
                                        </div>
                                    `;
                                }
                            }
                        } else if (retryCount < maxRetries) {
                            // Retry with exponential backoff
                            const delay = Math.min(1000 * Math.pow(2, retryCount), 5000);
                            console.log(`Google Maps API not ready yet, retrying in ${delay}ms... (attempt ${retryCount + 1}/${maxRetries})`);
                            setTimeout(() => tryInitMap(retryCount + 1, maxRetries), delay);
                        } else {
                            console.error('Google Maps API failed to load after maximum retries');
                            // Show error message
                            const mapContainer = document.getElementById('interactive-map');
                            if (mapContainer) {
                                mapContainer.innerHTML = `
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #fef2f2; color: #991b1b; flex-direction: column; gap: 12px; padding: 20px; text-align: center;">
                                        <svg style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <div>
                                            <p style="font-weight: 600; margin-bottom: 4px;">Unable to load Google Maps</p>
                                            <p style="font-size: 14px;">Please check your internet connection and refresh the page.</p>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    };
                    
                    // Listen for custom event from Google Maps callback
                    window.addEventListener('google-maps-loaded', () => {
                        console.log('Received google-maps-loaded event');
                        tryInitMap();
                    });
                    
                    // Also try immediately in case API is already loaded
                    tryInitMap();
                }
            }
        }
    </script>
</section>
