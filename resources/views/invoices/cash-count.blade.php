<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cash count Receipt</title>
    <style>
        body {
            width: 2.8in;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
        }

        .container {
            width: 2.8in;
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
                <td><strong style="color:#14727b;"></strong></td>
                <td class="text-right"><strong></strong></td>
            </tr>
            <tr>
                <td><strong style="color:#14727b;">Date:</strong></td>
                <td class="text-right">{{ $wallet->created_at->format('m-d-Y') }}</td>
            </tr>
            <tr>
                <td><strong style="color:#14727b;">Time:</strong></td>
                <td class="text-right">{{ $cashCounts[0]->created_at->format('h:i:s a') }}</td>
            </tr>
            <tr>
                <td><strong style="color:#14727b;">Cashier:</strong></td>
                <td class="text-right">{{ $wallet->cashier->name }}</td>
            </tr>
        </table>
    </div>

    <!-- Seller and Transaction Details -->
    <table class="details" width="100%">
        <tr>
            <td colspan="2" style="text-align: center;"><strong>Wallet Detail</strong></td>
        </tr>
        <tr>
            <td width="50%">
                <strong>Initial balance</strong><br>
                {{ $wallet->initial_balance }}<br>
            </td>
            <td width="50%">
                <strong>Remaining balance</strong><br>
                {{$wallet->balance}}<br>
            </td>
        </tr>
    </table>

    <!-- Product Table -->
    <table width="100%">
        <thead>
        <tr>
            <th>Sr</th>
            <th>Currency</th>
            <th>Count</th>
            <th class="text-right">Amount</th>
        </tr>
        </thead>

        <tbody>
        @php
            $total = 0;
        @endphp

        @foreach ($cashCounts as $index => $cash)
            @php
                $rowAmount = $cash->currency * $cash->count;
                $total += $rowAmount;
            @endphp

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ number_format($cash->currency) }}</td>
                <td>{{ $cash->count }}</td>
                <td class="text-right">{{ number_format($rowAmount) }}</td>
            </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="3" class="text-right"><strong>Total Cash:</strong></td>
            <td class="text-right"><strong>{{ number_format($total) }}</strong></td>
        </tr>
        <tfoot>
        <tr>
            <td colspan="4">Remarks : {{$cashCounts[0]->remarks ?? 'N/A'}}</td>
        </tr>
        </tfoot>
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
