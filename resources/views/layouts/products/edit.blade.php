<!-- Edit Product Modals -->
@foreach($products as $product)
    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
        aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editProductModalLabel{{ $product->id }}">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" class="needs-validation"
                        novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control form-control-sm" name="name"
                                    value="{{ old('name', $product->name) }}" required>
                                <div class="invalid-feedback">Please enter a product name.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select form-select-sm" name="weight_unit_id">
                                    <option value="" {{ old('weight_unit_id', $product->weight_unit_id ?? '') == '' ? 'selected' : '' }}>Select Unit</option>
                                    @foreach($weightUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('weight_unit_id', $product->weight_unit_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a unit.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sale Price</label>
                                <input type="number" class="form-control form-control-sm" name="sale_price" step="0.01"
                                    value="{{ old('sale_price', $product->sale_price) }}" required>
                                <div class="invalid-feedback">Please enter a valid sale price.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Purchase Price</label>
                                <input type="number" class="form-control form-control-sm" name="purchase_price" step="0.01"
                                    value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                <div class="invalid-feedback">Please enter a valid purchase price.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Sale Price</label>
                                <input type="number" class="form-control form-control-sm" name="company_sale_price"
                                    step="0.01" value="{{ old('company_sale_price', $product->company_sale_price) }}"
                                    required>
                                <div class="invalid-feedback">Please enter a valid purchase price.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Loyal Sale Price</label>
                                <input type="number" class="form-control form-control-sm" name="loyal_sale_price"
                                    step="0.01" value="{{ old('loyal_sale_price', $product->loyal_sale_price) }}" required>
                                <div class="invalid-feedback">Please enter a valid purchase price.</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control form-control-sm" name="description"
                                    rows="2">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach