<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja - {{ $sale->invoice_number }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --text-main: #111827;
            --text-muted: #6B7280;
            --border-dash: dashed 1.5px #E5E7EB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: var(--text-main);
            background: #E5E7EB;
            /* Latar abu di luar kertas struk */
            display: flex;
            justify-content: center;
            padding: 2rem;
            -webkit-font-smoothing: antialiased;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .receipt-container {
            background: #ffffff;
            width: 100%;
            max-width: 380px;
            /* Standar proporsi struk termal (80mm) */
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        /* Efek pinggiran kertas robek (sawtooth) di atas dan bawah kertas */
        .receipt-container::before,
        .receipt-container::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 6px;
            background-size: 12px 100%;
            background-repeat: repeat-x;
        }

        .receipt-container::before {
            top: -6px;
            background-image: linear-gradient(-45deg, #ffffff 6px, transparent 0), linear-gradient(45deg, #ffffff 6px, transparent 0);
        }

        .receipt-container::after {
            bottom: -6px;
            background-image: linear-gradient(135deg, #ffffff 6px, transparent 0), linear-gradient(-135deg, #ffffff 6px, transparent 0);
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .store-logo {
            width: 44px;
            height: 44px;
            background: #111827;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: white;
        }

        .store-logo svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
        }

        .store-name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .store-info {
            color: var(--text-muted);
            font-size: 11px;
            line-height: 1.5;
        }

        /* Meta Info (Tgl, Kasir, dll) */
        .meta-info {
            display: flex;
            justify-content: space-between;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            margin-bottom: 16px;
            text-transform: uppercase;
        }

        .meta-info>div {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-info .right {
            text-align: right;
        }

        /* Divider */
        .divider {
            border-top: var(--border-dash);
            margin: 16px 0;
        }

        /* Items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-row {
            margin-bottom: 14px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .item-row:last-child {
            margin-bottom: 0;
        }

        .item-name {
            font-weight: 600;
            font-size: 12px;
            line-height: 1.4;
        }

        .item-calc {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-muted);
        }

        .item-price-qty {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }

        .item-total {
            font-weight: 500;
            color: var(--text-main);
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
        }

        .item-discount {
            font-size: 10px;
            color: #ef4444;
            /* Warna merah untuk diskon */
            display: flex;
            justify-content: space-between;
            font-family: 'JetBrains Mono', monospace;
            margin-top: -2px;
        }

        /* Totals */
        .totals {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-muted);
        }

        .total-row.grand-total {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            padding-top: 12px;
            border-top: var(--border-dash);
            margin-top: 4px;
        }

        .amount {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
        }

        .grand-total .amount {
            font-weight: 700;
        }

        /* Payment Info Box */
        .payment-info {
            margin-top: 20px;
            padding: 14px;
            background: #F3F4F6;
            border-radius: 8px;
            font-size: 11px;
            text-align: center;
            color: var(--text-muted);
            border: 1px dashed #D1D5DB;
        }

        .payment-method {
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
            font-size: 12px;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .greeting {
            font-weight: 600;
            color: var(--text-main);
            font-size: 14px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        /* Action Buttons (Tidak terlihat saat print) */
        .actions {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 100;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 99px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-print {
            background: #111827;
            color: white;
        }

        .btn-close {
            background: #ffffff;
            color: #374151;
            border: 1px solid #E5E7EB;
        }

        /* Print Media Query */
        @media print {
            body {
                background: transparent;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                margin: 0 auto;
                padding: 24px;
                max-width: 380px;
            }

            .receipt-container::before,
            .receipt-container::after {
                display: none;
            }

            .actions {
                display: none !important;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    @php
        $biz = $businessProfile ?? null;
        $storeName = $biz->business_name ?? auth()->user()?->name ?? 'D-BOOST STORE';
        $storeAddress = $biz->business_address ?? 'Jl. Teknologi Digital No. 1';
        $storePhone = $biz->contact_phone ?? '+62 812 3456 7890';
    @endphp

    <div class="receipt-container">

        <!-- Header -->
        <div class="header">
            <div class="store-logo" {!! $biz && $biz->business_logo_url ? 'style="background-color: transparent; padding: 0;"' : '' !!}>
                @if($biz && $biz->business_logo_url)
                    <img src="{{ $biz->business_logo_url }}" alt="Logo Toko"
                        style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px;">
                @else
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                @endif
            </div>
            <div class="store-name">{{ $storeName }}</div>
            <div class="store-info">
                {{ $storeAddress }}<br>
                Telp: {{ $storePhone }}
            </div>
        </div>

        <!-- Meta Information -->
        <div class="meta-info">
            <div class="left">
                <span>INV : {{ $sale->invoice_number }}</span>
                <span>TGL : {{ $sale->sale_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="right">
                <span>KASIR: {{ substr($sale->creator->name ?? 'Admin', 0, 10) }}</span>
                <span>MET : {{ strtoupper(str_replace('_', ' ', $sale->payment_method ?? 'CASH')) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Items -->
        <div class="items">
            @foreach($sale->salesDetails as $detail)
                <div class="item-row">
                    <div class="item-name">{{ $detail->product->name ?? 'Produk Dihapus' }}</div>
                    <div class="item-calc">
                        <span class="item-price-qty">{{ $detail->quantity }} x
                            {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                        <span
                            class="item-total">{{ number_format($detail->quantity * $detail->unit_price, 0, ',', '.') }}</span>
                    </div>
                    @if($detail->discount_percentage > 0)
                        @php
                            $sub = $detail->quantity * $detail->unit_price;
                            $discAmt = $sub * ($detail->discount_percentage / 100);
                        @endphp
                        <div class="item-discount">
                            <span>Diskon ({{ number_format($detail->discount_percentage, 1) }}%)</span>
                            <span>-{{ number_format($discAmt, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span class="amount">{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
            </div>

            @if($sale->discount_amount > 0)
                <div class="total-row" style="color: #ef4444;">
                    <span>Diskon Global</span>
                    <span class="amount">-{{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($sale->tax_amount > 0)
                <div class="total-row">
                    <span>Pajak (10%)</span>
                    <span class="amount">{{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span class="amount">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div class="payment-method">
                @if($sale->payment_method === 'bank_transfer') TRANSFER BANK
                @elseif($sale->payment_method === 'qris') QRIS
                @elseif($sale->payment_method === 'cash') TUNAI
                @elseif($sale->payment_method === 'e_wallet') E-WALLET
                @else {{ strtoupper(str_replace('_', ' ', $sale->payment_method)) }}
                @endif
            </div>
            <div>
                Status: <span
                    style="font-weight: 700; color: {{ $sale->status === 'completed' ? '#10b981' : ($sale->status === 'pending' ? '#f59e0b' : '#ef4444') }}">{{ strtoupper($sale->status) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="greeting">Terima Kasih!</div>
            <div>Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan.</div>

            @if($sale->notes)
                <div class="divider" style="margin: 12px 0;"></div>
                <div style="text-align: left;">
                    <strong>Catatan:</strong><br>
                    {{ $sale->notes }}
                </div>
            @endif

            <div class="divider" style="margin: 16px 0 12px 0;"></div>
            <div
                style="font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                Digitalize your business with <span
                    style="color: #004bb5ff; font-family: 'Arial Black', 'Inter', sans-serif; font-weight: 900; letter-spacing: 0.5px;">DBOOST</span>
            </div>
        </div>

    </div>

    <!-- Floating Actions -->
    <div class="actions">
        <button class="btn btn-close" onclick="window.close()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Tutup
        </button>
        <button class="btn btn-print" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Cetak Struk
        </button>
    </div>

</body>

</html>