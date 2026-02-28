<x-app-layout>

    <!-- Page Content-->
    <div class="page-content" style="margin-top:60px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                        <h4 class="page-title">Sales Invoice</h4>
                        <div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Sales</a></li>
                                <li class="breadcrumb-item active">Invoice</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Company Header -->
                        <div class="card-body bg-black rounded-top">
                            <div class="row">
                                <div class="col-4 align-self-center">
                                    <img src="{{asset('assets/images/logo-sm.png')}}" alt="logo-small"
                                        class="logo-sm me-1" height="70">
                                </div>
                                <div class="col-8 text-end align-self-center">
                                    <h5 class="mb-1 fw-semibold text-white">
                                        <span class="text-muted">Invoice:</span> #SCRP2025-1458
                                    </h5>
                                    <h5 class="mb-0 fw-semibold text-white">
                                        <span class="text-muted">Issue Date:</span> 16/09/2025
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Buyer / Shipping Details -->
                        <div class="card-body">
                            <div class="row row-cols-3 d-flex justify-content-md-between">
                                <div class="col-md-3">
                                    <span class="badge rounded text-dark bg-light">Invoice To</span>
                                    <h5 class="my-1 fw-semibold fs-18">Steel Industries</h5>
                                    <p class="text-muted">Contact: +91 98765 43210 | steel@example.com</p>
                                </div>
                                <div class="col-md-3">
                                    <address class="fs-13">
                                        <strong class="fs-14">Billed To :</strong><br>
                                        11/42 Krishna Nagar <br>
                                        Delhi, New Delhi 208007<br>
                                        <abbr title="Phone">P:</abbr> +91 98765 43210
                                    </address>
                                </div>
                                <div class="col-md-3">
                                    <address class="fs-13">
                                        <strong class="fs-14">Shipped To:</strong><br>
                                        Plot No. 54, Industrial Area<br>
                                        Delhi, New Delhi 208012<br>
                                        <abbr title="Phone">P:</abbr> +91 98765 43210
                                    </address>
                                </div>
                            </div>

                            <!-- Invoice Table -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive project-invoice">
                                        <table class="table table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Material Description</th>
                                                    <th>Quantity (Tons)</th>
                                                    <th>Rate / Ton</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <h5 class="mt-0 mb-1 fs-14">Iron Scrap</h5>
                                                        <p class="mb-0 text-muted">Heavy melting scrap, mixed grade</p>
                                                    </td>
                                                    <td>10</td>
                                                    <td>$30,000</td>
                                                    <td>$3,00,000</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h5 class="mt-0 mb-1 fs-14">Aluminium Scrap</h5>
                                                        <p class="mb-0 text-muted">Extruded aluminium profile scrap</p>
                                                    </td>
                                                    <td>5</td>
                                                    <td>$1,50,000</td>
                                                    <td>$7,50,000</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h5 class="mt-0 mb-1 fs-14">Plastic Scrap</h5>
                                                        <p class="mb-0 text-muted">Mixed HDPE / LDPE plastic waste</p>
                                                    </td>
                                                    <td>12</td>
                                                    <td>$15,000</td>
                                                    <td>$1,80,000</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="1" class="border-0"></td>
                                                    <td colspan="2" class="border-0 fs-14 text-dark"><b>Sub Total</b>
                                                    </td>
                                                    <td class="border-0 fs-14 text-dark"><b>$12,30,000</b></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" class="border-0"></th>
                                                    <td colspan="2" class="border-0 fs-14 text-dark"><b>GST (18%)</b>
                                                    </td>
                                                    <td class="border-0 fs-14 text-dark"><b>$2,21,400</b></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="1" class="border-0"></th>
                                                    <td colspan="2" class="border-0 fs-14"><b>Total</b></td>
                                                    <td class="border-0 fs-14"><b>$14,51,400</b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Signature -->
                            <div class="row">
                                <div class="col-lg-6">
                                    <h5 class="mt-4">Terms And Conditions :</h5>
                                    <ul class="ps-3">
                                        <li><small class="fs-12">Payment due within 7 days from receipt of
                                                invoice.</small></li>
                                        <li><small class="fs-12">Payment can be made by NEFT/RTGS/UPI.</small></li>
                                        <li><small class="fs-12">Materials once delivered will not be returned.</small>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-6 align-self-center">
                                    <div class="float-none float-md-end" style="width: 30%;">
                                        <small>Authorized Signatory</small>
                                        <img src="{{asset('assets/images/extra/signature.png')}}" alt=""
                                            class="mt-2 mb-1" height="24">
                                        <p class="border-top">Signature</p>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- Footer -->
                            <div class="row d-flex justify-content-center">
                                <div class="col-lg-12 col-xl-4 ms-auto align-self-center">
                                    <div class="text-center">
                                        <small class="fs-12">Thank you for your business with Scrap
                                            ERP</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-4">
                                    <div class="float-end d-print-none mt-2 mt-md-0">
                                        <a href="javascript:window.print()" class="btn btn-info">Print</a>
                                        <a href="#" class="btn btn-primary">Submit</a>
                                        <a href="#" class="btn btn-danger">Cancel</a>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- card-body end -->
                    </div> <!-- card end -->
                </div>
            </div>
        </div>

    </div>
</x-app-layout>