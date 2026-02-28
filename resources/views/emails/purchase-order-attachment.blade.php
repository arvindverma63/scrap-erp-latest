<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Invoice</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f9fb;font-family:'Segoe UI',Arial,sans-serif;">

<table align="center" cellpadding="0" cellspacing="0" width="700"
       style="background-color:#ffffff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.08);
       overflow:hidden;margin:30px auto;border-collapse:collapse;">

    <!-- Header -->
    <tr>
        <td style="background-color:#e6f7f7;padding:20px;border-bottom:3px solid #00a3a3;">
            <table width="100%">
                <tr>
                    <td align="left">
                        <img src="https://lab5.invoidea.work/scraperp/public/assets/images/cm-logo.png"
                             alt="CMX Recycling Ltd" style="display:block;">
                    </td>

                    <td align="right" style="text-align:right;font-size:13px;color:#333;">
                        <p style="margin:0;font-size:18px;font-weight:700;color:#008080;">{{ App\Models\Setting::where('key', 'company_name')->first()->value }}</p>
                        <p style="margin:3px 0;">{{ App\Models\Setting::where('key', 'company_address')->first()->value }}</p>
                        <p style="margin:3px 0;">Tel: {{ App\Models\Setting::where('key', 'phone_number')->first()->value }}</p>
                        <p style="margin:3px 0;">{{ App\Models\Setting::where('key', 'website_email')->first()->value }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Intro Message -->
    <tr>
        <td style="padding:25px 20px;background-color:#fdfefe;">
            <h2 style="margin:0;color:#008080;font-size:20px;">Invoice Summary</h2>

            <p style="margin-top:10px;color:#444;font-size:15px;line-height:1.6;">
                Hello <strong>{{ $data['supplier']['name'] }}</strong>,<br>
                Thank you for doing business with <strong>CMX Recycling Ltd</strong>.
                Below is your invoice summary for purchase order #{{ $data['invoice']['invoice_number'] }}.
            </p>
        </td>
    </tr>

    <!-- Billing and Invoice Info -->
    <tr>
        <td style="padding:0 20px 20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <!-- Customer Info -->
                    <td valign="top" width="50%">
                        <div style="background-color:#f0fafa;padding:15px;border:1px solid #d9eeee;border-radius:5px;">
                            <p style="margin:0;font-weight:bold;color:#008080;">Bill To</p>

                            <p style="margin:5px 0 0;color:#333;">{{ $data['supplier']['name'] }}</p>

                            @if($data['supplier']['phone'])
                                <p style="margin:0;color:#333;">{{ $data['supplier']['country_code'] }} {{ $data['supplier']['phone'] }}</p>
                            @endif

                            @if($data['supplier']['street_address'])
                                <p style="margin:0;color:#333;">{{ $data['supplier']['street_address'] }}</p>
                            @endif
                        </div>
                    </td>

                    <!-- Invoice Info -->
                    <td valign="top" align="right" width="50%">
                        <div style="background-color:#f0fafa;padding:15px;border:1px solid #d9eeee;border-radius:5px;">
                            <p style="margin:0;"><strong>Date:</strong> {{ $data['invoice']['invoice_date'] }}</p>
                            <p style="margin:3px 0;"><strong>Invoice No:</strong> {{ $data['invoice']['invoice_number'] }}</p>
                            <p style="margin:0;"><strong>Due Date:</strong> {{ $data['invoice']['due_date'] }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Product Table -->
    <tr>
        <td style="padding:10px 20px 0;">
            <table width="100%" cellpadding="6" cellspacing="0"
                   style="border-collapse:collapse;border:1px solid #008080;
                   font-size:14px;border-radius:6px;overflow:hidden;">

                <thead>
                <tr style="background-color:#008080;color:#fff;">
                    <th align="center" style="padding:10px;">Sr.No.</th>
                    <th align="left" style="padding:10px;">Product</th>
                    <th align="center" style="padding:10px;">Qty</th>
                    <th align="center" style="padding:10px;">Unit</th>
                    <th align="center" style="padding:10px;">Unit Price</th>
                    <th align="center" style="padding:10px;">Total</th>
                </tr>
                </thead>

                <tbody>
                @foreach($data['order_items'] as $index => $item)
                    <tr style="background-color: {{ $index % 2 == 0 ? '#f9ffff' : '#f4fcfc' }};">
                        <td align="center" style="border-top:1px solid #cceaea;padding:8px;">
                            {{ $index + 1 }}
                        </td>

                        <td style="border-top:1px solid #cceaea;padding:8px;">
                            Product #{{ $item['product_id'] }}
                        </td>

                        <td align="center" style="border-top:1px solid #cceaea;padding:8px;">
                            {{ $item['quantity'] }}
                        </td>

                        <td align="center" style="border-top:1px solid #cceaea;padding:8px;">
                            {{ $item['weight_unit_id'] }}
                        </td>

                        <td align="center" style="border-top:1px solid #cceaea;padding:8px;">
                            {{ number_format($item['unit_price'], 2) }} JMD
                        </td>

                        <td align="center" style="border-top:1px solid #cceaea;padding:8px;">
                            {{ number_format($item['total_amount'], 2) }} JMD
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </td>
    </tr>

    <!-- Totals -->
    <tr>
        <td style="padding:20px;">
            <table align="right" width="300" cellpadding="6" cellspacing="0"
                   style="border-collapse:collapse;border-radius:6px;overflow:hidden;
                   background-color:#eafdfd;font-size:15px;">

                <tr>
                    <td style="padding:8px;border-bottom:1px solid #d9eeee;">Subtotal:</td>
                    <td align="right" style="padding:8px;border-bottom:1px solid #d9eeee;">
                        {{ number_format($data['invoice']['sub_total'], 2) }} JMD
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px;border-bottom:1px solid #d9eeee;">Paid Amount:</td>
                    <td align="right" style="padding:8px;border-bottom:1px solid #d9eeee;">
                        {{ number_format($data['paid_amount'], 2) }} JMD
                    </td>
                </tr>

                <tr>
                    <td style="padding:10px;font-weight:bold;background-color:#008080;color:#fff;">Balance:</td>
                    <td align="right" style="padding:10px;font-weight:bold;background-color:#008080;color:#fff;">
                        {{ number_format($data['invoice']['balance_amount'], 2) }} JMD
                    </td>
                </tr>

            </table>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background-color:#f0fafa;padding:15px;text-align:center;
                   font-size:13px;color:#555;border-top:1px solid #d9eeee;">
            © 2025 CMX Recycling Ltd — All Rights Reserved<br>
            <a href="mailto:cn@example.com" style="color:#008080;text-decoration:none;">cn@example.com</a> |
            <a href="tel:+1345345435345" style="color:#008080;text-decoration:none;">+1-345-345-435-345</a>
        </td>
    </tr>

</table>

</body>
</html>
