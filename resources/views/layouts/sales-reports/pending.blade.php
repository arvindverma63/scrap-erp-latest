<x-head title="SalesInvoice"/>


<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <!-- Page Heading + Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h4 class="fw-bold mb-0">Sales Orders</h4>

            </div>
            @include('layouts.SalesInvoice.edit')
            <!-- Table -->
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive card shadow-sm border-0 rounded-3">
                        <table class="table table-hover align-middle mb-0" id="datatable_1">
                            <thead class="table-light">
                            <tr>
                                <th>SNo.</th>
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
                                <th>Action</th>
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
                                    <td>
                                        <!-- Voided Order Button -->
                                        {{--                                            <a href="{{ route('admin.selling-orders.voided', $order->id) }}"--}}
                                        {{--                                                class="btn btn-sm btn-outline-primary sweet-action-btn"--}}
                                        {{--                                                data-action="void" data-id="{{ $order->id }}">--}}
                                        {{--                                                Voided--}}
                                        {{--                                            </a>--}}
                                        {{--                                            @can('approve_invoice')--}}
                                        {{--                                                <!-- Approve Order Button -->--}}
                                        {{--                                                <a href="{{ route('admin.selling-orders.complete', $order->id) }}"--}}
                                        {{--                                                    class="btn btn-sm btn-outline-danger sweet-action-btn"--}}
                                        {{--                                                    data-action="complete" data-id="{{ $order->id }}">--}}
                                        {{--                                                    Approve--}}
                                        {{--                                                </a>--}}
                                        {{--                                            @endcan--}}


                                            <a href="{{ route('admin.invoicePage', $order->id) }}"
                                               class="btn btn-sm btn-outline-primary view-invoice">
                                                <i class="fa-solid fa-file-pen"></i>
                                            </a>
                                        

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sweet-action-btn').forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const url = this.getAttribute('href');
                    const action = this.getAttribute('data-action');

                    let title, text, icon, confirmText;

                    if (action === 'complete') {
                        title = 'Approve Order?';
                        text = 'Are you sure you want to approve this order?';
                        icon = 'success';
                        confirmText = 'Yes, approve it!';
                    } else if (action === 'void') {
                        title = 'Void Order?';
                        text = 'This will cancel the order. Are you sure?';
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
