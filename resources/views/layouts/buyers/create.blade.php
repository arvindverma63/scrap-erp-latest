<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <h3 class="mb-0">Add Customer</h3>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Add Customer Form -->
            <div class="card">
                <div class="card-body">
                    <!-- Bootstrap Alert -->
                    @if (session('success') || session('error'))
                        <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show mb-4"
                             role="alert">
                            {{ session('success') ?? session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.buyers.store') }}" class="needs-validation"
                          id="addCustomerForm" novalidate>
                        @csrf

                        <!-- Customer Type Toggle -->
                        <div class="mb-4">
                            <h5 class="card-title">Customer Type</h5>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type" id="individual"
                                       value="individual"
                                       {{ old('customer_type', 'individual') == 'individual' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type" id="company"
                                       value="company"
                                       {{ old('customer_type') == 'company' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="company">Company</label>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <div class="mb-4">
                            <h5 class="card-title">Personal Details</h5>
                            <div class="row g-3">
                                <!-- Customer Name -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Customer Name <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           placeholder="Enter full name" value="{{ old('name') }}" required
                                           minlength="2"
                                           maxlength="100">
                                    <div class="invalid-feedback">Customer name is required (2-100 characters).</div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                           placeholder="Enter email" value="{{ old('email') }}"
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Country Code <span class="text-danger">*</span></label>
                                    <select name="country_code" class="form-select select2" required>
                                        @for($i=1;$i<=998;$i++)
                                            <option value="+{{$i}}" {{old('country_code', '+1') == '+'.$i ? 'selected' : null}}>
                                                +{{$i}}</option>
                                        @endfor
                                    </select>
                                    <div class="invalid-feedback">Enter a country code.</div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-2">
                                    <label for="phone" class="form-label">Phone <span
                                                class="text-danger">*</span></label>
                                    <input type="number" name="phone" id="phone" class="form-control"
                                           placeholder="9999999999" value="{{ old('phone') }}" required>
                                    <div class="invalid-feedback">Enter a valid phone number.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <div class="mb-4">
                            <h5 class="card-title">Address Details</h5>
                            <div class="row g-3">
                                <!-- Street Address -->
                                <div class="col-md-12">
                                    <label for="street_address" class="form-label">Street Address <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="street_address" id="street_address" class="form-control"
                                           placeholder="Enter street address" value="{{ old('street_address') }}"
                                           required
                                           minlength="5" maxlength="200">
                                    <div class="invalid-feedback">Street address is required (5-200 characters).</div>
                                </div>

                                <!-- City -->
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="city" class="form-control"
                                           placeholder="Enter city" value="{{ old('city') }}" required minlength="2"
                                           maxlength="100">
                                    <div class="invalid-feedback">City is required (2-100 characters).</div>
                                </div>

                                <!-- Postal Code -->
                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label">Postal Code <span
                                                class="text-danger">*</span></label>
                                    <input type="number" name="postal_code" id="postal_code" class="form-control"
                                           placeholder="400001" value="{{ old('postal_code') }}" required
                                           pattern="\d{6}">
                                    <div class="invalid-feedback">Enter a valid 6-digit postal code.</div>
                                </div>

                                <!-- Country -->
                                <div class="col-md-4">
                                    <label for="country" class="form-label">Country <span
                                                class="text-danger">*</span></label>
                                    <select name="country" id="country" class="form-select select2-supplier" required>
                                        <option value="" {{ old('country') == '' ? 'selected' : '' }}>Select Country
                                        </option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->name}}" {{ old('country', 'Jamaica') == $country->name ? 'selected' : '' }}>
                                                {{$country->name}}</option>
                                        @endforeach

                                        <!-- Add more countries as needed -->
                                    </select>
                                    <div class="invalid-feedback">Country is required.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Details (Hidden by Default) -->
                        <div id="companyDetailsSection" style="display: none;" class="mt-4 mb-4">
                            <h5 class="card-title">Company Details</h5>
                            <div class="row g-3">
                                <!-- Company Name -->
                                <div class="col-md-4">
                                    <label for="company_name" class="form-label">Company Name <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="company_name" id="company_name" class="form-control"
                                           placeholder="Enter company name" value="{{ old('company_name') }}"
                                           minlength="2"
                                           maxlength="100">
                                    <div class="invalid-feedback">Company name is required (2-100 characters).</div>
                                </div>

                                <!-- Company Email -->
                                <div class="col-md-4">
                                    <label for="company_email" class="form-label">Company Email <span
                                                class="text-danger">*</span></label>
                                    <input type="email" name="company_email" id="company_email" class="form-control"
                                           placeholder="company@example.com" value="{{ old('company_email') }}"
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <div class="invalid-feedback">Enter a valid company email.</div>
                                </div>

                                <!-- Company Phone -->
                                <div class="col-md-4">
                                    <label for="company_phone_number" class="form-label">Company Phone <span
                                                class="text-danger">*</span></label>
                                    <input type="number" name="company_phone_number" id=""
                                           class="form-control" placeholder="9999999999"
                                           value="{{ old('company_phone_number') }}">
                                    <div class="invalid-feedback">Enter a valid phone number.
                                    </div>
                                </div>
                                <!-- Tax Number -->
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="mb-4">
                            <h5 class="card-title">Bank Details</h5>
                            <div class="row g-3">
                                <!-- Bank Name -->
                                <div class="col-md-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control"
                                           placeholder="Enter bank name" value="{{ old('bank_name') }}" maxlength="100">
                                    <div class="invalid-feedback">Bank name must be 2-100 characters.</div>
                                </div>

                                <!-- Bank Branch -->
                                <div class="col-md-3">
                                    <label for="bank_branch" class="form-label">Bank Branch</label>
                                    <input type="text" name="bank_branch" id="bank_branch" class="form-control"
                                           placeholder="Enter bank branch" value="{{ old('bank_branch') }}"
                                           maxlength="100">
                                    <div class="invalid-feedback">Bank branch must be 2-100 characters.</div>
                                </div>

                                <!-- Account Number -->
                                <div class="col-md-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control"
                                           placeholder="Enter account number" value="{{ old('account_number') }}"
                                           pattern="\d{9,18}">
                                    <div class="invalid-feedback">Enter a valid account number (9-18 digits).</div>
                                </div>

                                <div class="col-md-2">
                                    <label for="tax" class="form-label">Tax Number (Optional)</label>
                                    <input type="text" name="tax" id="" class="form-control" placeholder="Enter TRN"
                                           value="{{ old('tax') }}" maxlength="50">
                                    <div class="invalid-feedback">Enter a valid tax number (max 50 characters).</div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="myCheckbox">
                                        <label class="form-check-label" for="myCheckbox">Is Loyal</label>
                                    </div>
                                    <!-- Hidden input to hold Yes/No value -->
                                    <input type="hidden" name="is_loyal" id="statusValue" value="No">
                                </div>


                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("addCustomerForm");
            const individualRadio = document.getElementById("individual");
            const companyRadio = document.getElementById("company");
            const companySection = document.getElementById("companyDetailsSection");
            const companyInputs = companySection.querySelectorAll("input");
            const checkbox = document.getElementById("myCheckbox");
            const hiddenInput = document.getElementById("statusValue");

            // Toggle company section visibility and required fields
            function toggleCompanySection() {
                if (companyRadio.checked) {
                    companySection.style.display = "block";
                    companyInputs.forEach(input => {
                        if (input.id !== "tax") {
                            input.required = true;
                            input.disabled = false;
                        }
                    });
                } else {
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

            // Checkbox logic
            checkbox.addEventListener("change", function () {
                hiddenInput.value = this.checked ? "Yes" : "No";
            });

            // Custom Bootstrap validation
            form.addEventListener("submit", function (event) {
                let isValid = true;

                // Check customer type
                if (!individualRadio.checked && !companyRadio.checked) {
                    isValid = false;
                    form.querySelector(".form-check").classList.add("is-invalid");
                } else {
                    form.querySelector(".form-check").classList.remove("is-invalid");
                }

                // Validate company fields only if company type is selected
                if (companyRadio.checked) {
                    companyInputs.forEach(input => {
                        if (input.id !== "tax" && !input.value.trim()) {
                            isValid = false;
                            input.classList.add("is-invalid");
                        } else if (input.id === "company_phone_number" && !input.value.match(/^\+91\s*\d{10}$/)) {
                            isValid = false;
                            input.classList.add("is-invalid");
                        } else if (input.id === "company_email" && !input.value.match(/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/)) {
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