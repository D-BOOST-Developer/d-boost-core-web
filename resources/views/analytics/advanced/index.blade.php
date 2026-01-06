<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Advanced Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-white">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">Advanced Analytics</h3>
                            <p class="text-gray-400">Deep insights and advanced business analytics</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Data Points -->
                        <div class="bg-gray-700/50 rounded-lg p-6 border border-gray-600">
                            <h4 class="text-lg font-semibold text-white mb-2">Data Points</h4>
                            <p class="text-3xl font-bold text-cyan-400 mb-2">1,250</p>
                            <p class="text-gray-400 text-sm">Analyzed data points</p>
                        </div>

                        <!-- Active Reports -->
                        <div class="bg-gray-700/50 rounded-lg p-6 border border-gray-600">
                            <h4 class="text-lg font-semibold text-white mb-2">Active Reports</h4>
                            <p class="text-3xl font-bold text-cyan-400 mb-2">6</p>
                            <p class="text-gray-400 text-sm">Running analytics</p>
                        </div>

                        <!-- Insights Generated -->
                        <div class="bg-gray-700/50 rounded-lg p-6 border border-gray-600">
                            <h4 class="text-lg font-semibold text-white mb-2">Insights</h4>
                            <p class="text-3xl font-bold text-green-400 mb-2">23</p>
                            <p class="text-gray-400 text-sm">Generated insights</p>
                        </div>

                        <!-- Accuracy Score -->
                        <div class="bg-gray-700/50 rounded-lg p-6 border border-gray-600">
                            <h4 class="text-lg font-semibold text-white mb-2">Accuracy</h4>
                            <p class="text-3xl font-bold text-cyan-400 mb-2">94%</p>
                            <p class="text-gray-400 text-sm">Prediction accuracy</p>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <p class="text-gray-400">📈 Advanced Analytics Module - Coming Soon</p>
                        <p class="text-sm text-gray-500 mt-2">This module is under development and will be available in the next update.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
