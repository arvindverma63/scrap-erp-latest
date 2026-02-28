<x-head title="ScrapERP | Reports"/>
<x-app-layout>

    <div class="page-content">
        <div class=" container-fluid">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h5 class="card-title mb-0">Purchase/Invoice Reports</h5>
            </div>

            <div class="row g-1 mb-2">
                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">
                                @if(request('web_page') == 'RECEIPT')
                                    {{'Total Receipts'}}
                                @else
                                    {{'Total Invoices'}}
                                @endif
                            </span>
                            <br>
                            <span class="fw-bold fs-4">
                                @if(request('web_page') == 'RECEIPT')
                                    {{count($receipts)}}
                                @else
                                    {{count($invoices)}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">{{'Initial Balance'}}</span>
                            <br>
                            <span class="fw-bold fs-4">{{number_format($data['initial_balance'], 2)}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">Final Wallet Balance</span>
                            <br>
                            <span class="fw-bold fs-4">{{number_format($data['final_wallet_balance'],2)}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">Total Haulage Fees</span>
                            <br>
                            <span class="fw-bold fs-4">
                                 @if(request('web_page') == 'RECEIPT')
                                    {{number_format(collect($receipts)->sum('haulage_fee'), 2)}}
                                @else
                                    {{'0'}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">Total Handling Fees</span>
                            <br>
                            <span class="fw-bold fs-4">
                                 @if(request('web_page') == 'RECEIPT')
                                    {{number_format(collect($receipts)->sum('handling_fee'), 2)}}
                                @else
                                    {{'0'}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">Total Scale Fees</span>
                            <br>
                            <span class="fw-bold fs-4">
                                 @if(request('web_page') == 'RECEIPT')
                                    {{number_format(collect($receipts)->sum('less_scale_fee'), 2)}}
                                @else
                                    {{number_format(collect($invoices)->sum('less_scale_fee'), 2)}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-center shadow-sm" style="border-radius: 8px;margin: 0;padding: 0;">
                        <div class="card-body" style="border-radius: 8px;margin: 0;padding: 0;">
                            <span class="card-title">Total Due Amount</span>
                            <br>
                            <span class="fw-bold fs-4">
                                @if(request('web_page') == 'RECEIPT')
                                    {{number_format(collect($receipts)->sum('paid_amount'), 2)}}
                                @else
                                    {{number_format(collect($invoices)->sum('paid_amount'), 2)}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card shadow-sm mb-2">
                <div class="card-body" style="border-radius: 8px;margin: 2;padding: 2;">
                    <form class="row g-3 align-items-end" method="GET"
                          action="{{ route('admin.reports') }}">
                        <input class="d-none" type="text" name="web_page" value="{{ request('web_page') }}">
                        <div class="col-md-3">
                            <label class="form-label small">From Date</label>
                            <input type="date" name="from_date" class="form-control"
                                   value="{{ request('from_date') ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">To Date</label>
                            <input type="date" name="to_date" class="form-control"
                                   value="{{ request('to_date') ?? '' }}">
                        </div>
                         <div class="col-md-3">
                            <label class="form-label small">Cashier</label>
                            <select name="cashier_id" class="form-control">
                                <option value="">All Cashier</option>
                                @foreach($cashiers as $cashier)
                                <option value="{{$cashier->id}}" 
                                {{ (request('cashier_id') == $cashier->id)? 'selected' : ''}}>{{$cashier->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card mb-0">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item bg-light border-1"
                            role="presentation">
                            <a class="nav-link {{ request('web_page') == 'RECEIPT' ? 'active' : '' }}"
                               href="{{route('admin.reports', ['web_page' => 'RECEIPT','from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')])}}"
                               role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-home-account"></i></span>
                                <span class="d-none d-sm-block">Receipts</span>
                            </a>
                        </li>
                        <li class="nav-item bg-light border-1" role="presentation">
                            <a class="nav-link  {{ request('web_page') == 'INVOICE' ? 'active' : '' }}"
                               href="{{route('admin.reports', ['web_page' => 'INVOICE','from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')])}}"
                               role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account-outline"></i></span>
                                <span class="d-none d-sm-block">Invoices</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content text-muted">
                        @if(request('web_page') == 'RECEIPT')
                            <div class="tab-pane {{ request('web_page') == 'RECEIPT' ? 'active show' : 'd-none' }}"
                                 id="navpills2-home" role="tabpanel">

                                <div>
                                    <div class="row">
                                        <h5 class="fw-bold my-2 px-3 col-11 float-start"><i
                                                    class="fas fa-wallet me-2"></i> Receipt Report</h5>
                                        @if ($receipts->count())
                                            <div class="col-1">
                                                <form class="row g-3 align-items-end" method="GET"
                                                      action="{{ route('admin.reports.receipt-download') }}">
                                                    <input class="d-none" type="text" name="web_page"
                                                           value="{{ request('web_page') }}">
                                                    <input class="d-none" type="date" name="from_date"
                                                           class="form-control"
                                                           value="{{ request('from_date') ?? '' }}">
                                                    <input class="d-none" type="date" name="to_date"
                                                           class="form-control"
                                                           value="{{ request('to_date') ?? '' }}">
                                                    <button type="submit" class="btn btn-sm col-1 float-end">
                                                        <i class="fas fa-download fs-20 text-primary"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>


                                    @if ($receipts->count())
                                        <div class="table-responsive">
                                            <table class="table table-striped align-middle">
                                                <thead class="table-light">
                                                <tr>
                                                    <th style="width: 5%;">SNo.</th>
                                                    <th style="width: 10%;">Receipt Number</th>
                                                    <th style="width: 10%;">Date</th>
                                                    <th style="width: 10%;">Supplier</th>
                                                    <th style="width: 10%;">Cashier</th>
                                                    <th style="width: 30%;">Products (Quantity)</th>
                                                    <th style="width: 10%;">Total Amount (JMD)</th>
                                                    <th style="width: 10%;">Due (JMD)</th>
                                                    <th style="width: 5%;">Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($receipts as $key => $receipt)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.receiptPage', $receipt->id)}}"
                                                               class="fw-bold">
                                                                {{ $receipt->invoice->invoice_number }}
                                                            </a>
                                                        </td>
                                                        <td>{{$receipt->created_at->format('m-d-Y')}}</td>
                                                        <td>{{$receipt->supplier->name}}</td>
                                                        <td>{{$receipt->cashier?->name ?? '-'}}</td>
                                                        <td>
                                                            @foreach($receipt->orderItems as $key =>  $item)
                                                                {{$item->product->name}}({{$item->quantity}})
                                                                {{count($receipt->orderItems) > $key +1 ? ', ': '' }}
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            {{ number_format($receipt->total_amount, 2)}}
                                                            {{ App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value }}
                                                        </td>
                                                        <td>
                                                            {{  number_format($receipt->paid_amount, 2)}}
                                                            {{ App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value }}
                                                        </td>
                                                        <td>{{$receipt->status}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No Receipts.</p>
                                    @endif
                                </div>

                            </div>
                        @endif

                        @if(request('web_page') == 'INVOICE')
                            <div class="tab-pane {{ request('web_page') == 'INVOICE' ? 'active show' : 'd-none' }}"
                                 id="navpills2-profile" role="tabpanel">
                                <div>
                                    <div class="row">
                                        <h5 class="fw-bold my-2 col-11 px-3"><i class="fas fa-file-invoice me-2"></i>
                                            Invoice Report</h5>
                                        @if ($invoices->count())
                                            <div class="col-1">
                                                <form class="row g-3 align-items-end" method="GET"
                                                      action="{{ route('admin.reports.invoice-download') }}">
                                                    <input class="d-none" type="text" name="web_page"
                                                           value="{{ request('web_page') }}">
                                                    <input class="d-none" type="date" name="from_date"
                                                           class="form-control"
                                                           value="{{ request('from_date') ?? '' }}">
                                                    <input class="d-none" type="date" name="to_date"
                                                           class="form-control"
                                                           value="{{ request('to_date') ?? '' }}">
                                                    <button type="submit" class="btn btn-sm col-1 float-end">
                                                        <i class="fas fa-download fs-20 text-primary"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($invoices->count())
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                <tr>
                                                    <th style="width: 5%;">SNo.</th>
                                                    <th style="width: 10%;">Invoice Number</th>
                                                    <th style="width: 10%;">Date</th>
                                                    <th style="width: 10%;">customer</th>
                                                    <th style="width:10%;">Cashier</th>
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
                                                        <td>{{ $invoice->created_at->format('m-d-Y')}}
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

                                        </div>
                                    @else
                                        <p class="text-muted text-center">No Invoices.</p>
                                    @endif
                                </div>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>