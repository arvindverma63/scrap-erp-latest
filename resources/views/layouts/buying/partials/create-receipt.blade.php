<x-head title="Purchasing" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <form action="{{ route('admin.orders.purchase.store') }}" method="POST" id="orderForm" novalidate>
                    @csrf

                    <!-- Order main fields -->
                    <div class="row g-2">
                        <div class="col-md-8">
                            <label class="form-label small mb-1">Receipt Number</label>
                            <input type="text" name="invoice_number" class="form-control form-control-sm" value="{{$receiptNumber}}"
                                placeholder="PO-XXXX" required>
                        </div>
                        <div class="col-md-4">
                                <label class="form-label small mb-1">Receipt Date</label>
                                <input type="date" name="invoice_date" 
                                    class="form-control form-control-sm scale-fee"
                                    value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier</label>
                            <select name="supplier_id" id="supplierSelect" class="form-select form-select-sm select-supplier select2-supplier" required>
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                         <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier Phone</label>
                            <input type="text" id="supplier_phone" class="form-control form-control-sm"
                                placeholder="|-------supplier phone-------|" disabled>
                        </div>
                       
                         <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier Email</label>
                            <input type="text" id="supplier_email" class="form-control form-control-sm"
                                placeholder="|-------supplier email-------|" disabled>
                        </div> 

                         <div class="col-md-3">
                            <label class="form-label small mb-1">Supplier Address</label>
                            <input type="text" id="supplier_address" class="form-control form-control-sm"
                                placeholder="|-------supplier name-------|" disabled>
                        </div> 
                    </div>



                    <!-- Multiple Products Table -->
                    <div class="card mt-2">
                        <div class="card-header py-0 pb-2 text-end">
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRow">
                               <i class="fas fa-plus"></i>Add Product
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0" id="productsTable">
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
                                    <tbody id="productsTable">
                                        <tr>
                                            <td>
                                                 <select name="product_id[]" class="form-select form-select-sm product-select select2-product"
                                                    required>
                                                    <option value="">Select Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price ?? '' }}">
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="weight_quantity[]" min="0" step="0.01"
                                                    class="form-control form-control-sm qty-input" placeholder="Qty" required>
                                            </td>
                                            <td>
                                                <select name="weight_unit_id[]" class="form-select form-select-sm unit-select">
                                                    <option value="">Unit</option>
                                                    @foreach ($weightUnits as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="rate_per_unit[]" min="0" step="0.01" class="form-control form-control-sm price-input"
                                                    placeholder="Price" readonly required>
                                            </td>
                                            <td>
                                                <input type="number" name="total_amount[]" min="0" step="0.01" readonly
                                                    class="form-control form-control-sm total-input bg-light" placeholder="Total" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeRow"
                                                    disabled><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr class="table-info">
                                            <td colspan="4"><strong>Subtotal</strong></td>
                                            <td><input type="number" id="subtotal" name="subtotal" min="0" step="0.01" readonly
                                                    class="form-control form-control-sm bg-light" value="0.00"></td>
                                            <td></td>
                                        </tr>
                                         <tr>
                                            <td colspan="4"><strong>Less Scale Fee</strong></td>
                                            <td><input type="number" id="lessscalefee" name="less_scale_fee" min="0" step="0.01"
                                                    class="form-control form-control-sm bg-light" value="0.00"></td>
                                            <td></td>
                                        </tr>
                                          <tr class="table-info">
                                            <td colspan="4"><strong>Amount to be Paid</strong></td>
                                            <td><input type="number" id="payableamount" name="paid_amount" min="0" step="0.01" readonly
                                                    class="form-control form-control-sm bg-light paid-amount" value="0.00"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Footer fields -->
                    <div class="row g-2 mb-2">
                        {{-- <div class="col-md-3">
                            <label class="form-label small mb-1">Paid Amount</label>
                            <input type="number" name="paid_amount" min="0" step="0.01"
                                class="form-control form-control-sm paid-amount" placeholder="0.00">
                        </div> --}}
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Payment Method</label>
                            <select name="payment_method" class="form-select form-select-sm" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                                <option value="Online">Online</option>
                                <option value="Credit">Credit</option>
                                <option value="Wire">Wire</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-none">
                            <label class="form-label small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm" required>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                     
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Due Amount</label>
                            <input type="number" name="balance_amount" id="balance_amount" min="0" step="0.01"
                                class="form-control form-control-sm" placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small mb-1">Paid Amount</label>
                            <input type="number" name="partially_paid" id="partially_paid" min="0" step="0.01"
                                class="form-control form-control-sm" placeholder="0.00">
                        </div>

                        

                        
                    </div>
                    <div class="row g-2 d-none">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <small class="text-muted">Subtotal: <span id="subtotalDisplay">0.00</span></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body py-2">
                                    <small class="text-warning">Scale Fee: <span id="feeDisplay">0.00</span></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body py-2">
                                    <small class="text-success">Partial Payment: <span
                                            id="balanceDisplay">0.00</span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">
                            <i class="fas fa-plus"></i> Generate Receipt
                        </button>
                    </div>
                </form>
        </div>
    </div>

@include('layouts.buying.js')


</x-app-layout>