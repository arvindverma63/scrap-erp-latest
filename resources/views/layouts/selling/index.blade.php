<x-head title="Selling"/>


<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Selling / Sales Orders</h3>
                @can('selling_create')
                 @if(!in_array(auth()->user()->roles->first()->id, [1,5]))
                    <a href="{{route('admin.orders.selling.create')}}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create Order
                    </a>
                    @endif
                @endcan
            </div>
            <!-- Bootstrap Alert -->
            @if (session('success') || session('error'))
                <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show"
                     role="alert">
                    {{ session('success') ?? session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Something went wrong.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- <!-- Orders Table -->
            @include('layouts.selling.create') --}}

            <!-- Search Bar -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form class="row g-3 align-items-end" method="GET"
                          action="{{ route('admin.orders.selling.index') }}">
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
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Search by Invoice, Customer, or Status"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit">
                                    Filter
                                </button>
                                <a href="{{ route('admin.orders.selling.index') }}" class="btn btn-light flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

{{--            <form method="GET" action="{{ route('admin.orders.selling.index') }}" id="searchForm" class="mb-3">--}}
{{--                <div class="input-group">--}}
{{--                    <input type="text" name="search" id="search" class="form-control"--}}
{{--                           placeholder="Search by Invoice, Customer, or Status" value="{{ request('search') }}">--}}
{{--                    <button class="btn btn-primary" type="submit">--}}
{{--                        <i class="fa fa-search"></i> Search--}}
{{--                    </button>--}}
{{--                    @if (request('search'))--}}
{{--                        <a href="{{ route('admin.orders.selling.index') }}" class="btn btn-outline-secondary">Clear</a>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </form>--}}

            <div class="table-responsive card shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 fs-6">
                    <thead class="table-light">
                    <tr>
                        <th>Sr.No.</th>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Total</th>
                        <th>Due Amount</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sellingOrders as $index => $order)
                        <tr>
                            <td>{{ $sellingOrders->firstItem() + $index}}</td>
                            <td>{{ $order->created_at->format('m-d-Y') }}</td>
                            <td>
                                @if($order->cash_count == 0)
                                <a href="{{ route('admin.invoicePage', $order->id) }}" class="fw-bold">
                                    {{ $order->invoice->invoice_number }}
                                </a>
                                @else
                                    <p class="fw-bold" data-bs-toggle="tooltip" title="Today's cash count generated">
                                    {{ $order->invoice->invoice_number }}
            </p>
                                @endif 
                                
                            </td>
                            <td>
                                <button class="btn btn-sm bg-transparent border-0 text-primary fw-bold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCustomerModal-{{ $order->customer->id }}">
                                    {{ $order->customer?->name ?? '-' }}
                                </button>
                            </td>
                            <td>{{ $order->cashier?->name ?? '-' }}</td>
                            <td>{{ number_format($order->total_amount, 2) }}
                                {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}
                            </td>
                            <td>{{ number_format($order->paid_amount, 2) }}
                                {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($order->status) {
                                        'Completed' => 'bg-success',
                                        'Pending' => 'bg-warning text-dark',
                                        'Voided' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                            </td>
                            <td>
                                @can('selling_update')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm bg-transparent border-0"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu">
                                            @if($order->status == 'Completed')
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('admin.orders.sales.invoice', $order->id) }}"
                                                       style="cursor: pointer;">
                                                        <i class="fa-solid fa-download"></i> Download Invoice
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item text-secondary" aria-disabled="true"
                                                       style="cursor: not-allowed;">
                                                        <i class="fa-solid fa-download"></i> Download Invoice
                                                    </a>
                                                </li>
                                            @endif

                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                   data-bs-target="#orderModal{{ $order->id }}"
                                                   style="cursor: pointer;">
                                                    <i class="fa-solid fa-eye"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            @can("payment_create")
                                                @if($order->paid_amount > 0 && $order->status == 'Completed')
                                                @if($order->cash_count == 0)
                                              <li>
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                           style="cursor: pointer;"
                                                           data-bs-target="#addSalesPaymentModal{{ $order->id }}">
                                                            <i class="fa-solid fa-money-bill-wave"></i> Add Payment
                                                        </a>
                                                    </li>
                                             @else
                                              <li>
                                                    <a class="dropdown-item py-0 cursor-pointer" data-bs-toggle="tooltip" title="Today's cash count generated">
                                                        <i class="fa-solid fa-money-bill-wave"></i> Add Payment
                                                    </a>
                                                </li>
                                             @endif
                                                @else
                                                    <li>
                                                        <a class="dropdown-item" style="cursor: not-allowed;"
                                                           aria-disabled="true">
                                                            <i class="fa-solid fa-money-bill-wave"></i> Add Payment
                                                        </a>
                                                    </li>
                                                @endif

                                            @endcan
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            @can("payment_read")
                                                <li>
                                                    <a class="dropdown-item" data-bs-toggle="modal"
                                                       style="cursor: pointer;"
                                                       data-bs-target="#viewSalesPaymentsModal{{ $order->id }}">
                                                        <i class="fa-solid fa-list"></i> View Payments
                                                    </a>
                                                </li>
                                            @endcan

                                        </ul>
                                    </div>
                                @endcan

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-3">No purchase orders found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @forelse($sellingOrders as $index => $order)
                @include('layouts.selling.view')
                @include('layouts.selling.partials.add-payments')
                @include('layouts.selling.partials.view-payments')
                @include('layouts.selling.partials.customer')
            @empty
            @endforelse

            <div class="mt-3">
                <p class="float-end">{{ $sellingOrders->links('pagination::bootstrap-5') }}</p>
            </div>
        </div>
    </div>
    @include('layouts.common.sweet-alert.search')
</x-app-layout>