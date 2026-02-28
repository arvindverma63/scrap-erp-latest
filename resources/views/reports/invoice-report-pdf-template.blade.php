<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice report</title>
    <style>
        body {
            width: auto;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            margin-left: 10px;
            margin-right: 10px;
        }

        .container {
            width: auto;
            padding: 5px;
            border: 1px dashed #999;
        }

        .logo {
            text-align: center;
            margin-bottom: 4px;
        }

        .logo img {
            width: 80px;
            height: auto;
        }

        .company {
            text-align: center;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .company strong {
            color: #14727b;
            font-size: 14px;
            display: block;
        }

        .section-border {
            border-top: 1px dashed #999;
            border-bottom: 1px dashed #999;
            padding: 5px 0;
            margin: 6px 0;
        }

        .details {
            width: 100%;
            margin-bottom: 6px;
        }

        .details td {
            vertical-align: top;
            font-size: 12px;
            padding: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        th,
        td {
            padding: 4px 2px;
            border-bottom: 1px dashed #ccc;
        }

        th {
            color: #14727b;
            text-align: left;
        }

        td {
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            border-bottom: none;
        }

        .signature {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
        }

        .signature img {
            height: 35px;
            margin-top: 4px;
        }

        .footer {
            border-top: 1px dashed #999;
            text-align: center;
            font-size: 11px;
            margin-top: 8px;
            padding-top: 4px;
        }

        @media print {
            .container {
                width: 2.8in; /* Set exact print width */
                padding: 0; /* No padding to maximize space */
                margin: 0 auto; /* Center on page */
                border: none; /* Remove borders for clean print */
            }

            body {
                margin: 0;
                padding: 0;
            }

            @page {
                size: 2.8in auto;          /* 3-inch paper width */
                margin: 0;               /* Remove default printer margins */
            }
        }
    </style>
</head>

<body>
<div style="height: auto;max-height: auto;" class="container">
    <!-- Logo and Company -->
    <div class="logo">
        <img src="{{ App\Models\Setting::where('key', 'company_logo')->first()->value }}" alt="Logo">
    </div>

    <div class="company">
        <strong>{{ App\Models\Setting::where('key', 'company_name')->first()->value }}</strong>
        {{ App\Models\Setting::where('key', 'company_address')->first()->value }}<br>
        Tel: {{ App\Models\Setting::where('key', 'phone_number')->first()->value }}<br>
        {{ App\Models\Setting::where('key', 'website_email')->first()->value }}
    </div>

    <!-- Receipt Info -->
    <div class="section-border" style="border-bottom: 0">
        <table width="100%">
            <tr>
                <td><strong style="color:#14727b;">From Date:</strong></td>
                <td class="text-right"><strong>{{date('m-d-Y', strtotime(request('from_date')))}}</strong></td>
            </tr>
            <tr>
                <td><strong style="color:#14727b;">To Date:</strong></td>
                <td class="text-right"><strong>{{date('m-d-Y', strtotime(request('to_date')))}}</strong></td>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach ($invoices as $key => $invoice)
                @php
                    $total += $invoice->total_amount;
                @endphp
            @endforeach
            <tr>
                <td><strong style="color:#14727b;">Total Amount:</strong></td>
                <td class="text-right">{{number_format($total, 2)}}
                    {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}

                </td>
            </tr>
        </table>
    </div>

    <!-- Seller and Transaction Details -->
    <table class="details" width="100%">
        <tr>
            <td colspan="2" style="text-align: center;"><strong>Receipt Report</strong></td>
        </tr>
        <tr>
            <td width="50%">
                <strong></strong><br>
                <br>
            </td>
            <td width="50%">
                <strong></strong><br>
                <br>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <!-- Total Receipts / Invoices -->
            <td style="border: 1px solid #ccc; text-align: center; padding: 5px;">
                <div style="font-size: 12px;">@if(request('web_page') == 'RECEIPT') Total Receipts @else Total Invoices @endif</div>
                <div style="font-weight: bold; font-size: 16px;">
                    @if(request('web_page') == 'RECEIPT')
                        {{ count($receipts) }}
                    @else
                        {{ count($invoices) }}
                    @endif
                </div>
            </td>

            <!-- Initial Balance -->
            <td style="border: 1px solid #ccc; text-align: center; padding: 5px;">
                <div style="font-size: 12px;">Initial Balance</div>
                <div style="font-weight: bold; font-size: 16px;">
                    {{ number_format($data['initial_balance'], 2) }}
                </div>
            </td>

            <!-- Final Wallet Balance -->
            <td style="border: 1px solid #ccc; text-align: center; padding: 5px;">
                <div style="font-size: 12px;">Final Wallet Balance</div>
                <div style="font-weight: bold; font-size: 16px;">
                    {{ number_format($data['final_wallet_balance'], 2) }}
                </div>
            </td>

            <!-- Total Haulage Fees -->
            <td style="border: 1px solid #ccc; text-align: center; padding: 5px;">
                <div style="font-size: 12px;">Total Haulage Fees</div>
                <div style="font-weight: bold; font-size: 16px;">
                    @if(request('web_page') == 'RECEIPT')
                        {{ number_format(collect($receipts)->sum('haulage_fee'), 2) }}
                    @else
                        0
                    @endif
                </div>
            </td>

            <!-- Total Scale Fees -->
            <td style="border: 1px solid #ccc; text-align: center; padding: 5px;">
                <div style="font-size: 12px;">Total Scale Fees</div>
                <div style="font-weight: bold; font-size: 16px;">
                    @if(request('web_page') == 'RECEIPT')
                        {{ number_format(collect($receipts)->sum('less_scale_fee'), 2) }}
                    @else
                        {{ number_format(collect($invoices)->sum('less_scale_fee'), 2) }}
                    @endif
                </div>
            </td>

            <!-- Total Due Amount -->
            <td style="border: 1px solid #ccc; text-align: center; padding: 5px;">
                <div style="font-size: 12px;">Total Due Amount</div>
                <div style="font-weight: bold; font-size: 16px;">
                    @if(request('web_page') == 'RECEIPT')
                        {{ number_format(collect($receipts)->sum('paid_amount'), 2) }}
                    @else
                        {{ number_format(collect($invoices)->sum('paid_amount'), 2) }}
                    @endif
                </div>
            </td>
        </tr>
    </table>


    <!-- Product Table -->
    <table width="100%">
        <thead>
        <tr>
            <th style="width: 5%;">SNo.</th>
            <th style="width: 10%;">Invoice Number</th>
            <th style="width: 10%;">Date</th>
            <th style="width: 10%;">customer</th>
            <th style="width: 10%;">Cashier</th>
            <th style="width: 30%;">Products (Quantity)</th>
            <th style="width: 10%;">Total Amount</th>
            <th style="width: 10%;">Due</th>
            <td style="width: 5%;">Status</td>
        </tr>
        </thead>
        <tbody>

        @foreach ($invoices as $key => $invoice)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $invoice->invoice->invoice_number }}</td>
                <td>{{ $invoice->created_at->format('m-d-Y')  }}
                </td>
                <td>{{$invoice->customer->name}}</td>
                <td>{{$invoice->cashier->name}}</td>
                <td>
                    @foreach($invoice->items as $key =>  $item)
                        {{$item->product->name}}({{$item->quantity}})
                        {{count($invoice->items) > $key +1 ? ', ': '' }}
                    @endforeach
                </td>
                <td>{{number_format($invoice->total_amount, 2)}}
                    {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}
                </td>
                <td>{{number_format($invoice->paid_amount, 2)}}
                    {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}
                </td>
                <td>{{$invoice->status}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <!-- Signature -->
    <div class="signature">
        {{--        <strong>Authorized Signature</strong><br>--}}
        {{--        @if (!empty($order->invoice->digital_signature))--}}
        {{--            <img src="{{ public_path($order->invoice->digital_signature) }}" alt="Signature">--}}
        {{--        @else--}}
        {{--            <span>No signature available</span>--}}
        {{--        @endif--}}
        {{--    </div>--}}

        <!-- Footer -->
        {{--        <div class="footer">--}}
        {{--            THANK YOU FOR YOUR BUSINESS!<br>--}}
        {{--            Please visit again.--}}
        {{--        </div>--}}
    </div>
</div>
</body>

</html>
