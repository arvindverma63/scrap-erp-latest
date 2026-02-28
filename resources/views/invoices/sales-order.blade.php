<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333333;
            line-height: 1.4;
            font-size: 13px
        }

        @page {
            body {
                box-shadow: none;
                padding: 0;
                margin: 0;
                border: 0
            }
        }

        strong {
            color: #000;
            font-size: 14px;
        }

        .invoice-box {
            margin: auto;
            font-size: 14px;
            background: #fff;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 0;
        }

        .header .left {
            display: table-cell;
            vertical-align: top;
        }

        .header .right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #14727b;
        }

        body p {
            margin: 5px 0;
        }



        .info {
            border-top: 2px solid #14727b;
            border-bottom: 2px solid #14727b;
            padding: 5px 0;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info div {
            display: inline-block;
            width: 49%;
            vertical-align: top;
        }

        .info  .right table {
                width: 45%;
                border: 0;
                margin-top: 5px;
                margin-left: auto;

        }
        .info .right table th,.info .right table td{background: transparent;border:0}
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table th,
        table td {
            border: 1px solid #14727b;
            padding: 6px;
            text-align: center;
            font-size: 13px;
        }

        table th {
            background: #14727b40;
            color: #000;
        }

        table td.description {
            text-align: left;
        }

        .totals {
            width: 300px;
            float: right;
            margin-top: 0px;
        }

        .totals table {
            border: none;
        }

        .totals td {
            padding: 6px;
            text-align: right;
            font-size: 13px;
        }

        .totals tr td:first-child {
            text-align: left;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #666;
            clear: both;
            position: fixed;
            bottom: 10px;
            width: 100%;
        }

        .brand {
            margin-top: 30px;
            font-size: 20px;
        }

        .brand span:first-child {
            color: #14727b;
            font-weight: bold;
        }

        .brand span:last-child {
            color: #f39c12;
            font-weight: bold;
        }

        span.logo {
            display: block;
            height: 110px;
        }

        span.logo img {
            width: 120px;
            object-fit: contain;
        }

        .left table th,
        .left table td {
            border: 0;
            background: transparent;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="left">
                <span class="logo"><img src="{{App\Models\Setting::where('key','company_logo')->first()->value}}" alt="Logo"></span>
            </div>
            <div class="right">
                 <h2>{{ App\Models\Setting::where('key', 'company_name')->first()->value }}</h2>
                <p>{{App\Models\Setting::where('key', 'company_address')->first()->value}}<br>
                    Tel: {{App\Models\Setting::where('key', 'phone_number')->first()->value}}<br>
                    {{App\Models\Setting::where('key', 'website_email')->first()->value}}</p>
            </div>
        </div>

        <div class="info">
            <div class="left">
                <strong style="margin-bottom:6px">Bill To</strong>
                <p>{{ $order->customer->name ?? 'N/A' }}</p>
                <p>{{ $order->customer->address ?? '' }}</p>
                <p>{{ $order->customer->phone ?? '' }}</p>
               
            </div>

            <div class="right">
                <table class="table  table-sm" border="0">
                    <tbody>
                        <tr>
                            <th style="text-align:left;padding:4px;text-align: right;">Date :</th>
                            <td style="text-align:right; padding:4px;">{{ $order->created_at->format('m-d-Y') }}
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:4px;text-align: right;">Invoice No.:</th>
                            <td style="text-align:left;padding:4px;text-align: right;">
                                {{ $order->invoices->first()->invoice_number ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:4px;text-align: right;">TRN :</th>
                            <td style="text-align:left;padding:4px;text-align: right;">{{ $order->customer->tax ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sr.No.</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->weightUnit->name ?? '' }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}
                            {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}</td>
                        <td>{{ number_format($item->total_amount, 2) }}
                            {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>{{ number_format($order->total_amount, 2) }}
                        {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}</td>
                </tr>
                <tr>
                    <td>Paid Amount:</td>
                    <td>{{ number_format($order->total_amount - $order->paid_amount, 2) }}
                        {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}</td>
                </tr>
                <tr>
                    <td><strong>Due Amount:</strong></td>
                    <td><strong>{{ number_format($order->paid_amount, 2) }}
                            {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}</strong></td>
                </tr>
            </table>

            <!-- Signature -->
            <div class="signature">
            <strong>Authorized Signature</strong><br>
            @if (!empty($order->invoice->digital_signature))
                <img style="height: 100px;width: 100px;" src="{{ public_path($order->invoice->digital_signature) }}" alt="Signature">
            @else
                <span>No signature available</span>
            @endif
            
        </div>
        </div>

        <div class="footer">
            Please make payments via cheques or wire transfer to CM Recycling Co. Ltd.<br>
            National Commercial Bank<br>
            Acct# 361069188 – Portmore Branch
        </div>
    </div>
</body>

</html>