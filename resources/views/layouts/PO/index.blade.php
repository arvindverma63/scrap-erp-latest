<x-head title="PurchaseOrder"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Heading + Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h4 class="fw-bold mb-0">Purchase Orders</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.purchase.index') }}" class="btn btn-primary">
                         <i class="fas fa-cart-plus"></i> Add Order
                    </a>
                     <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.PO.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="transaction_id" value="{{ request('transaction_id') }}"
                                    class="form-control" placeholder="Search PO number...">
                            </div>
                            <div class="col-md-3">
                                <select name="supplier_id" class="form-select">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Status</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>
                                        Processing</option>
                                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="Voided" {{ request('status') == 'Voided' ? 'selected' : '' }}>Voided
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
                                <a href="{{ route('admin.PO.index') }}" class="btn btn-light btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @include('layouts.PO.edit')
            <!-- Table -->
            <div class="table-responsive card shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sr.No.</th>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Total Amount ({{App\Models\Setting::where('key','currency_symbol')->first()->value}})</th>
                            <th>Paid ({{App\Models\Setting::where('key','currency_symbol')->first()->value}})</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $index => $order)
                            <tr>
                                <td>{{ $purchaseOrders->firstItem() + $index }}</td>
                                <td>{{ $order->invoice->invoice_number }}</td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>{{ $order->supplier?->name ?? '-' }}</td>
                                <td>${{ number_format($order->total_amount, 2) }} {{App\Models\Setting::where('key','currency_symbol')->first()->value}}</td>
                                <td>${{ number_format($order->paid_amount, 2) }} {{App\Models\Setting::where('key','currency_symbol')->first()->value}}</td>
                                <td>
                                    <span class="badge 
                                                                        @if ($order->status == 'Pending') bg-warning text-dark
                                                                        @elseif($order->status == 'Completed') bg-success
                                                                        @elseif($order->status == 'Voided') bg-danger @endif">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td>
                                    @if ($order->invoices->count())
                                        <a href="{{route('admin.orders.purchase.invoice', $order->id)}}"
                                            class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-download"></i></a>
                                    @else
                                        <a href="" class="btn btn-sm btn-outline-success">Create</a>
                                    @endif
                                </td>
                                <td>
                                    {{-- <a href="" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editOrderModal{{ $order->id }}">Edit</a> --}}
                                    {{-- Show Voided button only if NOT Voided already --}}
                                    @if ($order->status == 'Voided' || $order->status == 'Completed')
                                        <a href="{{ route('admin.order.voided', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="return confirm('Are you sure you want to mark this order as Voided?');">
                                            Voided
                                        </a>
                                    @endif

                                    {{-- Show Complete button only if NOT Completed already --}}
                                    @if ($order->status == 'Completed' || $order->status == 'Voided')
                                        <a href="{{ route('admin.order.completed', $order->id) }}"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to mark this order as completed?');">
                                            Complete
                                        </a>
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
            </div>
            <div class="mt-3">
                    {{ $purchaseOrders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</x-app-layout>