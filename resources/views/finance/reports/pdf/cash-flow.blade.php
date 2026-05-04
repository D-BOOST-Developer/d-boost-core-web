<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cash Flow Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; }
        
        .amount-col { text-align: right; width: 130px; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 11px; color: #666; margin-top: 15px; margin-bottom: 5px; }
        
        .total-row td { border-top: 1px solid #333; font-weight: bold; }
        .subtotal-row td { background-color: #f9f9f9; font-weight: bold; color: #555; }
        .grand-total td { background-color: #f4f4f4; border-top: 2px solid #333; font-size: 14px; padding: 10px; }
        
        .text-red { color: #cc0000; }
        .text-green { color: #006600; }
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
        <p><strong>Statement of Cash Flows</strong></p>
        <p style="font-style: italic; font-size: 11px;">
            Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </p>
    </div>

    <!-- Opening Balance -->
    <table style="background-color: #f4f4f4; border: 1px solid #ddd;">
        <tr>
            <td style="font-weight: bold;">Cash at Beginning of Period</td>
            <td class="amount-col" style="font-weight: bold;">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Cash Inflows -->
    <div class="section-title text-green">Cash Inflows (Operating Activities)</div>
    <table>
        <tr>
            <td>Cash Sales</td>
            <td class="amount-col">Rp {{ number_format($cashSales, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Card Payments Received</td>
            <td class="amount-col">Rp {{ number_format($creditCardSales, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bank Transfers Received</td>
            <td class="amount-col">Rp {{ number_format($bankTransfers, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>E-Wallet Payments</td>
            <td class="amount-col">Rp {{ number_format($eWalletSales, 0, ',', '.') }}</td>
        </tr>
        <tr class="subtotal-row">
            <td class="text-green">Total Cash Inflows</td>
            <td class="amount-col text-green">Rp {{ number_format($totalInflows, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Cash Outflows -->
    <div class="section-title text-red">Cash Outflows (Operating Activities)</div>
    <table>
        <tr>
            <td>Cash Expenses</td>
            <td class="amount-col">(Rp {{ number_format($cashExpenses, 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td>Card Expenses</td>
            <td class="amount-col">(Rp {{ number_format($cardExpenses, 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td>Bank Transfer Expenses</td>
            <td class="amount-col">(Rp {{ number_format($bankExpenses, 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td>E-Wallet Expenses</td>
            <td class="amount-col">(Rp {{ number_format($eWalletExpenses, 0, ',', '.') }})</td>
        </tr>
        <tr class="subtotal-row">
            <td class="text-red">Total Cash Outflows</td>
            <td class="amount-col text-red">(Rp {{ number_format($totalOutflows, 0, ',', '.') }})</td>
        </tr>
    </table>

    <!-- Net Cash Flow -->
    <table>
        <tr class="total-row">
            <td>Net Increase / (Decrease) in Cash</td>
            <td class="amount-col {{ $netCashFlow >= 0 ? 'text-green' : 'text-red' }}">
                Rp {{ number_format($netCashFlow, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <!-- Closing Balance -->
    <table style="margin-top: 20px;">
        <tr class="grand-total">
            <td>CASH AT END OF PERIOD</td>
            <td class="amount-col">
                Rp {{ number_format($closingBalance, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top: 60px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #eee; padding-top: 10px;">
        Generated on {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}
    </div>
</body>
</html>
