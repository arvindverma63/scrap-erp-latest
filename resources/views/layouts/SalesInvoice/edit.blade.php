<!-- Edit Purchase Order Modal -->
@foreach($salesOrders as $order)
    <div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1"
        aria-labelledby="editOrderModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel{{ $order->id }}">Edit Sales Order -
                        {{ $order->transaction_id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('admin.orders.selling.update')}}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small mb-1">PO Number</label>
                                <input type="text" name="transaction_id" class="form-control form-control-sm"
                                    value="{{ $order->transaction_id }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">customer</label>
                                <select name="customer_id" class="form-select form-select-sm">
                                    <option value="">Select customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Product</label>
                                <select name="product_id" class="form-select form-select-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $order->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Quantity</label>
                                <input type="number" name="weight_quantity" class="form-control form-control-sm"
                                    value="{{ $order->weight_quantity }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Unit</label>
                                <select name="weight_unit_id" class="form-select form-select-sm">
                                    <option value="">Select Unit</option>
                                    @foreach($weightUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ $order->weight_unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Rate per Unit</label>
                                <input type="number" step="0.01" name="rate_per_unit" class="form-control form-control-sm"
                                    value="{{ $order->rate_per_unit }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Less Scale Fee</label>
                                <input type="number" step="0.01" name="less_scale_fee" class="form-control form-control-sm"
                                    value="{{ $order->less_scale_fee }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Total Amount</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control form-control-sm"
                                    value="{{ $order->total_amount }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Paid Amount</label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control form-control-sm"
                                    value="{{ $order->paid_amount }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Payment Method</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="Cash" {{ $order->payment_method == 'Cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="Bank" {{ $order->payment_method == 'Bank' ? 'selected' : '' }}>Bank
                                    </option>
                                    <option value="Online" {{ $order->payment_method == 'Online' ? 'selected' : '' }}>Online
                                    </option>
                                    <option value="Credit" {{ $order->payment_method == 'Credit' ? 'selected' : '' }}>Credit
                                    </option>
                                </select>
                            </div>
                          
                            <div class="col-12">
                                <label class="form-label small mb-1">Remarks</label>
                                <textarea name="remarks" class="form-control form-control-sm"
                                    rows="2">{{ $order->remarks ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach