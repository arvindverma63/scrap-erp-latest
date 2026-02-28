<x-head title="Invoice"/>
<x-app-layout>
    <style>
        body {
            padding: 20px 0;
        }

        .card {
            padding: 2.25rem
        }

        @page {
            body {
            box-shadow: none;
            padding: 0;
            margin: 0;
            border: 0;
        }
        }

        strong {
            color: #000;
            font-size: 14px;
        }

        .invoice-box {
            font-size: 14px;
            background: #fff;
            height: 100vh;
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
            font-weight: 600
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

        .info .right table {
            width: 45%;
            border: 0;
            margin-top: 5px;
            margin-left: auto;

        }

        .info .right table th,
        .info .right table td {
            background: transparent;
            border: 0
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th,
        td {
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
            /*margin-top: 50px;*/
            font-size: 12px;
            color: #666;
            /*clear: both;*/

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
            width: 150px;
            object-fit: contain;
        }

        .left table th,
        .left table td {
            border: 0;
            background: transparent;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 12px;
                padding: 2px;
            }

            .card {
                padding: 20px
            }

            .totals {
                width: 100%;
            }
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">

           @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="invoice-box" style="height: auto !important;">
                    <div class="header">
                        <div class="left">
                            <span class="logo"><img
                                        src="{{App\Models\Setting::where('key', 'company_logo')->first()->value}}"
                                        alt="Logo"></span>
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
                                    <th style="text-align:left;padding:4px;text-align: right">Date :</th>
                                    <td style="text-align:right; padding:4px;font-weight:bold">
                                        {{ $order->created_at->format('m-d-Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th style="text-align:left;padding:4px;text-align: right;background:#fff">Invoice
                                        No.:
                                    </th>
                                    <td style="text-align:left;padding:4px;text-align: right;background:#fff;font-weight:bold">
                                        {{ $order->invoices->first()->invoice_number ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th style="text-align:left;padding:4px;text-align: right">TRN :</th>
                                    <td style="text-align:left;padding:4px;text-align: right;font-weight:bold;">
                                        {{ $order->customer->tax ?? '-' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <table>
                        <thead>
                        <tr>
                            <th>Item</th>
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
                                    {{App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value}}
                                </td>
                                <td>{{ number_format($item->total_amount, 2) }}
                                    {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="totals">
                        <table>
                            <tr>
                                <td>Subtotal:</td>
                                <td>{{ number_format($order->total_amount, 2) }}
                                    {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                </td>
                            </tr>
                            <tr>
                                <td>Less Scale Fee:</td>
                                <td>{{ number_format($order->less_scale_fee, 2) }}
                                    {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                </td>
                            </tr>
                            <tr>
                                <td>Paid Amount:</td>
                                <td>{{ number_format($order->total_amount - $order->paid_amount, 2) }}
                                    {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Due Amount:</strong></td>
                                <td><strong>{{ number_format($order->paid_amount, 2) }}
                                        {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}</strong>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="row container-fluid">
                        <div class="col-lg-12 text-end py-3">

                            @can('complete_invoice_signature')
                                @if ($order->status == 'Pending')
                                    <form action="{{ route('admin.invoice.updateStatus', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" id="statusInput">
                                        <input type="hidden" name="signature_data" id="signature_data">

                                        <!-- Signature Toggle -->
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="signature_option"
                                                       value="draw" checked>
                                                <label class="form-check-label">Draw Signature</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="signature_option"
                                                       value="upload">
                                                <label class="form-check-label">Use Pre-uploaded Signature</label>
                                            </div>
                                        </div>

                                        <!-- Canvas -->
                                        <div id="draw-signature" class="mb-lg-2 mb-1">
                                            <canvas id="signature-pad" width="400" height="200" class="border"
                                                    style="border-color:#5555554d !important;border-radius: 4px;"></canvas>
                                            <div class="mt-2">
                                                <button type="button" id="clear" class="btn btn-success me-2">
                                                    <i class="fas fa-eraser"></i> Clear
                                                </button>
                                                @if($order->status == 'Completed')
                                                    <button type="button" id="print" class="btn btn-primary"
                                                            onclick="window.print()">
                                                        <i class="fas fa-print"></i> Print
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Pre-uploaded -->
                                        <div id="upload-signature" class="mb-3" style="display:none;">
                                            <img src="{{ App\Models\Setting::where('key', 'receipt_signature')->first()->value }}"
                                                 alt="Signature" style="max-height:100px;">
                                        </div>

                                        <div class="d-flex gap-2 justify-content-end">
                                            <button type="submit" class="btn btn-danger" onclick="setStatus('Voided')">
                                                <i class="fas fa-ban"></i> Voided
                                            </button>
                                            <button type="submit" class="btn btn-primary"
                                                    onclick="setStatus('Completed')">
                                                <i class="fas fa-check-circle"></i> Complete
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @endcan
                            @if (!empty($order->invoice->digital_signature))
                                <div class="mb-3 h-10">
                                    <img src="{{ asset($order->invoice->digital_signature) }}" alt="Signature"
                                         style="max-height:100px;">
                                </div>
                                <p>Authorized Signature.</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        <p>Please make payments via cheques or wire transfer to CM Recycling Co. Ltd.</p>
                        <p>National Commercial Bank</p>
                        <p>Acct# 361069188 – Portmore Branch</p>
                    </div>
                </div>
            </div>

            @if($order->status == 'Completed')
                <button class="btn btn-primary float-end w-25" onclick="window.print()">
                    <i class="fa fa-print"></i> Print
                </button>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.0/dist/signature_pad.umd.min.js"></script>
    <script>
        const signaturePad = new SignaturePad(document.getElementById('signature-pad'));
        const drawDiv = document.getElementById('draw-signature');
        const uploadDiv = document.getElementById('upload-signature');
        const signatureDataInput = document.getElementById('signature_data');

        document.getElementById('clear').addEventListener('click', () => signaturePad.clear());

        document.querySelectorAll('input[name="signature_option"]').forEach(el => {
            el.addEventListener('change', function () {
                if (this.value === 'draw') {
                    drawDiv.style.display = 'block';
                    uploadDiv.style.display = 'none';
                } else {
                    drawDiv.style.display = 'none';
                    uploadDiv.style.display = 'block';
                }
            });
        });

        function setStatus(status) {
            document.getElementById('statusInput').value = status;

            const selectedOption = document.querySelector('input[name="signature_option"]:checked').value;
            if (selectedOption === 'draw') {
                if (signaturePad.isEmpty()) {
                    alert('Please provide a signature or select pre-uploaded.');
                    event.preventDefault();
                    return false;
                }
                signatureDataInput.value = signaturePad.toDataURL('image/png'); // send base64 to Laravel
            } else {
                // pre-uploaded, no action needed
                signatureDataInput.value = "{{ App\Models\Setting::where('key', 'receipt_signature')->first()->value }}";
            }
        }
    </script>
</x-app-layout>