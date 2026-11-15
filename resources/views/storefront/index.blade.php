<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#F1F0FF] leading-tight">
                Digital Storefront
            </h2>
            @if($config && $config->is_published)
            <a href="{{ $config->public_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-500 font-medium transition shadow-lg shadow-emerald-950/40">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Lihat Storefront Publik
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-[#1A1A24] rounded-xl shadow-lg border border-[#2A2A3A] p-5 hover:border-[#A78BFA]/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#A9A8C3] mb-1">Total Links</p>
                            <p class="text-3xl font-bold text-[#F1F0FF]">{{ $linksCount }}</p>
                        </div>
                        <div class="w-12 h-12 bg-[#252535] border border-[#3A3A4E] rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A24] rounded-xl shadow-lg border border-[#2A2A3A] p-5 hover:border-emerald-500/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#A9A8C3] mb-1">Active</p>
                            <p class="text-3xl font-bold text-emerald-400">{{ $activeLinksCount }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-950/40 border border-emerald-800/40 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A24] rounded-xl shadow-lg border border-[#2A2A3A] p-5 hover:border-purple-500/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#A9A8C3] mb-1">Total Clicks</p>
                            <p class="text-3xl font-bold text-purple-400">{{ $totalClicks }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-950/40 border border-purple-800/40 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A24] rounded-xl shadow-lg border border-[#2A2A3A] p-5 hover:border-[#2A2A3A] transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#A9A8C3] mb-1">Status</p>
                            <p class="text-lg font-semibold {{ $config && $config->is_published ? 'text-emerald-400' : 'text-[#7E7D9C]' }}">
                                {{ $config && $config->is_published ? 'Published' : 'Draft' }}
                            </p>
                        </div>
                        <div class="w-12 h-12 {{ $config && $config->is_published ? 'bg-emerald-950/40 border border-emerald-800/40' : 'bg-[#111118] border border-[#2A2A3A]' }} rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $config && $config->is_published ? 'text-emerald-400' : 'text-[#7E7D9C]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-6 hover:border-[#A78BFA] transition-all group shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-[#F1F0FF] mb-2">Marketplace Links</h3>
                            <p class="text-[#A9A8C3] text-sm mb-4">Kelola link produk dan marketplace Anda</p>
                        </div>
                        <div class="w-12 h-12 bg-[#252535] border border-[#3A3A4E] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('storefront.links.index') }}" 
                       class="inline-flex items-center text-[#A78BFA] hover:text-[#C4B5FD] font-semibold text-sm transition-colors">
                        Kelola Links
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-6 hover:border-purple-400 transition-all group shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-[#F1F0FF] mb-2">Landing Page</h3>
                            <p class="text-[#A9A8C3] text-sm mb-4">Customize tampilan dan layout storefront</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-950/40 border border-purple-800/40 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('storefront.builder.edit') }}" 
                       class="inline-flex items-center text-purple-400 hover:text-purple-300 font-semibold text-sm transition-colors">
                        Buka Builder
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <div class="bg-[#1A1A24] border border-[#2A2A3A] rounded-xl p-6 hover:border-emerald-400 transition-all group shadow-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-[#F1F0FF] mb-2">Client Reviews</h3>
                            <p class="text-[#A9A8C3] text-sm mb-4">Kelola dan approve review dari customer</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-950/40 border border-emerald-800/40 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('storefront.reviews.index') }}" 
                       class="inline-flex items-center text-emerald-400 hover:text-emerald-300 font-semibold text-sm transition-colors">
                        Kelola Reviews
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Status Info -->
            @if(!$config || !$config->is_published)
            <div class="bg-[#1A1A24] border border-[#A78BFA]/30 rounded-xl p-5 shadow-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-[#A78BFA] mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-[#F1F0FF] mb-2">Langkah Setup Storefront</h4>
                        <ol class="list-decimal list-inside text-[#A9A8C3] space-y-1 text-sm">
                            <li>Tambahkan link marketplace atau produk</li>
                            <li>Atur tampilan landing page</li>
                            <li>Publish dan bagikan ke customer</li>
                        </ol>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-[#1A1A24] border border-emerald-500/30 rounded-xl p-5 shadow-lg">
                <div class="flex items-start justify-between">
                    <div class="flex items-start flex-1">
                        <svg class="w-5 h-5 text-emerald-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-[#F1F0FF] mb-2">Storefront Active</h4>
                            <p class="text-[#A9A8C3] text-sm mb-3">Share link ini ke customer:</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 bg-[#111118] px-3 py-2 rounded-lg border border-[#2A2A3A] text-sm text-emerald-300 font-mono">{{ $config->public_url }}</code>
                                <button onclick="copyToClipboard('{{ $config->public_url }}')" 
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium transition shadow-md">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link berhasil dicopy ke clipboard!');
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
    </script>
    @endpush
</x-app-layout>
