<!-- Modal for this order -->
<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">Selling Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-bordered">
                    <tr>
                        <th>Invoice Number</th>
                        <td>{{ $order->invoice?->invoice_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ $order->created_at->format('m-d-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td>{{ $order->customer?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Customer Email</th>
                        <td>{{ $order->customer?->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Customer Address</th>
                        <td>{{ $order->customer?->address ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Cashier Name</th>
                        <td>{{ $order->cashier?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Cashier Email</th>
                        <td>{{ $order->cashier?->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Amount</th>
                        <td>{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Products</th>
                        <td>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit Price (USD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product?->name ?? '-' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->weightUnit->name ?? '' }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }} USD</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No products</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>