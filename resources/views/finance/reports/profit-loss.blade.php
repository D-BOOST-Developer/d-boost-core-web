<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between no-print">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Profit & Loss Statement') }}
                </h2>
                <div class="text-sm text-gray-500 mt-1">
                    Running financial performance report
                </div>
            </div>
            <div class="flex space-x-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 print:py-0">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 print:max-w-none print:px-0">
            <!-- Report Tabs -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700 no-print">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                    <li class="mr-2" role="presentation">
                        <a href="{{ route('finance.reports.profit-loss') }}" class="inline-block p-4 border-b-2 border-blue-600 rounded-t-lg active text-blue-600 dark:text-blue-500 hover:text-blue-600 dark:hover:text-blue-500" aria-current="page">
                            Profit & Loss
                        </a>
                    </li>
                    <li class="mr-2" role="presentation">
                        <a href="{{ route('finance.reports.cash-flow') }}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                            Cash Flow
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6 no-print">
                <form method="GET" action="{{ route('finance.reports.profit-loss') }}" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="w-full md:w-1/3">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Update Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Report Card -->
            <div id="print-area" class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 print:shadow-none print:border-0">
                <div class="p-8 print:p-0">
                    
                    <!-- Report Header (Professional) -->
                    <div class="mb-10 border-b-2 border-gray-800 pb-6">
                        <div class="flex justify-between items-center">
                            <!-- Left: Business Logo -->
                            <div class="w-1/4 flex justify-start">
                                 @if($businessProfile && $businessProfile->business_logo_path)
                                    <img src="{{ asset('storage/' . $businessProfile->business_logo_path) }}" alt="Company Logo" class="h-24 w-24 object-cover rounded-xl shadow-sm">
                                @else
                                    <div class="h-24 w-24 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center text-gray-400">
                                        <span class="text-xs text-center px-2">No Logo</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Center: Details -->
                            <div class="w-1/2 text-center">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-1">
                                    {{ $businessProfile->business_name ?? 'D-BOOST' }}
                                </h1>
                                <div class="text-gray-600 dark:text-gray-400 text-sm space-y-1 mb-2">
                                     @if($businessProfile)
                                        <p>{{ $businessProfile->business_address }} | {{ $businessProfile->contact_phone }}</p>
                                    @endif
                                </div>
                                
                                <h2 class="text-xl font-serif font-bold text-gray-900 dark:text-white uppercase tracking-wider decoration-double">
                                    Statement of Profit & Loss
                                </h2>
                                 <p class="text-gray-500 dark:text-gray-400 mt-2 text-xs font-medium italic">
                                    Period: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                                </p>
                            </div>

                            <!-- Right: D-BOOST Logo -->
                            <div class="w-1/4 flex justify-end">
                                <img src="{{ asset('images/D-BOOST.png') }}" alt="D-BOOST" class="h-24 w-auto object-contain rounded-xl shadow-sm bg-white p-2">
                            </div>
                        </div>
                    </div>

                    <!-- Report Body -->
                    <div class="max-w-4xl mx-auto font-serif text-sm">
                        <!-- Revenue Section -->
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-3 text-xs">Revenue</h3>
                            <table class="w-full">
                                <tr>
                                    <td class="py-2 pl-4">Sales Revenue</td>
                                    <td class="text-right py-2 w-48">Rp {{ number_format($salesRevenue, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 pl-4 text-red-600 italic">Less: Sales Returns</td>
                                    <td class="text-right py-1 w-48 text-red-600 border-b border-gray-300">(Rp {{ number_format($salesReturns, 0, ',', '.') }})</td>
                                </tr>
                                <tr class="font-bold text-base bg-gray-50 dark:bg-gray-700/30">
                                    <td class="py-3 pl-4">Net Sales</td>
                                    <td class="text-right py-3 w-48">Rp {{ number_format($netRevenue, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- COGS Section -->
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-3 text-xs">Cost of Sales</h3>
                            <table class="w-full">
                                <tr>
                                    <td class="py-2 pl-4">Cost of Goods Sold</td>
                                    <td class="text-right py-2 w-48 border-b border-gray-300">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="font-bold text-base bg-blue-50 dark:bg-blue-900/10">
                                    <td class="py-3 pl-4 text-blue-900 dark:text-blue-100">Gross Profit</td>
                                    <td class="text-right py-3 w-48 text-blue-900 dark:text-blue-100">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Operating Expenses -->
                        <div class="mb-8">
                            <h3 class="font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-3 text-xs">Operating Expenses</h3>
                            <table class="w-full">
                                @forelse($expensesByCategory as $category => $amount)
                                    <tr>
                                        <td class="py-2 pl-4 capitalize">{{ str_replace('_', ' ', $category) }}</td>
                                        <td class="text-right py-2 w-48 text-gray-600">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-2 pl-4 italic text-gray-500">No operating expenses</td>
                                        <td class="text-right py-2 w-48">-</td>
                                    </tr>
                                @endforelse
                                <tr>
                                    <td class="pt-2"></td>
                                    <td class="pt-2 border-b border-gray-300"></td>
                                </tr>
                                <tr class="font-bold text-base bg-red-50 dark:bg-red-900/10">
                                    <td class="py-3 pl-4 text-red-900 dark:text-red-100">Total Operating Expenses</td>
                                    <td class="text-right py-3 w-48 text-red-900 dark:text-red-100">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Operating Profit (Not calculated but theoretically Net Profit here) -->

                        <!-- Net Profit -->
                        <div class="pt-4 border-t-2 border-gray-800">
                            <table class="w-full">
                                <tr class="font-bold text-xl">
                                    <td class="py-4 pl-4 uppercase">Net Profit / (Loss)</td>
                                    <td class="text-right py-4 w-48 {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }} border-b-4 border-double border-gray-800">
                                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                            <div class="text-right mt-2 text-sm text-gray-500">
                                Net Profit Margin: {{ number_format($profitMargin, 1) }}%
                            </div>
                        </div>

                        <!-- Footer / Signatures -->
                         <div class="mt-20 pt-10 break-inside-avoid">
                            <div class="flex justify-between text-center px-10">
                                <div>
                                    <p class="mb-20 font-medium">Prepared By,</p>
                                    <div class="border-t border-gray-400 w-48 mx-auto"></div>
                                    <p class="mt-2 text-sm text-gray-500">{{ auth()->user()->name }}</p>
                                </div>
                                <div>
                                    <p class="mb-20 font-medium">Authorized By,</p>
                                    <div class="border-t border-gray-400 w-48 mx-auto"></div>
                                    <p class="mt-2 text-sm text-gray-500">{{ $businessProfile->owner_name ?? 'Owner' }}</p>
                                </div>
                            </div>
                            <div class="mt-12 text-center text-xs text-gray-400 border-t border-gray-200 pt-4">
                                Generated on {{ now()->format('d/m/Y') }} - {{ config('app.name') }} Partners
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            @page {
                margin: 0.5cm;
                size: A4;
            }
            body > * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .no-print { display: none !important; }
            
            body { 
                background-color: white !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
                font-family: Georgia, 'Times New Roman', Times, serif;
            }
            .bg-white { box-shadow: none !important; border: none !important; }
            
            /* Professional Table Styling for Print */
            table { width: 100%; border-collapse: collapse; }
            /* Force background colors in print */
            .bg-gray-50 { background-color: #f9fafb !important; }
            .bg-blue-50 { background-color: #eff6ff !important; }
            .bg-red-50 { background-color: #fef2f2 !important; }
        }
    </style>
</x-app-layout>
