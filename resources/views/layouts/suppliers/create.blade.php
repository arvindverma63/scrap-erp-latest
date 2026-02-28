<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <h3 class="mb-0">Add New Supplier</h3>
            </div>

            <!-- Add Supplier Form -->
            <div class="card">
                <div class="card-body">
                    @if (session('success') || session('error'))
                        <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show mb-4"
                             role="alert">
                            {{ session('success') ?? session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.supplier.store') }}" id="addSupplierForm"
                          class="needs-validation" novalidate>
                        @csrf

                        <!-- Supplier Type -->
                        <h5 class="card-title mb-3">Supplier Type</h5>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="supplier_type" id="individual"
                                       value="individual"
                                       {{ old('supplier_type', 'individual') == 'individual' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="supplier_type" id="company"
                                       value="company"
                                       {{ old('supplier_type') == 'company' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="company">Company</label>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <h5 class="card-title mt-4 mb-3">Personal Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                       placeholder="Enter full name" required minlength="2" maxlength="100">
                                <div class="invalid-feedback">Supplier name is required (2-100 characters).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                       placeholder="Enter email">
                                <div class="invalid-feedback">Enter a valid email address.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Country Code <span class="text-danger">*</span></label>
                                <select name="country_code" class="form-select select2" required>
                                    @for($i=1;$i<=998;$i++)
                                        <option value="+{{$i}}" {{old('country_code', '+1') == $i ? 'selected' : null}}>
                                            +{{$i}}</option>
                                    @endfor
                                </select>
                                <div class="invalid-feedback">Enter a country code.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}"
                                       placeholder="9999999999" pattern="(\+\d{1,3}\s*)?\d{10}" required>
                                <div class="invalid-feedback">Enter a valid 10-digit phone number.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Product Category <span class="text-danger">*</span></label>
                                <select name="product_id[]" class="form-select select2" multiple>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- Address Details -->
                        <h5 class="card-title mt-4 mb-3">Address Details</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Street Address <span class="text-danger">*</span></label>
                                <input type="text" name="street_address" class="form-control"
                                       value="{{ old('street_address') }}" placeholder="Enter street address" required
                                       minlength="5" maxlength="200">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}"
                                       placeholder="Enter city" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Postal Code</label>
                                <input type="number" name="postal_code" class="form-control"
                                       value="{{ old('postal_code') }}" placeholder="999999">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select name="country" class="form-select select2" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->name}}" {{old('country', 'Jamaica') == $country->name ? 'selected' : null}}>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Company Details -->
                        <div id="companyDetailsSection" style="display: none;">
                            <h5 class="card-title mt-4 mb-3">Company Details</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control"
                                           value="{{ old('company_name') }}" placeholder="Enter company name">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Company Email <span class="text-danger">*</span></label>
                                    <input type="email" name="company_email" class="form-control"
                                           value="{{ old('company_email') }}" placeholder="company@example.com">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Company Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="company_phone_number" class="form-control"
                                           value="{{ old('company_phone_number') }}" placeholder="9999999999">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Tax Number</label>
                                    <input type="text" name="tax" class="form-control" value="{{ old('tax') }}"
                                           placeholder="Enter TRN">
                                </div>
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <h5 class="card-title mt-4 mb-3">Bank Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}"
                                       placeholder="Enter bank name">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Branch</label>
                                <input type="text" name="bank_branch" class="form-control"
                                       value="{{ old('bank_branch') }}" placeholder="Enter branch">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Account Number</label>
                                <input type="number" name="account_number" class="form-control"
                                       value="{{ old('account_number') }}" placeholder="Enter account number">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-1"></i> Add Supplier
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("addSupplierForm");
            const individualRadio = document.getElementById("individual");
            const companyRadio = document.getElementById("company");
            const companySection = document.getElementById("companyDetailsSection");
            const companyInputs = companySection.querySelectorAll("input, select");

            // Toggle company section visibility and required fields
            function toggleCompanySection() {
                if (companyRadio.checked) {
                    companySection.style.display = "block";
                    companyInputs.forEach(input => {
                        // if (input.name !== "tax") {
                        input.required = true;
                        input.disabled = false;
                        // }
                    });
                } else {
                    console.log(345)
                    companySection.style.display = "none";
                    companyInputs.forEach(input => {
                        input.required = false;
                        input.disabled = true;
                    });
                }
            }

            // Run on load
            toggleCompanySection();

            // Bind change events for radio buttons
            individualRadio.addEventListener("change", toggleCompanySection);
            companyRadio.addEventListener("change", toggleCompanySection);

            // Custom Bootstrap validation
            form.addEventListener("submit", function (event) {
                let isValid = true;

                // Check supplier type
                if (!individualRadio.checked && !companyRadio.checked) {
                    isValid = false;
                    form.querySelector(".form-check").classList.add("is-invalid");
                } else {
                    form.querySelector(".form-check").classList.remove("is-invalid");
                }

                // Validate company fields only if company type is selected
                if (companyRadio.checked) {
                    companyInputs.forEach(input => {
                        if (input.name !== "tax" && !input.value.trim()) {
                            isValid = false;
                            input.classList.add("is-invalid");
                        } else if (input.name === "company_phone_number" && input.value && !input.value.match(/(\+\d{1,3}\s*)?\d{10}/)) {
                            isValid = false;
                            input.classList.add("is-invalid");
                        } else if (input.name === "company_email" && input.value && !input.value.match(/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/)) {
                            isValid = false;
                            input.classList.add("is-invalid");
                        } else {
                            input.classList.remove("is-invalid");
                        }
                    });
                }

                if (!form.checkValidity() || !isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add("was-validated");
            }, false);
        });


    </script>
</x-app-layout>