<x-head title="Receipt"/>

<x-app-layout>
    <style>
        body {
            padding: 20px 0;
        }

        .card {
            width: 100%;
            margin: 0 auto;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
            padding: 2.25rem
        }

        .text-right {
            text-align: right
        }

        /* ========== Header Section ========== */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .invoice-header .logo,
        .invoice-header .company-info {
            display: table-cell;
            vertical-align: top;
        }

        .invoice-header .logo img {
            max-width: 130px;
            height: auto;
        }

        .invoice-header .company-info {
            text-align: right;
        }

        .invoice-header h2 {
            margin: 0;
            color: #14727b;
            font-size: 20px;
            font-weight: 700;
        }

        .invoice-header p {
            margin: 2px 0;
            font-size: 13px;
        }

        /* ========== Seller & Transaction Details ========== */
        .receipt-details {
            width: 100%;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .receipt-details .left,
        .receipt-details .right {
            float: left;
            width: 48%;
        }

        .receipt-details .right {
            float: right;
            text-align: right;
        }

        .receipt-details strong {
            color: #000;
            font-size: 15px;
        }

        .receipt-details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 13px;
        }

        .receipt-details td {
            padding: 4px;
            text-align: right;
        }

        /* ========== Products Table ========== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }

        table, th, td {
            border: 1px solid #14727b;
        }

        th {
            background: #14727b25;
            font-weight: 600;
            color: #000;
            text-align: center;
            padding: 8px;
        }

        td {
            padding: 8px;
            text-align: center;
            color: #000;
        }

        .total-row {
            font-weight: bold;
            background-color: #f6f6f6;
        }

        /* ========== Buttons and Signature ========== */
        .button-group {
            text-align: right;
            margin-top: 15px;
        }

        .button-group button {
            background: #14727b;
            border: none;
            color: #fff;
            padding: 7px 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 6px;
        }

        .button-group button:hover {
            background: #0d555e;
        }

        .btn-danger {
            background: #b10000;
        }

        .signature-area {
            text-align: right;
            margin-top: 20px;
        }

        canvas {
            border: 1px solid #999;
            border-radius: 4px;
        }

        /* ========== Footer ========== */
        .footer {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #555;
            border-top: 1px solid #ccc;
            margin-top: 20px;
            padding-top: 10px;
        }

        /* ========== Responsive Adjustments ========== */
        @media (max-width: 768px) {
            .invoice-header {
                margin-bottom: 0
            }

            .text-right {
                text-align: left
            }

            .card {
                padding: 20px
            }

            .invoice-header,
            .receipt-details {
                display: block;
            }

            .invoice-header .company-info,
            .receipt-details .right {
                text-align: left;
            }

            .invoice-header .logo,
            .invoice-header .company-info,
            .receipt-details .left,
            .receipt-details .right {
                display: block;
                width: 100%;
                float: none;
            }

            .invoice-header img {
                display: block;
                margin: 0 auto 10px auto;
            }

            th, td {
                font-size: 12px;
                padding: 2px;
            }

            .button-group {
                text-align: center;
            }

            #draw-signature canvas {
                width: 100%
            }
        }

        /* ========== Print (A4 perfect) ========== */
        @media print {
            html, body {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 auto !important;
                background: #fff !important;
            }

            .card {
                width: 100% !important;
                max-width: 190mm !important;
                margin: 0 auto !important;
                box-shadow: none;
                border: none;
                padding: 10mm !important;
            }

            .button-group,
            .form-check,
            #clear,
            #print,
            .alert {
                display: none !important;
            }

            th, td {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 12px;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }

        }
    </style>


    <div class="page-content">
        <div class="container-fluid">
            <!-- Success/Error Messages -->
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
                <!-- Invoice Header -->
                <div class="invoice-header">
                    <div class="logo">
                        <img src="{{ App\Models\Setting::where('key', 'company_logo')->first()->value }}"
                             alt="Logo">
                    </div>
                    <div class="text-right">
                        <h2>{{ App\Models\Setting::where('key', 'company_name')->first()->value }}</h2>
                        <p>{{ App\Models\Setting::where('key', 'company_address')->first()->value }}<br>
                            Tel: {{ App\Models\Setting::where('key', 'phone_number')->first()->value }}<br>
                            {{ App\Models\Setting::where('key', 'website_email')->first()->value }}</p>
                        </p>
                    </div>

                </div>

                <p style="margin-bottom:0"><strong style="color:#14727b;">Purchase Receipt #:</strong><strong
                            style="font-size: 20px;"> {{ $order->invoices->first()->invoice_number ?? '-' }}</strong>
                </p>
                <p style="margin-top:0"><strong style="color:#14727b;">Date:</strong>
                    {{ $order->created_at->format('m-d-Y') }}
                </p>

                <!-- Seller and Transaction Details at Top -->
                <div class="receipt-details">
                    <div class="left">
                        <strong>Seller Details</strong><br>
                        {{ $order->supplier->name ?? 'N/A' }}<br>
                        {{ $order->supplier->street_address ?? 'N/A' }}<br>
                    </div>
                    <div class="right">
                        <strong>Transaction Details</strong>
                        <div class="table-responsive">
                            <table style="border: 0; margin-top:5px;" border="0">
                                <tbody>
                                <tr style="line-height:20px;">
                                    <td style="text-align:left;padding:4px;text-align: right;">Transaction Number:</td>
                                    <td style="text-align:right; padding:4px;font-weight:bold">
                                        {{ $order->invoices->first()->invoice_number ?? '-' }}
                                    </td>
                                </tr>
                                <tr style="line-height:20px;">
                                    <td style="text-align:left;padding:4px;text-align: right; background-color:#fff">TRN
                                        Number:
                                    </td>
                                    <td style="text-align:right; padding:4px; background-color:#fff;font-weight:bold">
                                        {{$order->supplier->tax}}
                                    </td>
                                </tr>
                                <tr style="line-height:20px;">
                                    <td style="text-align:left;padding:4px;text-align: right;">Transaction Type:</td>
                                    <td style="text-align:right; padding:4px; margin-bottom:2px;font-weight:bold">
                                        {{ $order->payment_method ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:left;padding:4px;text-align: right; background-color:#fff">
                                        Cashier:
                                    </td>
                                    <td style="text-align:right; padding:4px; background-color:#fff;font-weight:bold">
                                        {{ $order->cashier->name ?? '-' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responive">
                    <table>
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Products</th>
                            <th>Qty</th>
                            <th>Weight Unit</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->weightUnit->name ?? '' }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}
                                    {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                </td>
                                <td>{{ number_format($item->total_amount, 2) }}
                                    {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-right total-row">Total Settlement</td>
                            <td> {{ number_format($order->total_amount + $order->less_scale_fee +$order->haulage_fee , 2) }}
                                {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right total-row">Less Scale Fee</td>
                            <td>{{ number_format($order->less_scale_fee, 2) }}
                                {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right total-row">Haulage Fee</td>
                            <td>{{ number_format($order->haulage_fee, 2) }}
                                {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="text-right total-row">Handling Fee</td>
                            <td>{{ number_format($order->handling_fee, 2) }}
                                {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="text-right total-row">Paid Amount</td>
                            <td>{{ number_format($order->total_amount - $order->paid_amount, 2) }}
                                {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right total-row">Due Amount</td>
                            <td>
                                {{--                                {{ number_format($order->invoices->first()->balance_amount, 2) }}--}}
                                {{ number_format($order->paid_amount, 2) }}
                                {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-end py-3">

                        @can('complete_receipt_signature')
                            @if ($order->status == 'Pending')
                                <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST">
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
                            <div class="mb-3">
                                <img src="{{ asset($order->invoice->digital_signature) }}" alt="Signature"
                                     style="max-height:100px;">
                            </div>
                            <p>Authorized Signature.</p>
                        @endif

                    </div>
                </div>
                <div style="border-top:1px solid #3333331f; margin-top:10px; width:100%;"></div>

                <div class="footer">
                    THANK YOU FOR YOUR BUSINESS!<br>
                    All transactions are final
                </div>
            </div>
        </div>

        @if ($order->status == 'Completed')
            <button class="btn btn-primary float-end w-25" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        @endif

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sweet-status-btn').forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const url = this.getAttribute('href');
                    const action = this.getAttribute('data-action');

                    let title, text, icon, confirmText;

                    if (action === 'complete') {
                        title = 'Complete Order?';
                        text = 'Are you sure you want to mark this order as completed?';
                        icon = 'success';
                        confirmText = 'Yes, complete it!';
                    } else if (action === 'void') {
                        title = 'Void Order?';
                        text = 'This will cancel the order. Are you sure you want to continue?';
                        icon = 'warning';
                        confirmText = 'Yes, void it!';
                    }

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: confirmText
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        });
    </script>

    <!-- Scripts -->
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
