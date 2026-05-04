// Welcome Tour for First-Time Users - Enhanced Version
class WelcomeTour {
    constructor() {
        this.currentStep = 0;
        this.steps = [];
        this.overlay = null;
        this.tooltip = null;
        this.isActive = false;
    }

    init(steps) {
        this.steps = steps;
        this.createOverlay();
        this.createTooltip();
    }

    createOverlay() {
        // Create main overlay WITHOUT blur - only darken background
        this.overlay = document.createElement('div');
        this.overlay.className = 'welcome-tour-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9998;
            display: none;
            transition: all 0.3s ease;
            pointer-events: none;
        `;
        document.body.appendChild(this.overlay);

        // Create spotlight cutout overlay
        this.spotlightOverlay = document.createElement('div');
        this.spotlightOverlay.className = 'welcome-tour-spotlight';
        this.spotlightOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9998;
            display: none;
            pointer-events: none;
            transition: box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        document.body.appendChild(this.spotlightOverlay);
    }

    createTooltip() {
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'welcome-tour-tooltip';
        this.tooltip.style.cssText = `
            position: fixed;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 25px 70px rgba(0, 107, 255, 0.25), 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            max-width: 450px;
            display: none;
            border: 2px solid rgba(0, 107, 255, 0.1);
            animation: slideIn 0.4s ease-out;
        `;
        document.body.appendChild(this.tooltip);
    }

    start() {
        if (this.steps.length === 0) return;

        this.isActive = true;
        this.currentStep = 0;
        this.overlay.style.display = 'block';
        this.spotlightOverlay.style.display = 'block';
        this.showStep(0);
    }

    showStep(index) {
        if (index >= this.steps.length) {
            this.end();
            return;
        }

        const step = this.steps[index];

        // Support both element (string selector) and elementFn (function that returns element)
        let element;
        if (step.elementFn) {
            element = step.elementFn();
        } else if (step.element) {
            element = document.querySelector(step.element);
        }

        if (!element) {
            console.warn(`Element not found for step ${index + 1}:`, step.element || 'dynamic selector');
            this.next();
            return;
        }

        // Highlight element
        this.highlightElement(element);

        // Position tooltip
        this.positionTooltip(element, step);

        // Update tooltip content
        this.updateTooltipContent(step, index);

        // Show tooltip with animation
        this.tooltip.style.display = 'block';
        setTimeout(() => {
            this.tooltip.style.opacity = '1';
            this.tooltip.style.transform = 'scale(1)';
        }, 10);
    }

    highlightElement(element) {
        // Remove previous highlights
        document.querySelectorAll('.tour-highlight').forEach(el => {
            el.classList.remove('tour-highlight');
            el.style.position = '';
            el.style.zIndex = '';
            el.style.boxShadow = '';
            el.style.borderRadius = '';
            el.style.transform = '';
            el.style.backgroundColor = '';
            el.style.filter = '';
            el.style.opacity = '';
        });

        // Add highlight to current element with animation
        element.classList.add('tour-highlight');
        const originalPosition = window.getComputedStyle(element).position;
        if (originalPosition === 'static') {
            element.style.position = 'relative';
        }
        element.style.zIndex = '10000'; // Higher z-index to ensure it's above spotlight
        element.style.boxShadow = '0 0 0 4px #006BFF, 0 0 0 8px rgba(0, 107, 255, 0.4), 0 0 40px rgba(0, 107, 255, 0.3)';
        element.style.borderRadius = '16px';
        element.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        element.style.transform = 'scale(1.02)';

        // CRITICAL: Ensure element is FULLY BRIGHT - no darkening
        const originalBg = window.getComputedStyle(element).backgroundColor;
        element.style.backgroundColor = originalBg || 'white';
        element.style.opacity = '1'; // Full opacity
        element.style.filter = 'brightness(1.1)'; // Slightly brighter to stand out
        element.style.backdropFilter = 'none';

        // Scroll element into view smoothly
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Create spotlight cutout effect with VERY DARK surrounding for maximum contrast
        setTimeout(() => {
            const rect = element.getBoundingClientRect();
            const padding = 20;

            // Create box-shadow that covers entire screen except the highlighted element
            // Using VERY dark shadow (0.95 opacity) for strong contrast
            const spotlightShadow = `0 0 0 9999px rgba(0, 0, 0, 0.95)`;

            // Position spotlight overlay to match element
            this.spotlightOverlay.style.top = `${rect.top - padding}px`;
            this.spotlightOverlay.style.left = `${rect.left - padding}px`;
            this.spotlightOverlay.style.width = `${rect.width + (padding * 2)}px`;
            this.spotlightOverlay.style.height = `${rect.height + (padding * 2)}px`;
            this.spotlightOverlay.style.boxShadow = spotlightShadow;
            this.spotlightOverlay.style.borderRadius = '20px';
            // Ensure spotlight overlay is below highlighted element
            this.spotlightOverlay.style.zIndex = '9999';
        }, 100);
    }

    positionTooltip(element, step) {
        const rect = element.getBoundingClientRect();
        const tooltipRect = this.tooltip.getBoundingClientRect();

        let top, left;
        const position = step.position || 'bottom';
        const offset = 24;

        switch (position) {
            case 'top':
                top = rect.top - tooltipRect.height - offset;
                left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                break;
            case 'bottom':
                top = rect.bottom + offset;
                left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                break;
            case 'left':
                top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                left = rect.left - tooltipRect.width - offset;
                break;
            case 'right':
                top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                left = rect.right + offset;
                break;
            case 'center':
                top = (window.innerHeight / 2) - (tooltipRect.height / 2);
                left = (window.innerWidth / 2) - (tooltipRect.width / 2);
                break;
            default:
                top = rect.bottom + offset;
                left = rect.left;
        }

        // Ensure tooltip stays within viewport
        const padding = 16;
        if (top < padding) top = padding;
        if (left < padding) left = padding;
        if (left + tooltipRect.width > window.innerWidth - padding) {
            left = window.innerWidth - tooltipRect.width - padding;
        }
        if (top + tooltipRect.height > window.innerHeight - padding) {
            top = window.innerHeight - tooltipRect.height - padding;
        }

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
    }

    updateTooltipContent(step, index) {
        const isLast = index === this.steps.length - 1;
        const progress = ((index + 1) / this.steps.length) * 100;

        this.tooltip.innerHTML = `
            <div class="tour-content">
                ${step.icon ? `
                    <div class="tour-icon mb-5">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-[#006BFF] to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/40 transform hover:scale-105 transition-transform">
                            ${step.icon}
                        </div>
                    </div>
                ` : ''}
                
                ${step.badge ? `
                    <div class="mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-[#006BFF]">
                            ${step.badge}
                        </span>
                    </div>
                ` : ''}
                
                <h3 class="text-2xl font-bold text-gray-900 mb-3 leading-tight">${step.title}</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">${step.description}</p>
                
                ${step.tips ? `
                    <div class="bg-blue-50 border-l-4 border-[#006BFF] p-4 mb-6 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-[#006BFF]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800 font-medium">${step.tips}</p>
                            </div>
                        </div>
                    </div>
                ` : ''}
                
                <!-- Progress Bar -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-600">Progress</span>
                        <span class="text-sm font-semibold text-[#006BFF]">${index + 1} / ${this.steps.length}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#006BFF] to-blue-500 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: ${progress}%"></div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-2">
                    <button onclick="welcomeTour.skip()" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                        Lewati Tour
                    </button>
                    <div class="flex space-x-2">
                        ${index > 0 ? `
                            <button onclick="welcomeTour.previous()" class="px-5 py-2.5 text-gray-700 hover:text-gray-900 font-semibold rounded-xl transition-all hover:bg-gray-100 border-2 border-gray-200">
                                ← Kembali
                            </button>
                        ` : ''}
                        <button onclick="welcomeTour.${isLast ? 'end' : 'next'}()" class="px-6 py-2.5 bg-gradient-to-r from-[#006BFF] to-blue-600 hover:from-[#0056cc] hover:to-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:scale-105">
                            ${isLast ? '🎉 Selesai' : 'Lanjut →'}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    next() {
        this.currentStep++;
        this.showStep(this.currentStep);
    }

    previous() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.showStep(this.currentStep);
        }
    }

    end() {
        this.isActive = false;

        // Fade out animation
        this.overlay.style.opacity = '0';
        this.spotlightOverlay.style.opacity = '0';
        this.tooltip.style.opacity = '0';
        this.tooltip.style.transform = 'scale(0.95)';

        setTimeout(() => {
            this.overlay.style.display = 'none';
            this.spotlightOverlay.style.display = 'none';
            this.tooltip.style.display = 'none';
        }, 300);

        // Remove all highlights
        document.querySelectorAll('.tour-highlight').forEach(el => {
            el.classList.remove('tour-highlight');
            el.style.position = '';
            el.style.zIndex = '';
            el.style.boxShadow = '';
            el.style.borderRadius = '';
            el.style.transform = '';
            el.style.backgroundColor = '';
        });

        // Mark tour as completed
        localStorage.setItem('welcome_tour_completed', 'true');

        // Show completion message
        this.showCompletionMessage();
    }

    showCompletionMessage() {
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 107, 255, 0.3);
            z-index: 10000;
            text-align: center;
            max-width: 400px;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        `;

        message.innerHTML = `
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/40">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Tour Selesai! 🎉</h3>
            <p class="text-gray-600 mb-6">Halaman akan di-refresh untuk pengalaman terbaik!</p>
            <button id="tour-complete-btn" class="px-8 py-3 bg-gradient-to-r from-[#006BFF] to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:scale-105 transition-all">
                Mulai Sekarang
            </button>
        `;

        document.body.appendChild(message);

        // Add click handler for button
        const btn = message.querySelector('#tour-complete-btn');
        btn.addEventListener('click', () => {
            message.remove();
            // Refresh page to clean state
            window.location.href = window.location.pathname;
        });

        setTimeout(() => {
            message.style.opacity = '1';
            message.style.transform = 'translate(-50%, -50%) scale(1)';
        }, 100);

        // Auto refresh after 4 seconds
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translate(-50%, -50%) scale(0.8)';
            setTimeout(() => {
                message.remove();
                // Refresh page to clean state
                window.location.href = window.location.pathname;
            }, 400);
        }, 4000);
    }

    skip() {
        if (confirm('Apakah Anda yakin ingin melewati tour? Anda bisa mengaksesnya lagi nanti.')) {
            this.end();
        }
    }
}

// Initialize welcome tour
const welcomeTour = new WelcomeTour();

// Check if user just completed onboarding (session flag)
document.addEventListener('DOMContentLoaded', function () {
    console.log('=== WELCOME TOUR DEBUG ===');

    // Check session flag set by onboarding completion
    const showTourFromSession = window.showWelcomeTour === true;
    let tourCompleted = localStorage.getItem('welcome_tour_completed') === 'true';

    console.log('Show tour from session:', showTourFromSession);
    console.log('Tour completed (localStorage):', tourCompleted);

    // If user just completed onboarding, reset the completed flag
    if (showTourFromSession && tourCompleted) {
        console.log('🔄 User just completed onboarding - resetting tour completed flag');
        localStorage.removeItem('welcome_tour_completed');
        tourCompleted = false;
    }

    console.log('Should start tour:', showTourFromSession && !tourCompleted);

    if (showTourFromSession && !tourCompleted) {
        console.log('🚀 Tour will start in 2.5 seconds...');
        // Wait longer for page to fully load and render
        setTimeout(() => {
            console.log('▶️ Starting welcome tour now...');
            startWelcomeTour();
        }, 2500);
    } else {
        if (!showTourFromSession) {
            console.log('❌ Tour not started: Not from onboarding (session flag not set)');
        } else if (tourCompleted) {
            console.log('✅ Tour not started: Already completed');
        }
    }
});

function startWelcomeTour() {
    console.log('=== START WELCOME TOUR FUNCTION ===');
    console.log('Total steps defined:', 15);

    // Helper function to find module card by text content
    const findModuleCard = (moduleName) => {
        const moduleCards = document.querySelectorAll('.module-card-container > div');
        for (let card of moduleCards) {
            const text = card.textContent || card.innerText;
            if (text.toLowerCase().includes(moduleName.toLowerCase())) {
                return card;
            }
        }
        return null;
    };

    // Create dynamic selector function
    const getModuleSelector = (moduleName) => {
        return () => findModuleCard(moduleName);
    };

    const steps = [
        {
            element: 'header.dashboard-header',
            title: '🎉 Selamat Datang di DBOOST!',
            description: 'Selamat! Anda telah berhasil menyelesaikan onboarding. Ini adalah dashboard utama Anda - pusat kendali untuk mengelola seluruh aspek bisnis UMKM Anda dengan lebih efisien dan terorganisir.',
            position: 'bottom',
            badge: 'Langkah 1 dari 15',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
            tips: 'Dashboard ini telah disesuaikan dengan profil dan kategori bisnis Anda berdasarkan hasil onboarding.'
        },
        {
            element: '.sidebar-container',
            title: '📋 Sidebar Navigasi',
            description: 'Sidebar ini adalah menu navigasi utama Anda. Di sini Anda dapat mengakses semua modul dan fitur yang tersedia. Menu dikelompokkan berdasarkan modul untuk memudahkan navigasi.',
            position: 'right',
            badge: 'Navigasi',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>',
            tips: 'Sidebar dapat di-expand atau collapse untuk menghemat ruang layar. Coba klik tombol toggle di bagian bawah sidebar!'
        },
        {
            element: '.sidebar-container button[class*="toggle"]',
            title: '↔️ Toggle Sidebar',
            description: 'Gunakan tombol ini untuk memperlebar atau mempersempit sidebar. Saat dipersempit, Anda tetap dapat melihat ikon menu, namun label teks akan disembunyikan untuk menghemat ruang.',
            position: 'right',
            badge: 'Fitur Praktis',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12m-12 5h12M3 7h.01M3 12h.01M3 17h.01"></path></svg>',
            tips: 'Sidebar yang dipersempit sangat berguna saat Anda bekerja dengan layar kecil atau ingin fokus pada konten utama.'
        },
        {
            element: '.module-card-container',
            title: '🎯 Modul Bisnis Anda',
            description: 'Ini adalah modul-modul yang telah disesuaikan dengan kebutuhan bisnis Anda. Setiap modul dirancang khusus untuk membantu aspek tertentu dari operasional bisnis UMKM Anda.',
            position: 'top',
            badge: 'Modul Utama',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
            tips: 'Klik pada modul untuk melihat detail lengkap dan mengakses fitur-fiturnya. Modul yang aktif ditandai dengan warna yang lebih cerah.'
        },
        {
            elementFn: getModuleSelector('Dashboard'),
            title: '📊 Dashboard Module',
            description: 'Modul Dashboard memberikan ringkasan lengkap bisnis Anda: metrik penting, grafik penjualan, statistik real-time, dan aktivitas terbaru. Ini adalah pusat informasi untuk memantau performa bisnis Anda sekilas.',
            position: 'bottom',
            badge: 'Modul 1',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
            tips: 'Dashboard menampilkan data real-time yang diupdate otomatis. Refresh halaman untuk melihat data terbaru.'
        },
        {
            elementFn: getModuleSelector('Inventory'),
            title: '📦 Inventory Module',
            description: 'Kelola stok produk Anda dengan mudah! Modul Inventory membantu Anda melacak persediaan barang, mengelola produk, memonitor stok yang menipis, dan mengatur reorder point untuk memastikan stok selalu tersedia.',
            position: 'bottom',
            badge: 'Modul 2',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
            tips: 'Atur alert untuk stok minimum agar Anda mendapat notifikasi saat produk hampir habis!'
        },
        {
            elementFn: getModuleSelector('Finance'),
            title: '💰 Finance Module',
            description: 'Pantau kesehatan keuangan bisnis Anda! Modul Finance mencatat semua transaksi pemasukan dan pengeluaran, membuat laporan laba-rugi, analisis cash flow, dan memberikan insight keuangan yang mendalam.',
            position: 'bottom',
            badge: 'Modul 3',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            tips: 'Gunakan fitur kategorisasi untuk mengorganisir transaksi dan membuat laporan keuangan yang lebih detail.'
        },
        {
            elementFn: getModuleSelector('Digital Storefront'),
            title: '🛒 Digital Storefront',
            description: 'Bawa bisnis Anda ke dunia online! Modul Digital Storefront membantu Anda membuat toko online, mengelola katalog produk, menerima pesanan, dan mengelola pengiriman - semua dalam satu platform.',
            position: 'bottom',
            badge: 'Modul 4',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>',
            tips: 'Toko online Anda dapat diakses 24/7 oleh pelanggan, meningkatkan peluang penjualan Anda!'
        },
        {
            elementFn: getModuleSelector('Customer'),
            title: '👥 Customer Module',
            description: 'Kelola hubungan dengan pelanggan Anda! Modul Customer menyimpan database pelanggan, riwayat transaksi, preferensi pembelian, program loyalitas, dan membantu Anda memberikan layanan yang lebih personal.',
            position: 'bottom',
            badge: 'Modul 5',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
            tips: 'Pelanggan yang puas adalah aset terbesar! Gunakan data pelanggan untuk memberikan pengalaman yang lebih baik.'
        },
        {
            element: '[data-deby-trigger]',
            title: '🤖 Kenalan dengan DEBY AI!',
            description: 'Ini adalah DEBY - Digital Expert for Business, Your AI assistant! DEBY adalah asisten bisnis pintar yang siap membantu Anda 24/7. Klik tombol ini kapan saja untuk berbicara dengan DEBY.',
            position: 'left',
            badge: 'AI Assistant',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
            tips: 'DEBY selalu tersedia di pojok kanan bawah layar Anda. Tombol ini akan mengikuti saat Anda scroll!'
        },
        {
            element: '[data-deby-trigger]',
            title: '💡 Kemampuan DEBY AI',
            description: 'DEBY dapat membantu Anda dengan: ✨ Analisis data bisnis real-time, 📈 Rekomendasi strategi pemasaran, 💰 Tips pengelolaan keuangan, 📊 Prediksi tren penjualan, 🎯 Insight pelanggan, dan masih banyak lagi!',
            position: 'left',
            badge: 'Fitur AI',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
            tips: 'DEBY belajar dari data bisnis Anda untuk memberikan saran yang semakin personal dan akurat seiring waktu.'
        },
        {
            element: '[data-deby-trigger]',
            title: '💬 Contoh Pertanyaan untuk DEBY',
            description: 'Coba tanyakan ke DEBY: "Bagaimana performa penjualan bulan ini?", "Produk apa yang paling laku?", "Berapa profit saya minggu ini?", "Kapan waktu terbaik untuk restock?", atau "Berikan tips meningkatkan penjualan"',
            position: 'left',
            badge: 'Mulai Chat',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
            tips: 'Semakin spesifik pertanyaan Anda, semakin detail dan berguna jawaban dari DEBY!'
        },
        {
            element: '.sidebar-container .relative:has(button[class*="user"])',
            title: '👤 Profil & Pengaturan',
            description: 'Klik di sini untuk mengakses profil Anda, mengubah pengaturan akun, melihat dan mengatur preferensi modul, mengelola notifikasi, dan logout dengan aman.',
            position: 'right',
            badge: 'Akun Anda',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
            tips: 'Anda dapat mengubah preferensi modul kapan saja melalui menu pengaturan untuk menyesuaikan dashboard dengan kebutuhan bisnis Anda.'
        },
        {
            element: 'header.dashboard-header',
            title: '💡 Tips Menggunakan DBOOST',
            description: 'Beberapa tips: 1) Update data secara rutin untuk insight yang akurat, 2) Manfaatkan DEBY AI untuk analisis cepat, 3) Cek dashboard setiap hari untuk monitor bisnis, 4) Gunakan fitur export untuk backup data, 5) Jelajahi setiap modul untuk temukan fitur tersembunyi!',
            position: 'bottom',
            badge: 'Best Practices',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
            tips: 'Bookmark halaman ini untuk akses cepat ke dashboard Anda!'
        },
        {
            element: 'header.dashboard-header',
            title: '🚀 Siap Memulai!',
            description: 'Selamat! Anda sekarang sudah familiar dengan semua fitur utama DBOOST. Mulai eksplorasi dashboard, kelola bisnis Anda dengan lebih efisien, dan jangan ragu untuk bertanya ke DEBY AI kapan saja. Sukses untuk bisnis Anda! 🎉',
            position: 'center',
            badge: 'Selesai!',
            icon: '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>',
            tips: 'Anda bisa mengakses tour ini lagi kapan saja melalui menu bantuan atau dengan menambahkan ?first_login=1 di URL.'
        }
    ];

    console.log('Steps array created with', steps.length, 'steps');

    // Check if elements exist before starting tour
    const checkElements = () => {
        console.log('Checking if elements exist...');
        const missingElements = [];
        steps.forEach((step, index) => {
            const element = document.querySelector(step.element);
            if (!element) {
                missingElements.push({ step: index + 1, selector: step.element });
            }
        });

        console.log('Missing elements:', missingElements.length);
        if (missingElements.length > 0) {
            console.warn('Welcome Tour: Some elements not found:', missingElements);
            // Filter out steps with missing elements
            const validSteps = steps.filter(step => document.querySelector(step.element));
            console.log('Valid steps found:', validSteps.length);
            if (validSteps.length > 0) {
                console.log('Initializing tour with', validSteps.length, 'valid steps');
                welcomeTour.init(validSteps);
                welcomeTour.start();
            } else {
                console.error('Welcome Tour: No valid elements found. Tour cancelled.');
            }
        } else {
            console.log('All elements found! Starting tour with all', steps.length, 'steps');
            welcomeTour.init(steps);
            welcomeTour.start();
        }
    };

    // Retry mechanism if elements not ready
    let retryCount = 0;
    const maxRetries = 3;

    const attemptStart = () => {
        console.log('Attempt', retryCount + 1, 'to find elements...');
        const firstElement = document.querySelector('header.dashboard-header');
        console.log('First element (header.dashboard-header) found:', !!firstElement);

        if (firstElement || retryCount >= maxRetries) {
            if (!firstElement && retryCount >= maxRetries) {
                console.error('Max retries reached. First element still not found.');
            }
            checkElements();
        } else {
            retryCount++;
            console.log(`Welcome Tour: Retrying... (${retryCount}/${maxRetries})`);
            setTimeout(attemptStart, 1000);
        }
    };

    attemptStart();
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .tour-highlight {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.95;
        }
    }
`;
document.head.appendChild(style);
