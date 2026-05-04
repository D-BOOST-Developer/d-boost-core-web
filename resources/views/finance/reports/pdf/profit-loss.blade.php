<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Profit & Loss Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; }
        
        .amount-col { text-align: right; width: 120px; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 11px; color: #666; margin-top: 15px; margin-bottom: 5px; }
        
        .total-row td { border-top: 1px solid #333; font-weight: bold; }
        .grand-total td { background-color: #f4f4f4; border-top: 2px solid #333; font-size: 14px; padding: 10px; }
        
        .text-red { color: #cc0000; }
        .text-green { color: #006600; }
        
        .logo { width: 80px; height: auto; position: absolute; left: 0; top: 0; }
        .company-info { margin-left: 0; } /* Centered approach usually better for PDF */
    </style>
</head>
<body>
    <div class="header">
        @if($businessProfile && $businessProfile->business_logo_url)
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="{{ $businessProfile->business_logo_url }}" alt="Company Logo" style="height: 60px; width: auto; object-fit: contain;">
            </div>
        @endif
        <h1>{{ $businessProfile->business_name ?? 'D-BOOST Report' }}</h1>
        <p>{{ $businessProfile->business_address ?? '' }}</p>
        <p><strong>Profit & Loss Statement</strong></p>
        <p style="font-style: italic; font-size: 11px;">
            Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </p>
    </div>

    <!-- Revenue -->
    <div class="section-title">Revenue</div>
    <table>
        <tr>
            <td>Sales Revenue</td>
            <td class="amount-col">Rp {{ number_format($salesRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-red">Less: Sales Returns</td>
            <td class="amount-col text-red">(Rp {{ number_format($salesReturns, 0, ',', '.') }})</td>
        </tr>
        <tr class="total-row">
            <td>Net Sales</td>
            <td class="amount-col">Rp {{ number_format($netRevenue, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- COGS -->
    <div class="section-title">Cost of Sales</div>
    <table>
        <tr>
            <td>Cost of Goods Sold</td>
            <td class="amount-col">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Gross Profit</td>
            <td class="amount-col">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Operating Expenses -->
    <div class="section-title">Operating Expenses</div>
    <table>
        @forelse($expensesByCategory as $category => $amount)
            <tr>
                <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $category) }}</td>
                <td class="amount-col">Rp {{ number_format($amount, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="font-style: italic; color: #999;">No operating expenses</td>
            </tr>
        @endforelse
        <tr class="total-row">
            <td>Total Operating Expenses</td>
            <td class="amount-col text-red">(Rp {{ number_format($totalExpenses, 0, ',', '.') }})</td>
        </tr>
    </table>

    <!-- Net Profit -->
    <table style="margin-top: 30px;">
        <tr class="grand-total">
            <td>NET PROFIT / (LOSS)</td>
            <td class="amount-col {{ $netProfit >= 0 ? 'text-green' : 'text-red' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </td>
        </tr>
    </table>
    <div style="text-align: right; font-size: 11px; color: #666; margin-top: 5px;">
        Net Profit Margin: {{ number_format($profitMargin, 1) }}%
    </div>

    <!-- Footer -->
    <div style="margin-top: 60px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #eee; padding-top: 10px;">
        Generated on {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}
    </div>
</body>
</html>
