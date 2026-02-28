<x-head title="Pending Receipt"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <!-- Page Heading + Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h4 class="fw-bold">Receipts (Pending)</h4>

{{--                <div class="d-flex gap-2">--}}
{{--                    <a href="{{ route('admin.orders.purchase.create') }}" class="btn btn-primary">--}}
{{--                        <i class="fas fa-cart-plus"></i> Add Order--}}
{{--                    </a>--}}
{{--                    <div class="btn-group">--}}
{{--                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"--}}
{{--                                aria-expanded="false">--}}
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
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($purchaseOrders as $index => $order)
                                <tr>
                                    <td>{{ $purchaseOrders->firstItem() + $index}}</td>
                                    <td><a href="{{ route('admin.receiptPage', $order->id) }}" class="fw-bold">
                                            {{ $order->invoice->invoice_number }}
                                        </a></td>
                                    <td>{{ $order->created_at->format('m-d-Y') }}</td>
                                    <td>{{$order->cashier->name}}</td>
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
                                        
                                            <a href="{{ route('admin.receiptPage', $order->id) }}"
                                               class="btn btn-sm btn-outline-primary view-invoice">
                                                <i class="fa-solid fa-file-pen"></i>
                                            </a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No Receipt orders found.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sweet-purchase-btn').forEach(function (button) {
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

    @include('layouts.common.datatable')
</x-app-layout>
