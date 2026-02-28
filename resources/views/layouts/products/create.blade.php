<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="addProductForm" method="POST" action="{{ route('admin.products.store') }}">
                    @csrf
                    <div class="row g-3">

                        <!-- Product Name -->
                        <div class="col-md-6">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" id="name"
                                value="{{ old('name') }}">
                            <small class="text-danger d-none">Please enter a product name.</small>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div class="col-md-3">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="weight_unit_id" id="weight_unit_id">
                                <option value="">Select Unit</option>
                                @foreach($weightUnits as $unit)
                                    <option value="{{ $unit->id }}" {{ old('weight_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger d-none">Please select a unit.</small>
                            @error('weight_unit_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Sale Price -->
                        <div class="col-md-6">
                            <label class="form-label">Sale Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="sale_price" id="sale_price"
                                step="0.01" value="{{ old('sale_price') }}">
                            <small class="text-danger d-none">Please enter sale price.</small>
                            @error('sale_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div class="col-md-6">
                            <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="purchase_price"
                                id="purchase_price" step="0.01" value="{{ old('purchase_price') }}">
                            <small class="text-danger d-none">Please enter purchase price.</small>
                            @error('purchase_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Company Sale Price -->
                        <div class="col-md-6">
                            <label class="form-label">Company Sale Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="company_sale_price"
                                id="company_sale_price" step="0.01" value="{{ old('company_sale_price') }}">
                            <small class="text-danger d-none">Please enter company sale price.</small>
                            @error('company_sale_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Loyal Sale Price -->
                        <div class="col-md-6">
                            <label class="form-label">Loyal Sale Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="loyal_sale_price"
                                id="loyal_sale_price" step="0.01" value="{{ old('loyal_sale_price') }}">
                            <small class="text-danger d-none">Please enter loyal sale price.</small>
                            @error('loyal_sale_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control form-control-sm" name="description"
                                rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save me-1"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Validation -->
<script>
    document.getElementById('addProductForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let valid = true;

        // Reset errors
        this.querySelectorAll('.form-control, .form-select').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('small.text-danger.d-none').forEach(el => el.classList.add('d-none'));

        const requiredFields = [
            'name',
            'weight_unit_id',
            'sale_price',
            'purchase_price',
            'company_sale_price',
            'loyal_sale_price'
        ];

        requiredFields.forEach(id => {
            const field = document.getElementById(id);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                field.nextElementSibling.classList.remove('d-none');
                valid = false;
            }
        });

        if (valid) this.submit();
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
    }

    label .text-danger {
        font-weight: bold;
    }
</style>