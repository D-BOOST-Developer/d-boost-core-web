<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_number }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/D-BOOST-removebg.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #006BFF;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #006BFF, #00D4AA);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .company-logo svg {
            width: 30px;
            height: 30px;
            fill: white;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.5;
        }

        .invoice-title {
            text-align: right;
            flex: 1;
        }

        .invoice-title h1 {
            font-size: 36px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .invoice-meta {
            font-size: 11px;
            color: #666;
        }

        .invoice-meta strong {
            color: #333;
        }

        /* Invoice Details Section */
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .bill-to, .ship-to, .invoice-info {
            flex: 1;
            margin-right: 20px;
        }

        .bill-to:last-child, .ship-to:last-child, .invoice-info:last-child {
            margin-right: 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            min-height: 100px;
        }

        .customer-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .customer-details {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #006BFF;
            color: #1a1a1a;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table th:first-child {
            border-radius: 6px 0 0 0;
        }

        .items-table th:last-child {
            border-radius: 0 6px 0 0;
            text-align: right;
        }

        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .items-table tr:hover {
            background: #e8f5e8;
        }

        .item-description {
            font-weight: 500;
            color: #333;
            margin-bottom: 3px;
        }

        .item-sku {
            font-size: 9px;
            color: #666;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Summary Section */
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .summary-table {
            width: 300px;
        }

        .summary-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 8px 12px;
            font-size: 11px;
            border-bottom: 1px solid #e9ecef;
        }

        .summary-table .summary-label {
            text-align: left;
            color: #666;
            font-weight: 500;
        }

        .summary-table .summary-value {
            text-align: right;
            color: #333;
            font-weight: 600;
        }

        .summary-table .total-row {
            background: #006BFF;
            color: #1a1a1a;
            font-weight: bold;
            font-size: 14px;
        }

        .summary-table .total-row td {
            border: none;
            padding: 12px;
        }

        .summary-table .total-row td:first-child {
            width: 30%;
            text-align: left;
        }

        .summary-table .total-row td:last-child {
            width: 70%;
            text-align: right;
            padding-right: 20px;
        }

        /* Payment & Notes Section */
        .payment-notes {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .payment-info, .notes-section {
            flex: 1;
            margin-right: 20px;
        }

        .notes-section {
            margin-right: 0;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #006BFF;
            padding: 15px;
            border-radius: 0 6px 6px 0;
        }

        .payment-method {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .payment-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }

        /* Footer */
        .invoice-footer {
            border-top: 2px solid #e9ecef;
            padding-top: 20px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .footer-note {
            margin-bottom: 10px;
            font-style: italic;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 10px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 0;
                size: A4;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .invoice-container {
                padding: 15px;
            }
            
            .invoice-header {
                flex-direction: column;
                text-align: center;
            }
            
            .invoice-title {
                text-align: center;
                margin-top: 20px;
            }
            
            .invoice-details {
                flex-direction: column;
            }
            
            .bill-to, .ship-to, .invoice-info {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .payment-notes {
                flex-direction: column;
            }
            
            .payment-info {
                margin-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-logo">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="company-name">SDA UMKM</div>
                <div class="company-tagline">Smart Data Analysis Dashboard</div>
                <div class="company-details">
                    Jl. Teknologi No. 123, Jakarta Selatan<br>
                    Phone: +62 21 1234 5678<br>
                    Email: info@sdaumkm.com<br>
                    Website: www.sdaumkm.com
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-meta">
                    <div><strong>Invoice #:</strong> {{ $sale->invoice_number }}</div>
                    <div><strong>Date:</strong> {{ $sale->sale_date->format('d M Y') }}</div>
                    <div><strong>Due Date:</strong> {{ $sale->sale_date->copy()->addDays(30)->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Invoice Details Section -->
        <div class="invoice-details">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="detail-box">
                    @if($sale->customer)
                        <div class="customer-name">{{ $sale->customer->name }}</div>
                        <div class="customer-details">
                            @if($sale->customer->email)
                                Email: {{ $sale->customer->email }}<br>
                            @endif
                            @if($sale->customer->phone)
                                Phone: {{ $sale->customer->phone }}<br>
                            @endif
                            @if($sale->customer->address)
                                {{ $sale->customer->address }}<br>
                            @endif
                            @if($sale->customer->city)
                                {{ $sale->customer->city }}
                                @if($sale->customer->postal_code), {{ $sale->customer->postal_code }}@endif
                            @endif
                        </div>
                    @else
                        <div class="customer-name">Walk-in Customer</div>
                        <div class="customer-details">
                            No customer information recorded
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="ship-to">
                <div class="section-title">Ship To</div>
                <div class="detail-box">
                    @if($sale->customer)
                        <div class="customer-name">{{ $sale->customer->name }}</div>
                        <div class="customer-details">
                            @if($sale->customer->address)
                                {{ $sale->customer->address }}<br>
                            @endif
                            @if($sale->customer->city)
                                {{ $sale->customer->city }}
                                @if($sale->customer->postal_code), {{ $sale->customer->postal_code }}@endif
                            @endif
                        </div>
                    @else
                        <div class="customer-name">Walk-in Customer</div>
                        <div class="customer-details">
                            No shipping address
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="invoice-info">
                <div class="section-title">Invoice Info</div>
                <div class="detail-box">
                    <div class="customer-details">
                        <strong>Sales Rep:</strong> {{ $sale->creator->name ?? 'Admin' }}<br>
                        <strong>Payment Terms:</strong> Net 30<br>
                        <strong>Payment Method:</strong> {{ ucfirst($sale->payment_method ?? 'cash') }}<br>
                        <strong>Status:</strong> 
                        <span class="payment-status status-{{ strtolower($sale->status) }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="items-section">
            <div class="section-title">Items</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 40%">Description</th>
                        <th style="width: 15%" class="text-center">Qty</th>
                        <th style="width: 15%" class="text-right">Unit Price</th>
                        <th style="width: 10%" class="text-center">Disc</th>
                        <th style="width: 15%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->salesDetails as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="item-description">{{ $detail->product->name ?? 'Deleted Product' }}</div>
                            @if($detail->product)
                                <div class="item-sku">SKU: {{ $detail->product->sku ?? 'N/A' }}</div>
                            @else
                                <div class="item-sku" style="color: #dc2626;">Product has been deleted</div>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($detail->quantity, 0) }}</td>
                        <td class="text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($detail->discount_percentage > 0)
                                {{ number_format($detail->discount_percentage, 1) }}%
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">Rp {{ number_format($detail->line_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="invoice-summary">
            <div class="summary-table">
                <table>
                    <tr>
                        <td class="summary-label">Subtotal:</td>
                        <td class="summary-value">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                    <tr>
                        <td class="summary-label">Discount:</td>
                        <td class="summary-value">- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($sale->tax_amount > 0)
                    <tr>
                        <td class="summary-label">Tax (10%):</td>
                        <td class="summary-value">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>TOTAL:</td>
                        <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment & Notes Section -->
        <div class="payment-notes">
            <div class="payment-info">
                <div class="section-title">Payment Information</div>
                <div class="info-box">
                    <div class="payment-method">Payment Method: {{ ucwords(str_replace('_', ' ', $sale->payment_method ?? 'cash')) }}</div>
                    <div class="customer-details">
                        @if($sale->payment_method === 'bank_transfer' && isset($businessProfile) && $businessProfile->bank_name)
                            <strong>Bank Details:</strong><br>
                            Bank: {{ \App\Models\UserBusinessProfile::getIndonesianBanks()[$businessProfile->bank_name] ?? $businessProfile->bank_name }}<br>
                            Account: {{ $businessProfile->bank_account_number }}<br>
                            Name: {{ $businessProfile->bank_account_holder }}
                        @elseif($sale->payment_method === 'qris' && isset($businessProfile) && $businessProfile->qris_barcode_path)
                            <strong>QRIS Payment:</strong><br>
                            Scan the QR code below to complete payment
                        @elseif($sale->payment_method === 'credit_card')
                            Credit Card payment processed securely
                        @else
                            Cash payment received
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="notes-section">
                <div class="section-title">Notes</div>
                <div class="info-box">
                    @if($sale->notes)
                        {{ $sale->notes }}
                    @else
                        Thank you for your business! Please contact us if you have any questions about this invoice.
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-note">
                This is a computer-generated invoice. No signature required.
            </div>
            <div class="contact-info">
                <div class="contact-item">
                    <span>📧</span>
                    <span>info@sdaumkm.com</span>
                </div>
                <div class="contact-item">
                    <span>📞</span>
                    <span>+62 21 1234 5678</span>
                </div>
                <div class="contact-item">
                    <span>🌐</span>
                    <span>www.sdaumkm.com</span>
                </div>
            </div>
            <div>
                Generated on {{ now()->format('d M Y H:i:s') }} | Invoice {{ $sale->invoice_number }}
            </div>
        </div>
    </div>

    <!-- Print Button (Hidden in print) -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="background: #006BFF; color: #1a1a1a; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            🖨️ Print Invoice
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-left: 10px;">
            ✕ Close
        </button>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Print function
        function printInvoice() {
            window.print();
        }
        
        // Close function
        function closeWindow() {
            window.close();
        }
    </script>
</body>
</html>
