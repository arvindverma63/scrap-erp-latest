<div class="modal fade" id="editCustomerModal-{{ $order->customer->id }}" tabindex="-1"
    aria-labelledby="editCustomerModalLabel-{{ $order->customer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel-{{ $order->customer->id }}">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.buyers.update', $order->customer->id) }}"
                    class="needs-validation" id="editCustomerForm-{{ $order->customer->id }}" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Supplier Type Toggle -->
                    <div class="mb-4">
                        <h5 class="card-title">Customer Type</h5>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="customer_type"
                                id="individual_{{ $order->customer->id }}" value="individual"
                                {{ old('customer_type', $order->customer->customer_type) == 'individual' ? 'checked' : '' }}>
                            <label class="form-check-label" for="individual_{{ $order->customer->id }}">Individual</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="customer_type"
                                id="company_{{ $order->customer->id }}" value="company"
                                {{ old('customer_type', $order->customer->customer_type) == 'company' ? 'checked' : '' }}>
                            <label class="form-check-label" for="company_{{ $order->customer->id }}">Company</label>
                        </div>
                        <div class="invalid-feedback">Please select a Customer type.</div>
                    </div>

                    <!-- Personal Details -->
                    <div class="mb-4">
                        <h5 class="card-title mt-4 mb-4">Personal Details</h5>
                        <div class="row g-3">
                            <!-- Supplier Name -->
                            <div class="col-md-4">
                                <label for="name_{{ $order->customer->id }}" class="form-label">Customer Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name_{{ $order->customer->id }}" class="form-control"
                                    placeholder="Enter full name" value="{{ old('name', $order->customer->name) }}" required>
                                <div class="invalid-feedback">Customer name is required.</div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-4">
                                <label for="email_{{ $order->customer->id }}" class="form-label">Email</label>
                                <input type="email" name="email" id="email_{{ $order->customer->id }}"
                                    class="form-control" placeholder="Enter email"
                                    value="{{ old('email', $order->customer->email) }}">
                                <div class="invalid-feedback">Enter a valid email address.</div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-4">
                                <label for="phone_{{ $order->customer->id }}" class="form-label">Phone <span
                                        class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone_{{ $order->customer->id }}"
                                    class="form-control" placeholder="+91 0000000000"
                                    value="{{ old('phone', $order->customer->phone) }}" required pattern="^\+91\s*\d{10}$">
                                <div class="invalid-feedback">Enter a valid phone number (e.g., +91 9876543210).</div>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="mt-3 mb-3">
                            <h6 class="mb-3">Address Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="street_address_{{ $order->customer->id }}" class="form-label">Street
                                        Address</label>
                                    <input type="text" name="street_address" id="street_address_{{ $order->customer->id }}"
                                        class="form-control" placeholder="Enter street address"
                                        value="{{ old('street_address', $order->customer->street_address) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="city_{{ $order->customer->id }}" class="form-label">City</label>
                                    <input type="text" name="city" id="city_{{ $order->customer->id }}"
                                        class="form-control" placeholder="Enter city"
                                        value="{{ old('city', $order->customer->city) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="postcode_{{ $order->customer->id }}" class="form-label">Postal Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="postal_code" id="postcode_{{ $order->customer->id }}"
                                        class="form-control" placeholder="400001"
                                        value="{{ old('postal_code', $order->customer->postcode) }}" required pattern="\d{6}">
                                    <div class="invalid-feedback">Enter a valid 6-digit postal code.</div>
                                </div>
                                <div class="col-md-3">
                                    <label for="country_{{ $order->customer->id }}" class="form-label">Country</label>
                                    <select name="country" id="country_{{ $order->customer->id }}" class="form-select">
                                        <option value=""
                                            {{ old('country', $order->customer->country) == '' ? 'selected' : '' }}>Select
                                            Country</option>
                                        <option value="India"
                                            {{ old('country', $order->customer->country) == 'India' ? 'selected' : '' }}>India
                                        </option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Details -->
                    <div id="companyDetailsSection{{ $order->customer->id }}">
                        <h5 class="card-title">Company Details</h5>
                        <div class="row g-3">
                            <!-- Company Name -->
                            <div class="col-md-6">
                                <label for="company_name_{{ $order->customer->id }}" class="form-label">Company Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="company_name" id="company_name_{{ $order->customer->id }}"
                                    class="form-control" placeholder="Enter company name"
                                    value="{{ old('company_name', $order->customer->company_name) }}">
                                <div class="invalid-feedback">Company name is required.</div>
                            </div>

                            <!-- Company Email -->
                            <div class="col-md-6">
                                <label for="company_email_{{ $order->customer->id }}" class="form-label">Company Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="company_email" id="company_email_{{ $order->customer->id }}"
                                    class="form-control" placeholder="company@example.com"
                                    value="{{ old('company_email', $order->customer->company_email) }}">
                                <div class="invalid-feedback">Enter a valid company email.</div>
                            </div>

                            <!-- Company Phone -->
                            <div class="col-md-6">
                                <label for="company_phone_number_{{ $order->customer->id }}" class="form-label">Company
                                    Phone</label>
                                <input type="tel" name="company_phone_number"
                                    id="company_phone_number_{{ $order->customer->id }}" class="form-control"
                                    placeholder="+91 9876543210"
                                    value="{{ old('company_phone_number', $order->customer->company_phone_number) }}"
                                    pattern="^\+91\s*\d{10}$">
                                <div class="invalid-feedback">Enter a valid phone number.</div>
                            </div>
                            <!-- Tax Number -->
                            <div class="col-md-3">
                                <label for="tax_{{ $order->customer->id }}" class="form-label">Tax Number (Optional)</label>
                                <input type="text" name="tax" id="tax_{{ $order->customer->id }}"
                                    class="form-control" placeholder="Enter TRN"
                                    value="{{ old('tax', $order->customer->tax) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="mb-4 mt-4">
                        <h5 class="card-title">Bank Details</h5>
                        <div class="row g-3">

                            <!-- Bank Name -->
                            <div class="col-md-3">
                                <label for="bank_name_{{ $order->customer->id }}" class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name_{{ $order->customer->id }}"
                                    class="form-control" placeholder="Enter bank name"
                                    value="{{ old('bank_name', $order->customer->bank_name) }}">
                            </div>

                            <!-- Bank Branch -->
                            <div class="col-md-3">
                                <label for="bank_branch_{{ $order->customer->id }}" class="form-label">Bank Branch</label>
                                <input type="text" name="bank_branch" id="bank_branch_{{ $order->customer->id }}"
                                    class="form-control" placeholder="Enter bank branch"
                                    value="{{ old('bank_branch', $order->customer->bank_branch) }}">
                            </div>

                            <!-- Account Number -->
                            <div class="col-md-3">
                                <label for="account_number_{{ $order->customer->id }}" class="form-label">Account
                                    Number</label>
                                <input type="text" name="account_number" id="account_number_{{ $order->customer->id }}"
                                    class="form-control" placeholder="Enter account number"
                                    value="{{ old('account_number', $order->customer->account_number) }}">
                            </div>
                        </div>
                    </div>

                  

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("[id^='editCustomerModal-']").forEach(function(modal) {
            modal.addEventListener("shown.bs.modal", function() {
                const customerId = modal.id.replace("editCustomerModal-", "");
                const individualRadio = document.getElementById("individual_" + customerId);
                const companyRadio = document.getElementById("company_" + customerId);
                const companySection = document.getElementById("companyDetailsSection" + customerId);

                if (!individualRadio || !companyRadio || !companySection) {
                    console.warn("Missing elements for customer ID:", customerId);
                    return;
                }

                function toggleCompanySection() {
                    companySection.style.display = companyRadio.checked ? "block" : "none";
                }

                toggleCompanySection();
                individualRadio.addEventListener("change", toggleCompanySection);
                companyRadio.addEventListener("change", toggleCompanySection);
            });
        });
    });
</script>
