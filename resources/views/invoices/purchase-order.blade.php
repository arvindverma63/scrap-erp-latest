<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Receipt</title>

    <style>
        body {
            width: 80mm;
            margin: 0 auto;
            font-family: "Courier New", monospace; 
            font-size: 15px;
            line-height: 1.35;
            color: #000;
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .container {
            padding: 4px;
        }

        /* LOGO */
        .logo {
            text-align: center;
            margin-bottom: 4px;
        }

        .logo img {
            width: 90px;
            height: auto;
        }

        .company {
            text-align: center;
            margin-bottom: 6px;
        }

        .company strong {
            font-size: 15px;
            font-weight: bold;
            display: block;
        }

        .section-border {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 4px;
            border-bottom: 1px dashed #000;
        }

        th {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            border-bottom: none;
            font-weight: bold;
        }

        .signature {
            margin-top: 18px;
            text-align: right;
        }

        .footer {
            border-top: 1px dashed #000;
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
            padding-top: 6px;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0 !important;
            }

            * {
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                width: 80mm !important;
            }

            .container {
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Logo -->
        <div class="logo">
            <img src="{{ App\Models\Setting::where('key','company_logo')->first()->value }}" alt="Logo">
        </div>

        <!-- Company -->
        <div class="company">
            <strong>{{ App\Models\Setting::where('key','company_name')->first()->value }}</strong>
            {{ App\Models\Setting::where('key','company_address')->first()->value }}<br>
            Tel: {{ App\Models\Setting::where('key','phone_number')->first()->value }}<br>
            {{ App\Models\Setting::where('key','website_email')->first()->value }}
        </div>

        <!-- Receipt Info -->
        <div class="section-border" style="border-bottom:0;">
            <table>
                <tr>
                    <td><strong>Receipt No:</strong></td>
                    <td class="text-right"><strong>{{ $order->invoices->first()->invoice_number ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td class="text-right">{{ $order->created_at->format('Y-m-d') }}</td>
                </tr>
            </table>
        </div>

        <!-- Seller & Transaction -->
        <table>
            <tr>
                <td width="50%">
                    <strong>Seller</strong><br>
                    {{ $order->supplier->name ?? 'N/A' }}<br>
                    {{ $order->supplier->country_code.$order->supplier->phone }}
                </td>

                <td width="50%">
                    <strong>Transaction</strong><br>
                    Trx #: {{ $order->invoices->first()->invoice_number ?? '-' }}<br>
                    Pay: {{ $order->payment_method ?? '-' }}<br>
                    Cashier: {{ $order->cashier->name ?? '-' }}
                </td>
            </tr>
        </table>

        <!-- Product List -->
        <table>
            <thead>
                <tr>
                    <th style="width:10%;text-align:left">Sr</th>
                    <th style="width:50%;text-align:left">Item</th>
                    <th style="width:20%;text-align:left">Rate</th>
                    <th style="width:20%;text-align:center" class="text-right">Amt (JMD)</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($order->orderItems as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>
                            {{ $item->product->name }}<br>
                            Qty: {{ $item->quantity }} | {{ $item->weightUnit->name ?? '' }}
                        </td>
                        <td>{{ number_format($item->unit_price,2) }}</td>
                        <td class="text-right">{{ number_format($item->total_amount,2) }}</td>
                    </tr>
                @endforeach

                <!-- Totals -->
                <tr>
                    <td colspan="3" class="text-right">SubTotal:</td>
                    <td class="text-right">{{ number_format($order->total_amount+$order->less_scale_fee+$order->haulage_fee,2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Less Scale Fee:</td>
                    <td class="text-right">{{ number_format($order->less_scale_fee,2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Haulage Fee:</td>
                    <td class="text-right">{{ number_format($order->haulage_fee,2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Handling Fee:</td>
                    <td class="text-right">{{ number_format($order->handling_fee,2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">{{ number_format($order->total_amount,2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Due:</td>
                    <td class="text-right">{{ number_format($order->paid_amount,2) }}</td>
                </tr>

                <tr class="total-row">
                    <td colspan="3" class="text-right">Paid:</td>
                    <td class="text-right">{{ number_format($order->total_amount-$order->paid_amount,2) }}</td>
                    {{-- <td class="text-right">{{ number_format($order->balance_amount,2) }}</td> --}}
                </tr>
            </tbody>
        </table>

        <!-- Signature -->
        <div class="signature">
            <strong>Authorized Signature</strong><br>
            @if (!empty($order->invoice->digital_signature))
                <img src="{{ public_path($order->invoice->digital_signature) }}" alt="Signature" height="40">
            @else
                <span>No signature</span>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            THANK YOU FOR YOUR BUSINESS!<br>
            Please visit again.
        </div>

    </div>

</body>

</html>
