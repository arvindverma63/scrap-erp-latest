<!-- Order Modal -->
<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1"
     aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="orderModalLabel{{ $order->id }}">
                    Order #{{ $order->transaction_id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Basic Order Info -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Cashier:</strong> {{ $order->cashier->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge {{ $order->status === 'Pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ $order->status }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Created At:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Invoice #:</strong> {{ $order->latestInvoice->invoice_number ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Invoice Date:</strong> {{ $order->latestInvoice->invoice_date ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Total:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
                        <p class="mb-1"><strong>Paid:</strong> ₹{{ number_format($order->paid_amount, 2) }}</p>
                        <p class="mb-1"><strong>Balance:</strong> ₹{{ number_format($order->balance(), 2) }}</p>
                    </div>
                </div>

                <!-- Order Items Table -->
                <h6 class="fw-bold mt-4 mb-2">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Weight Unit</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->weight_unit->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">₹{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
