<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1"
    aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">
                    <i class="fas fa-file-invoice"></i>
                    {{ $order->type === 'Sales' ? 'Customer' : 'Supplier' }} Ledger Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 mb-3">
                @php
                    $statusClass = match ($order->status) {
                        'Completed' => 'bg-success',
                        'Pending' => 'bg-warning text-dark',
                        'Voided' => 'bg-danger',
                        default => 'bg-secondary',
                    };

                    $party = $order->type === 'Sales' ? $order->customer : $order->supplier;
                @endphp

                <table class="table table-bordered mb-0">
                    <tr><th>Invoice ID</th><td>{{ $order->invoice?->invoice_number ?? '-' }}</td></tr>
                    <tr><th>Date</th><td>{{ $order->created_at->format('d M Y') }}</td></tr>
                    <tr><th>{{ $order->type === 'Sales' ? 'Customer' : 'Supplier' }} Name</th><td>{{ $party?->name ?? '-' }}</td></tr>
                    <tr><th>Email</th><td>{{ $party?->email ?? '-' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $party?->phone ?? '-' }}</td></tr>
                    <tr><th>Address</th>
                        <td>{{ $party?->street_address ?? '' }}, {{ $party?->city ?? '' }}, {{ $party?->country ?? '' }} - {{ $party?->postal_code ?? '' }}</td>
                    </tr>
                    <tr><th>Company Details</th>
                        <td>
                            <strong>{{ $party?->company_name ?? '-' }}</strong><br>
                            Email: {{ $party?->company_email ?? '-' }}<br>
                            Phone: {{ $party?->company_phone_number ?? '-' }}
                        </td>
                    </tr>
                    <tr><th>Bank Info</th>
                        <td>
                            Bank: {{ $party?->bank_name ?? '-' }}<br>
                            Branch: {{ $party?->bank_branch ?? '-' }}<br>
                            A/C No: {{ $party?->account_number ?? '-' }}
                        </td>
                    </tr>
                    <tr><th>Tax No.</th><td>{{ $party?->tax ?? '-' }}</td></tr>
                    <tr><th>Cashier</th><td>{{ $order->cashier?->name ?? '-' }} ({{ $order->cashier?->email ?? '-' }})</td></tr>
                    <tr><th>Total Amount</th><td>{{ number_format($order->total_amount, 2) }}
                        </td></tr>
                    <tr><th>Status</th><td><span class="badge {{ $statusClass }}">{{ $order->status }}</span></td></tr>

                    <tr>
                        <th>Products</th>
                        <td>
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit Price (JMD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($order->type == 'Purchase')
                                        @forelse($order->orderItems as $item)
                                            <tr>
                                                <td>{{ $item->product?->name ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->weightUnit?->name ?? '-' }}</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted">No products found</td></tr>
                                        @endforelse
                                    @endif

                                    @if ($order->type == 'Sales')
                                        @forelse($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product?->name ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->weightUnit?->name ?? '-' }}</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted">No products found</td></tr>
                                        @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
