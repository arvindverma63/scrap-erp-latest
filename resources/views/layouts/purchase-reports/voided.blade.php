<x-head title="Voided Order" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <!-- Page Heading + Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h4 class="fw-bold mb-0">Receipts (Voided)</h4>

{{--                <div class="d-flex gap-2">--}}
{{--                    <a href="{{ route('admin.orders.purchase.create') }}" class="btn btn-primary">--}}
{{--                        <i class="fas fa-cart-plus"></i> Add Order--}}
{{--                    </a>--}}
{{--                    <div class="btn-group">--}}
{{--                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"--}}
{{--                            aria-expanded="false">--}}
{{--                            <i class="fas fa-file-export"></i> Export--}}
{{--                        </button>--}}
{{--                        <ul class="dropdown-menu">--}}
{{--                            <li>--}}
{{--                                <a class="dropdown-item" href="#">--}}
{{--                                    <i class="fas fa-file-csv text-success"></i> Export CSV--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a class="dropdown-item" href="#">--}}
{{--                                    <i class="fas fa-file-pdf text-danger"></i> Export PDF--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}

            </div>
            @include('layouts.PO.edit')
            <!-- Table -->
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive card shadow-sm rounded-3 border-0">
                        <table class="table table-hover align-middle mb-0" id="datatable_1">
                            <thead class="table-light">
                            <tr>
                                <th>Sr.No.</th>
                                <th>Receipt Number</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>Supplier</th>
                                <th>Total Amount
                                    ({{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }})
                                </th>
                                <th>Paid ({{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }})
                                </th>
                                <th>Status</th>
                                <th>Receipt</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($purchaseOrders as $index => $order)
                                <tr>
                                    <td>{{ $purchaseOrders->firstItem() + $index }}</td>
                                    <td><a href="{{ route('admin.receiptPage', $order->id) }}" class="fw-bold">
                                            {{ $order->invoice->invoice_number }}
                                        </a></td>
                                    <td>{{ $order->created_at->format('m-d-Y') }}</td>
                                    <td>{{$order->cashier?->name ?? '-'}}</td>
                                    <td>{{ $order->supplier?->name ?? '-' }}</td>
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
                                                                                        @elseif($order->status == 'Completed') bg-success
                                                                                        @elseif($order->status == 'Voided') bg-danger @endif">
                                                {{ $order->status }}
                                            </span>
                                    </td>
                                    <td>
                                        @if ($order->invoices->count())
                                            <a href="{{ route('admin.receiptPage', $order->id) }}"
                                               class="btn btn-sm btn-outline-primary view-invoice">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <!-- <a href="{{ route('admin.orders.purchase.invoice', $order->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                                
                                                <a href="{{ route('admin.orders.purchase.invoice', $order->id) }}?action=view"
                                                    target="_blank" class="btn btn-sm btn-outline-success"
                                                    title="View / Print Invoice">
                                                    <i class="fa-solid fa-print"></i>
                                                </a> -->
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
                    </div>
                    <p class="float-end">{{ $purchaseOrders->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>
            {{-- <div class="mt-3">
                {{ $purchaseOrders->links('pagination::bootstrap-5') }}
            </div> --}}
        </div>
    </div>
    @include('layouts.common.datatable')
</x-app-layout>
