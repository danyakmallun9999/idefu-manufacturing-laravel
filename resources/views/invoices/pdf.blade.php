<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            font-size: 22px;
            line-height: 1.5;
            background: #fff;
        }

        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 8px 15px;
            background: white;
        }

        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 4px solid #F98127;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .company-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-info-table td {
            vertical-align: top;
            padding: 0;
        }

        .logo-placeholder {
            width: 100px;
            height: 100px;
            background: #F98127;
            border-radius: 10px;
            text-align: center;
            vertical-align: middle;
            color: white;
            font-weight: bold;
            font-size: 30px;
            line-height: 100px;
        }

        .company-details {
            padding-left: 20px;
            vertical-align: middle;
        }

        .company-name {
            font-size: 62px;
            font-weight: bold;
            color: #F98127;
            margin-bottom: 8px;
            line-height: 100px;
        }

        .company-address {
            color: #666;
            line-height: 1.5;
            font-size: 20px;
        }

        .invoice-header {
            text-align: right;
            width: 250px;
        }

        .invoice-title {
            font-size: 56px;
            font-weight: bold;
            color: #F98127;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 24px;
            color: #666;
        }

        .billing-section {
            margin-bottom: 20px;
        }

        .billing-table {
            width: 100%;
            border-collapse: collapse;
        }

        .billing-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .billing-table td:last-child {
            padding-right: 0;
            padding-left: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #F98127;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .address-info {
            line-height: 1.5;
            color: #333;
            font-size: 20px;
        }

        .invoice-details {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #F98127;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            font-weight: bold;
            color: #374151;
            font-size: 20px;
        }

        .detail-value {
            color: #1f2937;
            font-size: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .items-table th {
            background: #F98127;
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 20px;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 20px;
            text-align: center;
        }

        .product-image {
            width: 180px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            margin-bottom: 8px;
        }

        .product-details {
            text-align: left;
            vertical-align: top;
        }

        .product-details-inline {
            color: #666;
            font-size: 18px;
            line-height: 1.4;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .items-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .totals-table {
            min-width: 350px;
            margin-left: auto;
        }

        .totals-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table tr:last-child {
            border-bottom: 3px solid #F98127;
            font-weight: bold;
            font-size: 24px;
        }

        .totals-table td {
            padding: 10px 15px;
            font-size: 20px;
        }

        .total-label {
            text-align: right;
            color: #374151;
        }

        .total-amount {
            text-align: right;
            font-weight: 600;
        }

        .payment-section {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .payment-title {
            font-size: 24px;
            font-weight: bold;
            color: #F98127;
            margin-bottom: 10px;
        }

        .payment-method {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .method-name {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
            font-size: 20px;
        }

        .method-details {
            font-size: 19px;
            color: #6b7280;
        }

        .terms-section {
            margin-bottom: 20px;
        }

        .terms-title {
            font-size: 24px;
            font-weight: bold;
            color: #F98127;
            margin-bottom: 10px;
        }

        .terms-content {
            font-size: 19px;
            color: #4b5563;
            line-height: 1.5;
        }

        .terms-content ul {
            margin-left: 20px;
        }

        .terms-content li {
            margin-bottom: 6px;
        }

        .signature-section {
            margin-top: 25px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 20px;
        }

        .signature-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 120px;
            font-size: 21px;
        }

        .signature-line {
            border-top: 2px solid #374151;
            padding-top: 18px;
            font-size: 19px;
            color: #6b7280;
            margin-bottom: 18px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 18px;
            color: #6b7280;
        }

        @media print {
            .invoice-container {
                padding: 5px 10px;
                max-width: none;
            }

            .signature-section {
                page-break-inside: avoid;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="invoice-header">
                        <table class="company-info-table">
                            <tr>
                                <td style="width: 120px; padding-right: 15px; vertical-align: middle;">
                                    @if ($logoBase64)
                                        <img src="{{ $logoBase64 }}" alt="Logo Perusahaan"
                                            style="width: 100px; object-fit: contain; border-radius: 10px;">
                                    @else
                                        <div class="logo-placeholder">
                                            IDEFU
                                        </div>
                                    @endif
                                </td>
                                <td class="company-details">
                                    <div class="company-name">{{ $invoice->company_name }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-left: 0;">
                                    <div class="company-address">
                                        {{ $invoice->company_address ?? 'Office : Jl. Hugeng Imam Santoso Km.09 Ngabul Tahunan Jepara, Central Java Indonesia. Workshop : Bawu Rt 10/02 Batealit Jepara.' }}<br>
                                        Telp: {{ $invoice->company_phone ?? '+6285741555089' }} | Email:
                                        {{ $invoice->company_email ?? 'idesign@idefu.co.id' }} |
                                        {{ $invoice->company_website ?? 'idefu.co.id' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="invoice-header">
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-number"># {{ $invoice->invoice_number }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Billing Section -->
        <div class="billing-section">
            <table class="billing-table">
                <tr>
                    <td>
                        <div class="section-title">BILL TO</div>
                        <div class="address-info">
                            <strong>{{ $invoice->order->customer->name ?? 'N/A' }}</strong><br>
                            {{ $invoice->order->customer->address ?? 'Alamat customer akan ditampilkan di sini' }}
                        </div>
                    </td>
                    <td>
                        <div class="section-title">SHIP TO</div>
                        <div class="address-info">
                            <strong>{{ $invoice->order->customer->name ?? 'N/A' }}</strong><br>
                            {{ $invoice->shipping_address ?? ($invoice->order->customer->address ?? 'Alamat pengiriman akan ditampilkan di sini') }}<br>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Invoice Date:</span>
                    <span
                        class="detail-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Due Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value">{{ $invoice->payment_status_display }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">PO Number:</span>
                    <span class="detail-value">{{ $invoice->po_number ?? 'PO-2024-001' }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%" class="text-center">Image</th>
                    <th width="30%">Product Description</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Unit Price</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">
                        @if ($productImageBase64)
                            <img src="{{ $productImageBase64 }}" alt="Product Image" class="product-image">
                        @else
                            <div class="product-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 17px;">
                                No Image
                            </div>
                        @endif
                    </td>
                    <td class="product-details">
                        <strong style="font-size: 22px;">{{ $invoice->order->product_name }}</strong><br>
                        @if ($invoice->order->product_type === 'custom')
                            <div class="product-details-inline">
                                <strong>Specification:</strong> {{ $invoice->order->product_specification ?? 'Custom made product according to requirements' }} | 
                                <strong>Type:</strong> Custom Product
                            </div>
                        @else
                            @if ($invoice->order->product)
                                <div class="product-details-inline">
                                    <strong>Model:</strong> {{ $invoice->order->product->model ?? '-' }} | 
                                    <strong>Wood Type:</strong> {{ $invoice->order->product->wood_type ?? '-' }} | 
                                    <strong>Details:</strong> {{ $invoice->order->product->details ?? '-' }}
                                </div>
                            @else
                                <div class="product-details-inline">
                                    <strong>Type:</strong> Fixed Product
                                </div>
                            @endif
                        @endif
                    </td>
                    <td class="text-center" style="font-size: 20px;">{{ $invoice->order->quantity }} pcs</td>
                    <td class="text-right" style="font-size: 20px;">Rp
                        {{ number_format($invoice->order->product_type === 'custom' ? $invoice->subtotal / $invoice->order->quantity : $invoice->order->total_price ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-size: 20px; font-weight: bold;">Rp
                        {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-amount">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="total-label">Shipping Cost:</td>
                    <td class="total-amount">Rp {{ number_format($invoice->shipping_cost ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if (($invoice->discount_amount ?? 0) > 0)
                    <tr>
                        <td class="total-label">Discount:</td>
                        <td class="total-amount" style="color: #059669;">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if ($invoice->discount_reason)
                        <tr style="border-bottom: none;">
                            <td class="total-label" style="font-size: 18px; color: #6b7280; font-style: italic;">Discount reason:</td>
                            <td class="total-amount" style="font-size: 18px; color: #6b7280; font-style: italic;">{{ $invoice->discount_reason }}</td>
                        </tr>
                    @endif
                @endif
                @if ($invoice->order->product_type !== 'custom' && $invoice->paid_amount > 0)
                    <tr>
                        <td class="total-label">Down Payment:</td>
                        <td class="total-amount">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($invoice->order->product_type !== 'custom')
                    <tr>
                        <td class="total-label"><strong>TOTAL:</strong></td>
                        <td class="total-amount" style="color: #F98127;"><strong>Rp
                                {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    @if ($invoice->paid_amount > 0 && $invoice->paid_amount < $invoice->total_amount)
                        <tr>
                            <td class="total-label">Remaining Payment:</td>
                            <td class="total-amount" style="color: #dc2626;">Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td class="total-label"><strong>PRICE TO BE CALCULATED:</strong></td>
                        <td class="total-amount" style="color: #dc2626; font-style: italic;"><strong>Will be calculated
                                after production is completed.</strong></td>
                    </tr>
                @endif
            </table>
        </div>

        @if ($invoice->order->product_type === 'custom')
            <!-- Payment Information for Custom Products -->
            <div class="payment-section">
                <div class="payment-title">Payment Information</div>
                <div class="payment-method">
                    <div class="method-name">Payment Status</div>
                    <div class="method-details">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 19px;">Total Down Payment Paid:</span>
                            <span style="font-weight: bold; color: #059669; font-size: 19px;">Rp
                                {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 19px;">Status:</span>
                            <span
                                style="font-weight: bold; color: #F98127; font-size: 19px;">{{ $invoice->payment_status_display }}</span>
                        </div>
                        <div
                            style="margin-top: 12px; padding: 10px; background: #fef3c7; border-radius: 6px; font-size: 18px; color: #92400e;">
                            <strong>Info:</strong> Final price will be calculated after production
                            is completed.
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Payment Methods for Fixed Products -->
            <div class="payment-section">
                <div class="payment-title">Payment Methods</div>
                <div class="payment-method">
                    <div class="method-name">{{ $invoice->payment_method_display ?? 'Bank Transfer BCA' }}</div>
                    @if ($invoice->bank_name && $invoice->account_number)
                        <div class="method-details">
                            Bank: {{ $invoice->bank_name }}<br>
                            Account No: {{ $invoice->account_number }}<br>
                            Account Name: {{ $invoice->account_holder ?? 'Idefu Furniture' }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Terms and Conditions -->
        <div class="terms-section">
            <div class="terms-title">Terms and Conditions</div>
            <div class="terms-content">
                @if ($invoice->terms_conditions)
                    {!! nl2br(e($invoice->terms_conditions)) !!}
                @else
                    <ul>
                        <li>Payment must be made before the due date specified on the invoice.</li>
                        <li>Items that have been ordered and produced cannot be cancelled or returned.</li>
                        <li>Specification changes after production has started will incur additional costs.</li>
                        <li>Production time is calculated after payment is received and final specifications are approved.</li>
                        <li>All disputes will be resolved through consultation or arbitration.</li>
                    </ul>
                @endif
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-title">Seller</div>
                        <div class="signature-line">
                            <strong>{{ $invoice->seller_name ?? 'Idefu Furniture' }}</strong><br>
                            Date: ___________
                        </div>
                    </td>
                    <td>
                        <div class="signature-title">Buyer</div>
                        <div class="signature-line">
                            <strong>{{ $invoice->order->customer->name ?? 'Customer Name' }}</strong><br>
                            Date: ___________
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for trusting your business to us.</strong></p>
            <p>This invoice is created electronically and is valid without a wet signature.</p>
            <p>For questions regarding this invoice, contact us at idesign@idefu.co.id or +6285741555089</p>
        </div>
    </div>
</body>

</html>
