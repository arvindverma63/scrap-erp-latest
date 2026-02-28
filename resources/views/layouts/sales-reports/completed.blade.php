<x-head title="SalesInvoice"/>


<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <!-- Page Heading + Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h4 class="fw-bold mb-0">Sales Orders</h4>

                {{--<div class="d-flex gap-2">
                    <!-- Add Order Button -->
                    <a href="{{ route('admin.sales.completedReport') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Order
                    </a>


                    <!-- Export Dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-csv text-success"></i> Export CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-pdf text-danger"></i> Export PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>--}}

            </div>
            <!-- Filters -->
            {{-- <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.sales.completedReport') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="transaction_id" value="{{ request('transaction_id') }}"
                                    class="form-control" placeholder="Search PO number...">
                            </div>
                            <div class="col-md-3">
                                <select name="customer_id" class="form-select">
                                    <option value="">Select customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id')==$customer->id ?
                                        'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Status</option>
                                    <option value="Pending" {{ request('status')=='Pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="Processing" {{ request('status')=='Processing' ? 'selected' : '' }}>
                                        Processing</option>
                                    <option value="Completed" {{ request('status')=='Completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="Voided" {{ request('status')=='Voided' ? 'selected' : '' }}>Voided
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="form-control" placeholder="From">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"
                                    placeholder="To">
                            </div>
                            <div class="col-md-12 text-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('admin.sales.completedReport') }}"
                                    class="btn btn-light btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div> --}}
            @include('layouts.SalesInvoice.edit')
            <!-- Table -->
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive card border-0 shadow-sm rounded-3">
                        <table class="table table-hover align-middle mb-0 border" id="datatable_1">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Invoice Number</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>customer</th>
                                <th>Total Amount
                                    ({{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }})
                                </th>
                                <th>Due Amount ({{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }})
                                </th>
                                <th>Status</th>
                                <th>Invoice</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($salesOrders as $index => $order)
                                <tr>
                                    <td>{{ $salesOrders->firstItem() + $index}}</td>
                                    <td><a href="{{ route('admin.invoicePage', $order->id) }}" class="fw-bold">
                                            {{ $order->invoice->invoice_number }}
                                        </a></td>
                                    <td>{{ $order->created_at->format('m-d-Y') }}</td>
                                    <td>{{$order->cashier->name}}</td>
                                    <td>{{ $order->customer?->name ?? '-' }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}
                                        {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                    </td>
                                    <td>${{ number_format($order->paid_amount, 2) }}
                                        {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                    </td>
                                    <td>
                                            <span
                                                    class="badge
                                                                                        @if ($order->status == 'Pending') bg-warning text-dark
                                                                                        @elseif($order->status == 'Completed') bg-success @endif">
                                                {{ $order->status }}
                                            </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($order->invoices->count())
                                            <a href="{{ route('admin.invoicePage', $order->id) }}"
                                               class="btn btn-sm btn-outline-primary btn-sec">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.sales.invoice', $order->id) }}"
                                               class="btn btn-sm btn-outline-primary btn-sec">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <!-- Sales Invoice - Print (View) -->
                                            <a href="{{ route('admin.orders.sales.invoice', $order->id) }}?action=view"
                                               target="_blank" class="btn btn-sm btn-outline-success btn-sec"
                                               title="View / Print Sales Invoice">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @else
                                            <a href="" class="btn btn-sm btn-outline-success">Create</a>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No purchase orders found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{-- <div class="mt-2">
                            {{ $salesOrders->links() }}
                        </div> --}}
                    </div>
                    <p class="float-end">{{ $salesOrders->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.common.datatable')
</x-app-layout>
