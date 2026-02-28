<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <h3 class="mb-0">Edit Customer</h3>
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
                    <form method="POST" action="{{ route('admin.buyers.update', $customer->id) }}"

                          class="needs-validation" id="editCustomerForm" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Supplier Type Toggle -->
                        <div class="mb-4">
                            <h5 class="card-title">Customer Type</h5>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type"
                                       id="individual" value="individual"
                                        {{ old('customer_type', $customer->customer_type) == 'individual' ? 'checked' : '' }}>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type"
                                       id="company" value="company"
                                        {{ old('customer_type', $customer->customer_type) == 'company' ? 'checked' : '' }}>
                                <label class="form-check-label" for="company">Company</label>
                            </div>
                            <div class="invalid-feedback">Please select a Customer type.</div>
                        </div>

                        <!-- Personal Details -->
                        <div class="mb-4">
                            <h5 class="card-title mt-4 mb-4">Personal Details</h5>
                            <div class="row g-3">
                                <!-- Supplier Name -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Customer Name <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           placeholder="Enter full name" value="{{ old('name', $customer->name) }}"
                                           required>
                                    <div class="invalid-feedback">Customer name is required.</div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email"
                                           class="form-control" placeholder="Enter email"
                                           value="{{ old('email', $customer->email) }}">
                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-2">
                                    <label class="form-label">Country Code <span class="text-danger">*</span></label>
                                    <select name="country_code" class="form-select select2" required>
                                        @for($i=1;$i<=998;$i++)
                                            <option value="{{$i}}" {{$customer->country_code == $i ? 'selected' : null}}>
                                                +{{$i}}</option>
                                        @endfor
                                    </select>
                                    <div class="invalid-feedback">Enter a country code.</div>
                                </div>

                                <div class="col-md-2">
                                    <label for="phone" class="form-label">Phone <span
                                                class="text-danger">*</span></label>
                                    <input type="number" max="10" name="phone" id="phone"
                                           class="form-control" placeholder="9999999999"
                                           value="{{ old('phone', $customer->phone) }}" required
                                           pattern="^\+91\s*\d{10}$">
                                    <div class="invalid-feedback">Enter a valid phone number.</div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="mt-3 mb-3">
                                <h6 class="mb-3">Address Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="street_address" class="form-label">Street
                                            Address</label>
                                        <input type="text" name="street_address" id="street_address"
                                               class="form-control" placeholder="Enter street address"
                                               value="{{ old('street_address', $customer->street_address) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" name="city" id="city"
                                               class="form-control" placeholder="Enter city"
                                               value="{{ old('city', $customer->city) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="postcode" class="form-label">Postal Code <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" name="postal_code" id="postcode"
                                               class="form-control" placeholder="111111"
                                               value="{{ old('postcode', $customer->postal_code) }}" required>
                                        <div class="invalid-feedback">Enter a valid 6-digit postal code.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="country" class="form-label">Country</label>
                                        <select name="country" id="country"
                                                class="form-select select2">
                                            <option value=""
                                                    {{ old('country', $customer->country) == '' ? 'selected' : '' }}>
                                                Select
                                                Country
                                            </option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" {{ old('country', $customer->country) == $country->name ? 'selected' : '' }}>
                                                    {{$country->name}}</option>
                                            @endforeach
                                            <!-- Add more countries as needed -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Details -->
                        <div id="companyDetailsSection">
                            <h5 class="card-title">Company Details</h5>
                            <div class="row g-3">
                                <!-- Company Name -->
                                <div class="col-md-6">
                                    <label for="company_name" class="form-label">Company Name <span
                                                class="text-danger">*</span></label>
                                    <input type="text" name="company_name" id="company_name"
                                           class="form-control" placeholder="Enter company name"
                                           value="{{ old('company_name', $customer->company_name) }}">
                                    <div class="invalid-feedback">Company name is required.</div>
                                </div>

                                <!-- Company Email -->
                                <div class="col-md-6">
                                    <label for="company_email" class="form-label">Company Email
                                        <span
                                                class="text-danger">*</span></label>
                                    <input type="email" name="company_email" id="company_email"
                                           class="form-control" placeholder="company@example.com"
                                           value="{{ old('company_email', $customer->company_email) }}">
                                    <div class="invalid-feedback">Enter a valid company email.</div>
                                </div>

                                <!-- Company Phone -->
                                <div class="col-md-6">
                                    <label for="company_phone_number" class="form-label">Company
                                        Phone</label>
                                    <input type="tel" name="company_phone_number"
                                           id="company_phone_number" class="form-control"
                                           placeholder="9999999999"
                                           value="{{ old('company_phone_number', $customer->company_phone_number) }}">
                                    <div class="invalid-feedback">Enter a valid phone number.</div>
                                </div>
                                <!-- Tax Number -->

                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="mb-4 mt-4">
                            <h5 class="card-title">Bank Details</h5>
                            <div class="row g-3">

                                <!-- Bank Name -->
                                <div class="col-md-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name"
                                           class="form-control" placeholder="Enter bank name"
                                           value="{{ old('bank_name', $customer->bank_name) }}">
                                </div>

                                <!-- Bank Branch -->
                                <div class="col-md-3">
                                    <label for="bank_branch" class="form-label">Bank Branch</label>
                                    <input type="text" name="bank_branch" id="bank_branch"
                                           class="form-control" placeholder="Enter bank branch"
                                           value="{{ old('bank_branch', $customer->bank_branch) }}">
                                </div>

                                <!-- Account Number -->
                                <div class="col-md-3">
                                    <label for="account_number" class="form-label">Account
                                        Number</label>
                                    <input type="text" name="account_number" id="account_number"
                                           class="form-control" placeholder="Enter account number"
                                           value="{{ old('account_number', $customer->account_number) }}">
                                </div>

                                <div class="col-md-2">
                                    <label for="tax" class="form-label">Tax Number
                                        (Optional)</label>
                                    <input type="text" name="tax" id="tax"
                                           class="form-control" placeholder="Enter TRN"
                                           value="{{ old('tax', $customer->tax) }}">
                                </div>

                                <!-- ✅ Is Loyal Checkbox -->
                                <div class="col-md-1 d-flex align-items-center">
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox"
                                               id="isLoyalCheckbox"
                                                {{ old('is_loyal', $customer->is_loyal) == 'Yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isLoyalCheckbox">
                                            Is Loyal
                                        </label>
                                    </div>
                                    <!-- Hidden field to send Yes/No -->
                                    <input type="hidden" name="is_loyal" id="isLoyalValue"
                                           value="{{ old('is_loyal', $customer->is_loyal ?? 'No') }}">
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const checkbox = document.getElementById('isLoyalCheckbox');
                                        const hiddenInput = document.getElementById('isLoyalValue');

                                        checkbox.addEventListener('change', function () {
                                            hiddenInput.value = this.checked ? 'Yes' : 'No';
                                        });
                                    });
                                </script>
                            </div>
                        </div>


                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">Update Customer</button>
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
                        // if (input.id !== "tax") {
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