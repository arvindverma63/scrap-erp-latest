<!-- Add Supplier Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="alertContainer"></div>

                <form method="POST" action="{{ route('admin.buyers.store') }}" class="needs-validation"
                    id="addCustomerForm" novalidate>
                    @csrf

                    <!-- Supplier Type -->
                    <h5 class="mb-3">Customer Type</h5>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="customer_type" id="individual"
                                value="individual" {{ old('customer_type', 'individual') == 'individual' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="individual">Individual</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="customer_type" id="company"
                                value="company" {{ old('customer_type') == 'company' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="company">Company</label>
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <h5 class="mt-3 mb-3">Personal Details</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter full name"
                                value="{{ old('name') }}" required minlength="2" maxlength="100">
                            <div class="invalid-feedback">Supplier name is required (2–100 characters).</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email"
                                value="{{ old('email') }}" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <div class="invalid-feedback">Enter a valid email address.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="9876543210"
                                value="{{ old('phone') }}" pattern="(\+\d{1,3}\s*)?\d{10}" required>
                            <div class="invalid-feedback">Enter a valid 10-digit phone number.</div>
                        </div>
                    </div>

                    <!-- Address Details -->
                    <h5 class="mt-3 mb-3">Address Details</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="street_address" class="form-control"
                                placeholder="Enter street address" value="{{ old('street_address') }}" required
                                minlength="5" maxlength="200">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" placeholder="Enter city"
                                value="{{ old('city') }}" required minlength="2" maxlength="100">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                            <input type="text" name="postal_code" class="form-control" placeholder="400001"
                                value="{{ old('postal_code') }}" pattern="\d{6}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select name="country" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                <option value="{{$country->name}}" {{ old('country', 'Jamaica') == $country->name ? 'selected' : '' }}>{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Company Details -->
                    <div id="companyDetailsSection" style="display:none;">
                        <h5 class="mt-3 mb-3">Company Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control"
                                    placeholder="Enter company name" value="{{ old('company_name') }}" minlength="2"
                                    maxlength="100">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Company Email <span class="text-danger">*</span></label>
                                <input type="email" name="company_email" class="form-control"
                                    placeholder="company@example.com" value="{{ old('company_email') }}"
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Company Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="company_phone_number" class="form-control"
                                    placeholder="9876543210" value="{{ old('company_phone_number') }}"
                                    pattern="(\+\d{1,3}\s*)?\d{10}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tax Number</label>
                                <input type="text" name="tax" class="form-control" placeholder="Enter TRN"
                                    value="{{ old('tax') }}" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <h5 class="mt-3 mb-3">Bank Details</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Enter bank name"
                                value="{{ old('bank_name') }}" maxlength="100">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <input type="text" name="bank_branch" class="form-control" placeholder="Enter branch"
                                value="{{ old('bank_branch') }}" maxlength="100">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control"
                                placeholder="Enter account number" value="{{ old('account_number') }}"
                                pattern="\d{9,18}">
                        </div>

                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="myCheckbox">
                                <label class="form-check-label" for="myCheckbox">Is Loyal</label>
                            </div>
                            <!-- Hidden input to hold Yes/No value -->
                            <input type="hidden" name="is_loyal" id="statusValue" value="No">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add Supplier
                        </button>
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
        const companyInputs = companySection.querySelectorAll("input, select");
        const alertContainer = document.getElementById("alertContainer");

        // Toggle company section visibility and required fields
        function toggleCompanySection() {
            if (companyRadio.checked) {
                companySection.style.display = "block";
                companyInputs.forEach(input => {
                    if (input.name !== "tax") {
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

        // Custom Bootstrap validation and AJAX submission
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            event.stopPropagation();

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

            if (form.checkValidity() && isValid) {
                // Collect form data
                const formData = new FormData(form);

                fetch("{{ route('admin.buyers.onInvoiceCreate') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"
                    }
                })
                    .then(async response => {
                        const data = await response.json();

                        // Clear any previous alerts
                        alertContainer.innerHTML = '';

                        if (!response.ok) {
                            // Handle validation (422)
                            if (response.status === 422 && data.errors) {
                                let errorList = Object.entries(data.errors)
                                    .map(([field, messages]) => `<li>${messages.join('<br>')}</li>`)
                                    .join('');

                                alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <strong>Validation Error:</strong><br>
                    <ul class="mb-0">${errorList}</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
                            } else {
                                // Other errors (500, etc.)
                                alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    ${data.message || 'An unexpected error occurred.'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
                            }
                            throw new Error('Request failed');
                        }

                        // ✅ Success handling
                        console.log(data);
                        const supplier = data.data;

                        alertContainer.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

                        // ✅ Reset form & close modal
                        form.reset();
                        toggleCompanySection();

                        const modalElement = document.getElementById("addCustomerModal");
                        const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                        modalInstance.hide();

                        // ✅ Add new supplier to dropdown
                        const customerSelect = document.getElementById('customerSelect');
                        const newOption = new Option(supplier.name, supplier.id, true, true);
                        customerSelect.add(newOption);

                        // ✅ Fill supplier info fields
                        document.getElementById('customer_phone').value = supplier.phone ?? '';
                        document.getElementById('customer_email').value = supplier.email ?? '';
                        document.getElementById('customer_address').value = supplier.street_address ?? '';

                        // ✅ Refresh select2
                        if ($(customerSelect).hasClass('select2-customer')) {
                            $(customerSelect).trigger('change');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                    });

            }

            form.classList.add("was-validated");
        }, false);
    });
</script>