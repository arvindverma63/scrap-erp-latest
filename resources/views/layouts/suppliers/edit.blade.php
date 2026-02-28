<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <h3 class="mb-0">Edit Supplier</h3>
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
                    <form method="POST" action="{{ route('admin.supplier.update', $supplier->id) }}"
                          class="needs-validation" id="editSupplierForm" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Supplier Type Toggle -->
                        <div>
                            <h5 class="card-title mb-3">Supplier Type</h5>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="supplier_type"
                                       id="individual" value="individual"
                                       {{ old('supplier_type', $supplier->supplier_type) == 'individual' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="supplier_type"
                                       id="company" value="company"
                                       {{ old('supplier_type', $supplier->supplier_type) == 'company' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="company">Company</label>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <div class="mb-4">
                            <h5 class="card-title mt-3 mb-2">Personal Details</h5>
                            <div class="row g-3">
                                <!-- Supplier Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Supplier Name <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           placeholder="Enter full name" value="{{ old('name', $supplier->name) }}"
                                           required
                                           minlength="2" maxlength="100">
                                    <div class="invalid-feedback">Supplier name is required (2-100 characters).</div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email"
                                           class="form-control" placeholder="Enter email"
                                           value="{{ old('email', $supplier->email) }}"
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-4">
                                    <label class="form-label">Country Code <span class="text-danger">*</span></label>
                                    <select name="country_code" class="form-select select2" required>
                                        @for($i=1;$i<=998;$i++)
                                            <option value="+{{$i}}" {{$supplier->country_code == '+'.$i ? 'selected' : null}}>
                                                +{{$i}}</option>
                                        @endfor
                                    </select>
                                    <div class="invalid-feedback">Enter a country code.</div>
                                </div>

                                <div class="col-md-4">
                                    <label for="phone" class="form-label">Phone <span
                                                class="text-danger">*</span></label>
                                    <input type="tel" name="phone" id="phone"
                                           class="form-control" placeholder="9999999999"
                                           value="{{ old('phone', $supplier->phone) }}" required
                                           pattern="(\+\d{1,3}\s*)?\d{10}">
                                    <div class="invalid-feedback">Enter a valid 10-digit phone number (country code
                                        optional).
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="product_id" class="form-label">Product Category
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="product_id[]" id="product_id" class="form-select select2" multiple>
                                        @php
                                            // Get all supplier product IDs as an array
                                            $selectedProducts = old('product_id', $supplier->products->pluck('product_id')->toArray());
                                        @endphp
                                        @foreach ($products as $category)
                                            <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, $selectedProducts) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Product category is required.</div>
                                </div>

                            </div>

                            <!-- Address Section -->
                            <div class="mt-3 mb-3">
                                <h5 class="card-title mt-3 mb-2">Address Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="street_address" class="form-label">Street
                                            Address <span class="text-danger">*</span></label>
                                        <input type="text" name="street_address" id="street_address"
                                               class="form-control" placeholder="Enter street address"
                                               value="{{ old('street_address', $supplier->street_address) }}" required
                                               minlength="5" maxlength="200">
                                        <div class="invalid-feedback">Street address is required (5-200 characters).
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="city" class="form-label">City <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" name="city" id="city"
                                               class="form-control" placeholder="Enter city"
                                               value="{{ old('city', $supplier->city) }}" required
                                               minlength="2" maxlength="100">
                                        <div class="invalid-feedback">City is required (2-100 characters).</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="postcode" class="form-label">Postal
                                            Code </label>
                                        <input type="number" name="postal_code" id="postcode"
                                               class="form-control" placeholder="400001"
                                               value="{{ old('postal_code', $supplier->postal_code) }}">
                                        <div class="invalid-feedback">Enter a valid 6-digit postal code.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="country" class="form-label">Country <span
                                                    class="text-danger">*</span></label>
                                        <select name="country" id="country"
                                                class="form-select select2" required>
                                            <option value=""
                                                    {{ old('country', $supplier->country) == '' ? 'selected' : '' }}>
                                                Select
                                                Country
                                            </option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}"
                                                        {{ old('country', $supplier->country) == $country->name ? 'selected' : '' }}>{{$country->name}}
                                                </option>
                                            @endforeach

                                            <!-- Add more countries as needed -->
                                        </select>
                                        <div class="invalid-feedback">Country is required.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Details -->
                        <div id="companyDetailsSection">
                            <h5 class="card-title mt-3 mb-2">Company Details</h5>
                            <div class="row g-3">
                                <!-- Company Name -->
                                <div class="col-md-4">
                                    <label for="company_name" class="form-label">Company Name <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="company_name" id="company_name"
                                           class="form-control" placeholder="Enter company name"
                                           value="{{ old('company_name', $supplier->company_name) }}"
                                           minlength="2" maxlength="100">
                                    <div class="invalid-feedback">Company name is required (2-100 characters).</div>
                                </div>

                                <!-- Company Email -->
                                <div class="col-md-4">
                                    <label for="company_email" class="form-label">Company Email
                                        <span
                                                class="text-danger">*</span></label>
                                    <input type="email" name="company_email" id="company_email"
                                           class="form-control" placeholder="company@example.com"
                                           value="{{ old('company_email', $supplier->company_email) }}"
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <div class="invalid-feedback">Enter a valid company email.</div>
                                </div>

                                <!-- Company Phone -->

                                <div class="col-md-4">
                                    <label for="company_phone_number" class="form-label">Company
                                        Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="company_phone_number"
                                           id="company_phone_number" class="form-control"
                                           placeholder="9999999999"
                                           value="{{ old('company_phone_number', $supplier->company_phone_number) }}"
                                           pattern="(\+\d{1,3}\s*)?\d{10}">
                                    <div class="invalid-feedback">Enter a valid 10-digit phone number (country code
                                        optional).
                                    </div>
                                </div>

                                <!-- Tax Number -->
                                <div class="col-md-4">
                                    <label for="tax" class="form-label">Tax Number
                                        (Optional)</label>
                                    <input type="text" name="tax" id=""
                                           class="form-control" placeholder="Enter TRN"
                                           value="{{ old('tax', $supplier->tax) }}" maxlength="50">
                                    <div class="invalid-feedback">Enter a valid tax number (max 50 characters).</div>
                                </div>

                                <!-- Product Category -->
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="mb-4 mt-4">
                            <h5 class="card-title mt-3 mb-2">Bank Details</h5>
                            <div class="row g-3">
                                <!-- Bank Name -->
                                <div class="col-md-4">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name"
                                           class="form-control" placeholder="Enter bank name"
                                           value="{{ old('bank_name', $supplier->bank_name) }}" maxlength="100">
                                    <div class="invalid-feedback">Bank name must be 2-100 characters.</div>
                                </div>

                                <!-- Bank Branch -->
                                <div class="col-md-4">
                                    <label for="bank_branch" class="form-label">Bank Branch</label>
                                    <input type="text" name="bank_branch" id="bank_branch"
                                           class="form-control" placeholder="Enter bank branch"
                                           value="{{ old('bank_branch', $supplier->bank_branch) }}" maxlength="100">
                                    <div class="invalid-feedback">Bank branch must be 2-100 characters.</div>
                                </div>

                                <!-- Account Number -->
                                <div class="col-md-4">
                                    <label for="account_number" class="form-label">Account
                                        Number</label>
                                    <input type="number" name="account_number" id="account_number"
                                           class="form-control" placeholder="Enter account number"
                                           value="{{ old('account_number', $supplier->account_number) }}"
                                           pattern="\d{9,18}">
                                    <div class="invalid-feedback">Enter a valid account number (9-18 digits).</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-edit me-1"></i> Update
                                Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // For each modal, attach listener
            // document.querySelectorAll("[id^='editSupplierModal']").forEach(function (modal) {
            //     const supplierId = modal.id.replace("editSupplierModal", "");
            const form = document.getElementById("editSupplierForm");
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
                    companySection.style.display = "none";
                    companyInputs.forEach(input => {
                        input.required = false;
                        input.disabled = true;
                    });
                }
            }

            // Run on load (in case editing existing supplier)
            toggleCompanySection();

            // Bind events for radio buttons
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
                        if (input.name === "company_phone_number" && input.value && !input.value.match(/(\+\d{1,3}\s*)?\d{10}/)) {
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
            // });


        });
    </script>

</x-app-layout>