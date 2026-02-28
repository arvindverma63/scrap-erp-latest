<x-head title="Dashboard"/>


<x-app-layout>
    <!-- Page Content-->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Bootstrap Alert -->
            @if (session('success') || session('error'))
                <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show"
                     role="alert">
                    {{ session('success') ?? session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                        <h4 class="page-title">Scrap ERP Dashboards</h4>
                        <div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">ScrapERP</a>
                                </li><!--end nav-item-->
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div><!--end page-title-box-->
                </div><!--end col-->
            </div><!--end row-->

            <div class="row">

                <div class="col-lg-12">
                    <div class="row justify-content-center">

                        @canany(['wallet_approve', 'wallet_deposit', 'cash_count'])
                            <div class="col-md-3 mb-3">
                                <a href="{{route('admin.wallets.index')}}">
                                    <div class="card bg-corner-img h-100 cursor-pointer">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13"> Account
                                                        Balance</p>
                                                    <h4 class="mt-1 mb-0 fw-bold">{{ number_format(App\Models\Wallet::where('date', date(\Carbon\Carbon::now()->toDateString()))?->first()?->balance, 2) }}</h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                                        <i class="fas fa-wallet fs-20 text-primary"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                    </div>
                                </a>

                                @include('layouts.common.topup-modal')
                            </div>
                        @endcanany

                        @if(auth()->user()->roles->first()->name == 'super-admin' || 
                        auth()->user()->roles->first()->name == 'admin')
                            <div class="col-md-3 mb-3">
                                <a href="{{route('admin.wallets.index')}}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13"> Pending
                                                        Topup
                                                        Request</p>
                                                    <h4 class="mt-1 mb-0 fw-bold">{{$data['pending_topup']}}</h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                                        <i class="fas fa-clock fs-20 text-primary"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                            </div>
                        @endif

                        @canany(['products_create', 'products_read', 'products_update'])
                            <div class="col-md-3  mb-3">
                                <a href="{{route('admin.products.index')}}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Type of
                                                        Scrap</p>
                                                    <h4 class="mt-1 mb-0 fw-bold">{{ $data['scrap_type'] }}</h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                                        <i
                                                                class="fas fa-user-tag fs-22 align-self-center mb-0 text-primary"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                                <!--end card-->
                            </div>
                        @endcanany

                        @canany(['buying_create', 'buying_read', 'buying_update'])
                            <div class="col-md-3  mb-3">
                                <a href="{{route('admin.buyers.index')}}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Buyers</p>
                                                    <h4 class="mt-1 mb-0 fw-bold">{{$data['buyers']}}</h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                                        <i
                                                                class="fas fa-user-tie fs-22 align-self-center mb-0 text-primary"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                                <!--end card-->
                            </div>
                        @endcanany
                        <!--end col-->
                        @canany(['audit_read'])
                            <div class="col-md-3 mb-3">
                                <a href="{{route('admin.audit.ledger', ['type' => 'Purchase'])}}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13">
                                                        Ledger Reports</p>
                                                    <h4 class="mt-1 mb-0 fw-bold">{{$data['ledger_count']}}</h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-info rounded mx-auto">
                                                        <i class="fas fa-building fs-22 align-self-center mb-0 text-info"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                                <!--end card-->
                            </div>
                        @endcanany
                        <!--end col-->
                        @canany(['invoices_create','invoices_read', 'invoices_update', ''])
                            <div class="col-md-3 mb-3">
                                <a href="{{route('admin.sales.pendingReport')}}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Pending
                                                        Invoices
                                                    </p>
                                                    <h4 class="mt-1 mb-0 fw-bold">
                                                        {{$data['pending_invoice']}}
                                                    </h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-warning rounded mx-auto">
                                                        <i
                                                                class="fas fa-money-check-alt fs-22 align-self-center mb-0 text-warning"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                                <!--end card-->
                            </div>
                        @endcanany
                        <!--end col-->
                        @if(auth()->user()->roles->first()->name == 'super-admin' || 
                        auth()->user()->roles->first()->name == 'admin')
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('admin.supplier.index') }}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Sellers
                                                    </p>
                                                    <h4 class="mt-1 mb-0 fw-bold">{{ $data['suppliers'] }}</h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-warning rounded mx-auto">
                                                        <i
                                                                class="fas fa-file-invoice fs-22 align-self-center mb-0 text-warning"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                                <!--end card-->
                            </div>
                        @endif
                        <!--end col-->

                        @if(auth()->user()->roles->first()->name == 'super-admin' || 
                        auth()->user()->roles->first()->name == 'admin')
                            <div class="col-md-3 mb-3">
                                <a href="{{route('admin.reports', ['web_page' => 'RECEIPT','from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')])}}">
                                    <div class="card bg-corner-img h-100">
                                        <div class="card-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Today's
                                                        Report Invoice/Receipt
                                                    </p>
                                                    <h4 class="mt-1 mb-0 fw-bold">
                                                        {{$data['today_invoices']}}/{{ $data['today_receipts'] }}
                                                    </h4>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3 align-self-center">
                                                    <div
                                                            class="d-flex justify-content-center align-items-center thumb-md border-dashed border-danger rounded mx-auto">
                                                        <i class="fas fa-receipt fs-22 align-self-center mb-0 text-danger"></i>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </a>
                                <!--end card-->
                            </div><!--end col-->
                        @endif


                    </div>
                    <!--end row-->
                </div><!--end col-->
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Year wise receipts and invoice generated</h4>
                                </div><!--end col-->
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <!-- <a href="#"
                                            class="btn bt btn-light dropdown-toggle dropdown-toggle-button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icofont-calendar fs-5 me-1"></i> This Month<i
                                                class="las la-angle-down ms-1"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item dropdown-item-graph" href="#"
                                                data-filter="today">Today</a>
                                            <a class="dropdown-item dropdown-item-graph" href="#"
                                                data-filter="last_week">Last
                                                Week</a>
                                            <a class="dropdown-item dropdown-item-graph" href="#"
                                                data-filter="last_month">Last
                                                Month</a>
                                            <a class="dropdown-item dropdown-item-graph" href="#"
                                                data-filter="year">This Year</a>
                                        </div> -->

                                    </div>
                                </div><!--end col-->
                            </div> <!--end row-->
                        </div><!--end card-header-->
                        <div class="card-body pt-0">
                            <div id="reports" class="apex-charts pill-bar"></div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->

                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Low Stock Scrap</h4>
                                </div><!--end col-->
                            </div> <!--end row-->
                        </div><!--end card-header-->
                        <a href="{{ route('admin.stock-alert.index') }}">
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tbody>
                                        @foreach ($lowToHigh as $low)
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('assets/images/metal/Steel.jpg') }}"
                                                             class="me-2 align-self-center thumb-sm rounded-circle"
                                                             alt="...">
                                                        <h6 class="m-0 text-truncate">
                                                            {{ $low['product_name'] ?? '-' }}</h6>
                                                    </div>
                                                </td>
                                                <td class="px-0 text-end">
                                                        <span
                                                                class="text-body ps-2 align-self-center text-end fw-medium">
                                                            {{ $low['total_quantity'] ?? 0 }}
                                                            {{ $low['weight_unit'] ?? '-' }}
                                                        </span>
                                                </td>
                                            </tr>
                                        @endforeach


                                        </tbody>
                                    </table> <!--end table-->
                                </div><!--end /div-->
                                <hr class="hr-dashed">

                            </div><!--end card-body-->
                        </a>
                    </div><!--end card-->

                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">High Stock Scrap</h4>
                                </div><!--end col-->
                            </div> <!--end row-->
                        </div><!--end card-header-->
                        <a href="{{ route('admin.stock-alert.index') }}">
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tbody>
                                        @foreach ($highToLow as $high)
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('assets/images/metal/Steel.jpg') }}"
                                                             class="me-2 align-self-center thumb-sm rounded-circle"
                                                             alt="...">
                                                        <h6 class="m-0 text-truncate">
                                                            {{ $high['product_name'] ?? '-' }}
                                                        </h6>
                                                    </div>
                                                </td>
                                                <td class="px-0 text-end">
                                                        <span
                                                                class="text-body ps-2 align-self-center text-end fw-medium">
                                                            {{ $high['total_quantity'] ?? 0 }}
                                                            {{ $high['weight_unit'] ?? '-' }}
                                                        </span>
                                                </td>
                                            </tr>
                                        @endforeach


                                        </tbody>
                                    </table> <!--end table-->
                                </div><!--end /div-->
                                <hr class="hr-dashed">

                            </div><!--end card-body-->
                        </a>
                    </div><!--end card-->
                </div> <!--end col-->
            </div>

        </div><!-- container -->
        <!--Start Rightbar-->
        <!--Start Rightbar/offcanvas-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="Appearance" aria-labelledby="AppearanceLabel">
            <div class="offcanvas-header border-bottom justify-content-between">
                <h5 class="m-0 font-14" id="AppearanceLabel">Appearance</h5>
                <button type="button" class="btn-close text-reset p-0 m-0 align-self-center"
                        data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <h6>Account Settings</h6>
                <div class="p-2 text-start mt-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="settings-switch1">
                        <label class="form-check-label" for="settings-switch1">Auto updates</label>
                    </div><!--end form-switch-->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="settings-switch2" checked>
                        <label class="form-check-label" for="settings-switch2">Location Permission</label>
                    </div><!--end form-switch-->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="settings-switch3">
                        <label class="form-check-label" for="settings-switch3">Show offline Contacts</label>
                    </div><!--end form-switch-->
                </div><!--end /div-->
                <h6>General Settings</h6>
                <div class="p-2 text-start mt-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="settings-switch4">
                        <label class="form-check-label" for="settings-switch4">Show me Online</label>
                    </div><!--end form-switch-->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="settings-switch5" checked>
                        <label class="form-check-label" for="settings-switch5">Status visible to all</label>
                    </div><!--end form-switch-->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="settings-switch6">
                        <label class="form-check-label" for="settings-switch6">Notifications Popup</label>
                    </div><!--end form-switch-->
                </div><!--end /div-->
            </div><!--end offcanvas-body-->
        </div>
        <!--end Rightbar/offcanvas-->
        <!--end Rightbar-->
        <!--Start Footer-->
        <footer class="footer text-center text-sm-start d-print-none">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-0 rounded-bottom-0">
                            <div class="card-body">
                                <p class="text-muted mb-0">
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script>
                                    ScrapERP
                                    <span class="text-muted d-none d-sm-inline-block float-end">
                                        Design with
                                        <i class="fa fa-heart text-danger align-middle"></i>
                                        by Invoidea </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--end footer-->
    </div>

    {{--
    <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script> --}}

    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pages/index.init.js')}}"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rawData = @json($chartData);

            const data = {
                labels: [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ],
                invoices: rawData.map(item => item.invoice), // invoice count
                receipts: rawData.map(item => item.receipt)      // receipt count
            };

            const options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {show: false}
                },
                colors: ['#435663', '#8FABD4'],
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        borderRadius: 6
                    }
                },
                series: [
                    {name: 'Invoices', data: data.invoices},
                    {name: 'Receipts', data: data.receipts}
                ],
                xaxis: {
                    categories: data.labels,
                    labels: {rotate: -45}
                },
                yaxis: {
                    title: {text: 'Counts'}
                },
                dataLabels: {enabled: false},
                legend: {position: 'top'},
                grid: {borderColor: '#EFECE3'}
            };

            const chartContainer = document.querySelector("#reports");
            const chart = new ApexCharts(chartContainer, options);
            chart.render();

            // const chartContainer = document.querySelector("#reports");
            // const dropdownButton = document.querySelector(".dropdown-toggle-button");
            // let chart;

            // function loadChart(filter = 'month') {
            //     fetch(`{{ route('admin.inventory.reportData') }}?filter=${filter}`)
            //         .then(res => res.json())
            //         .then(data => {
            //         console.log(data);
            //             const options = {
            //                 chart: {
            //                     type: 'bar',
            //                     height: 350,
            //                     toolbar: {
            //                         show: false
            //                     }
            //                 },
            //                 colors: ['#00464d', '#ccdadb'],
            //                 plotOptions: {
            //                     bar: {
            //                         columnWidth: '45%',
            //                         borderRadius: 6
            //                     }
            //                 },
            //                 series: [{
            //                         name: 'Purchase',
            //                         data: data.purchases
            //                     },
            //                     {
            //                         name: 'Sale',
            //                         data: data.sales
            //                     }
            //                 ],
            //                 xaxis: {
            //                     categories: data.labels,
            //                     labels: {
            //                         rotate: -45
            //                     }
            //                 },
            //                 yaxis: {
            //                     title: {
            //                         text: 'Quantity'
            //                     }
            //                 },
            //                 dataLabels: {
            //                     enabled: false
            //                 },
            //                 legend: {
            //                     position: 'top'
            //                 },
            //                 grid: {
            //                     borderColor: '#f1f3fa'
            //                 },
            //             };

            //             if (chart) chart.destroy();
            //             chart = new ApexCharts(chartContainer, options);
            //             chart.render();
            //         });
            // }
            // // Default load
            // loadChart('month');

            // Handle dropdown clicks
            // document.querySelectorAll('.dropdown-item-graph').forEach(item => {
            //     item.addEventListener('click', e => {
            //         e.preventDefault();
            //         const filter = e.target.getAttribute('data-filter');
            //         const text = e.target.textContent.trim();

            //         // update button label
            //         dropdownButton.innerHTML =
            //             `<i class="icofont-calendar fs-5 me-1"></i> ${text} <i class="las la-angle-down ms-1"></i>`;

            //         // reload chart
            //         loadChart(filter);
            //     });
            // });
        });
    </script>


    <!-- end page content -->
</x-app-layout>
