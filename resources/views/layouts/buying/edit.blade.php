@can('buying_update')
<!-- Update Order Modal -->
<div class="modal fade" id="updateOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="updateOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateOrderModalLabel">Update Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.orders.purchase.update', $order->id) }}" method="POST" id="orderForm{{ $order->id }}" novalidate>
                    @csrf
                    @method("PUT")

                    <!-- Order main fields -->
                    <div class="row g-2">
                        <div class="col-md-8">
                            <label class="form-label small mb-1">Receipt Number</label>
                            <input type="text" name="invoice_number" class="form-control form-control-sm" 
                                value="{{ $order->invoice?->invoice_number ?? '' }}"
                                placeholder="PO-XXXX" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Receipt Date</label>
                            <input type="date" name="invoice_date" 
                                class="form-control form-control-sm scale-fee"
                                value="{{ $order->invoice?->invoice_date ?? date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier</label>
                            <select name="supplier_id" id="supplierSelect{{ $order->id }}" class="form-select form-select-sm select-supplier select2-supplier" required>
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                        {{ $order->supplier_id == $supplier->id ? 'selected' : '' }}
                                        data-phone="{{ $supplier->phone }}" 
                                        data-email="{{ $supplier->email }}" 
                                        data-address="{{ $supplier->address }}">
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier Phone</label>
                            <input type="text" id="supplier_phone{{ $order->id }}" class="form-control form-control-sm"
                                value="{{ $order->supplier?->phone ?? '' }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier Email</label>
                            <input type="text" id="supplier_email{{ $order->id }}" class="form-control form-control-sm"
                                value="{{ $order->supplier?->email ?? '' }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier Address</label>
                            <input type="text" id="supplier_address{{ $order->id }}" class="form-control form-control-sm"
                                value="{{ $order->supplier?->address ?? '' }}" disabled>
                        </div>
                    </div>

                    <!-- Multiple Products Table -->
                    <div class="card mt-2">
                        <div class="card-header py-0 pb-2 text-end">
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRow{{ $order->id }}">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0" id="productsTable{{ $order->id }}">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30%;">Material</th>
                                            <th style="width: 12%;">Quantity</th>
                                            <th style="width: 12%;">Unit</th>
                                            <th style="width: 15%;">Unit Price</th>
                                            <th style="width: 15%;">Total Amount</th>
                                            <th style="width: 8%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsTableBody{{ $order->id }}">
                                        @foreach ($order->orderItems as $index => $orderItem)
                                        <tr>
                                            <td>
                                                <select name="product_id[]" class="form-select form-select-sm product-select select2-product"
                                                    required>
                                                    <option value="">Select Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                            data-price="{{ $product->price ?? '' }}"
                                                            {{ $orderItem->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="quantity[]" min="0" step="0.01"
                                                    class="form-control form-control-sm qty-input" 
                                                    value="{{ $orderItem->quantity ?? '' }}" 
                                                    placeholder="Qty" required>
                                            </td>
                                            <td>
                                                <select name="weight_unit_id[]" class="form-select form-select-sm unit-select">
                                                    <option value="">Unit</option>
                                                    @foreach ($weightUnits as $unit)
                                                        <option value="{{ $unit->id }}" 
                                                            {{ $orderItem->weight_unit_id == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="unit_price[]" min="0" step="0.01" 
                                                    class="form-control form-control-sm price-input"
                                                    value="{{ $orderItem->unit_price ?? '' }}" 
                                                    placeholder="Price" readonly required>
                                            </td>
                                            <td>
                                                <input type="number" name="total_amount[]" min="0" step="0.01" readonly
                                                    class="form-control form-control-sm total-input bg-light" 
                                                    value="{{ $orderItem->total_amount ?? '' }}" 
                                                    placeholder="Total" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeRow"
                                                    {{ $index == 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <td colspan="4"><strong>Subtotal</strong></td>
                                            <td><input type="number" id="subtotal{{ $order->id }}" name="total_amount" min="0" step="0.01" readonly
                                                    class="form-control form-control-sm bg-light" 
                                                    value="{{ $order->total_amount ?? '0.00' }}"></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><strong>Less Scale Fee</strong></td>
                                            <td><input type="number" id="lessscalefee{{ $order->id }}" name="less_scale_fee" min="0" step="0.01"
                                                    class="form-control form-control-sm bg-light" 
                                                    value="{{ $order->less_scale_fee ?? '0.00' }}"></td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td colspan="4"><strong>Amount to be Paid</strong></td>
                                            <td><input type="number" id="payableamount{{ $order->id }}" name="paid_amount" min="0" step="0.01" readonly
                                                    class="form-control form-control-sm bg-light paid-amount" 
                                                    value="{{ $order->paid_amount ?? '0.00' }}"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer fields -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Payment Method</label>
                            <select name="payment_method" class="form-select form-select-sm" required>
                                <option value="Cash" {{ $order->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank" {{ $order->payment_method == 'Bank' ? 'selected' : '' }}>Bank</option>
                                <option value="Online" {{ $order->payment_method == 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="Credit" {{ $order->payment_method == 'Credit' ? 'selected' : '' }}>Credit</option>
                                <option value="Wire" {{ $order->payment_method == 'Wire' ? 'selected' : '' }}>Wire</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-none">
                            <label class="form-label small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm" required>
                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Due Amount</label>
                            <input type="number" name="balance_amount" min="0" step="0.01"
                                class="form-control form-control-sm" 
                                value="{{ $order->balance() ?? '0.00' }}" 
                                placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Paid Amount</label>
                            <input type="number" name="partially_paid" min="0" step="0.01"
                                class="form-control form-control-sm" 
                                value="{{ $order->totalPaid() ?? '0.00' }}" 
                                placeholder="0.00">
                        </div>
                    </div>
                    <div class="row g-2 d-none">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <small class="text-muted">Subtotal: <span id="subtotalDisplay{{ $order->id }}">{{ $order->total_amount ?? '0.00' }}</span></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body py-2">
                                    <small class="text-warning">Scale Fee: <span id="feeDisplay{{ $order->id }}">{{ $order->less_scale_fee ?? '0.00' }}</span></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body py-2">
                                    <small class="text-success">Partial Payment: <span id="balanceDisplay{{ $order->id }}">{{ $order->totalPaid() ?? '0.00' }}</span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm" id="submitBtn{{ $order->id }}">
                            <i class="fas fa-plus"></i> Update Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

@include('layouts.buying.partials.edit-js')

<style>
    .table tfoot tr {
        font-weight: bold;
    }

    .total-input,
    #subtotal {
        font-weight: 600;
        color: #28a745;
    }

    .is-valid {
        border-color: #28a745;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    .removeRow:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>