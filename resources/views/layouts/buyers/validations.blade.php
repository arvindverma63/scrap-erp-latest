<script>
    // Create Form Validation
    document.getElementById('createCustomerForm').addEventListener('submit', function (event) {
        let isValid = true;
        const requiredFields = [
            { id: 'name', message: 'Name is required.' },
            { id: 'email', message: 'A valid email is required.', validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) },
            { id: 'phone', message: 'Phone number is required.' },
            { id: 'address', message: 'Address is required.' },
            { id: 'status', message: 'Status is required.' }
        ];
        const optionalEmailFields = [
            { id: 'company_email', message: 'A valid company email is required.' }
        ];

        requiredFields.forEach(field => {
            const input = document.querySelector(`#createCustomerForm [name="${field.id}"]`);
            const feedback = input.nextElementSibling;
            input.classList.remove('is-invalid');
            feedback.textContent = '';

            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                feedback.textContent = field.message;
                isValid = false;
            } else if (field.validate && !field.validate(input.value)) {
                input.classList.add('is-invalid');
                feedback.textContent = field.message;
                isValid = false;
            }
        });

        // Validate optional email fields if provided
        optionalEmailFields.forEach(field => {
            const input = document.querySelector(`#createCustomerForm [name="${field.id}"]`);
            const feedback = input.nextElementSibling;
            input.classList.remove('is-invalid');
            feedback.textContent = '';
            if (input.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
                input.classList.add('is-invalid');
                feedback.textContent = field.message;
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    // Clear validation on input change for create form
    document.querySelectorAll('#createCustomerForm input, #createCustomerForm select').forEach(input => {
        input.addEventListener('input', function () {
            this.classList.remove('is-invalid');
            const feedback = this.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        });
    });

    // Edit Form Validation (for all edit forms)
    document.querySelectorAll('[id^="editCustomerForm-"]').forEach(form => {
        form.addEventListener('submit', function (event) {
            let isValid = true;
            const requiredFields = [
                { id: 'name', message: 'Name is required.' },
                { id: 'email', message: 'A valid email is required.', validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) },
                { id: 'phone', message: 'Phone number is required.' },
                { id: 'address', message: 'Address is required.' },
                { id: 'status', message: 'Status is required.' }
            ];
            const optionalEmailFields = [
                { id: 'company_email', message: 'A valid company email is required.' }
            ];

            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.id}"]`);
                const feedback = input.nextElementSibling;
                input.classList.remove('is-invalid');
                feedback.textContent = '';

                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    feedback.textContent = field.message;
                    isValid = false;
                } else if (field.validate && !field.validate(input.value)) {
                    input.classList.add('is-invalid');
                    feedback.textContent = field.message;
                    isValid = false;
                }
            });

            // Validate optional email fields if provided
            optionalEmailFields.forEach(field => {
                const input = form.querySelector(`[name="${field.id}"]`);
                const feedback = input.nextElementSibling;
                input.classList.remove('is-invalid');
                feedback.textContent = '';
                if (input.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
                    input.classList.add('is-invalid');
                    feedback.textContent = field.message;
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        // Clear validation on input change for edit form
        form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', function () {
                this.classList.remove('is-invalid');
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = '';
                }
            });
        });
    });

    // Populate View Modal
    document.querySelectorAll('.view-customer-btn').forEach(button => {
        button.addEventListener('click', function () {
            const customer = {
                name: this.getAttribute('data-name'),
                email: this.getAttribute('data-email'),
                phone: this.getAttribute('data-phone'),
                address: this.getAttribute('data-address'),
                company_name: this.getAttribute('data-company_name') || 'N/A',
                company_email: this.getAttribute('data-company_email') || 'N/A',
                company_address: this.getAttribute('data-company_address') || 'N/A',
                company_phone_number: this.getAttribute('data-company_phone_number') || 'N/A',
                tax_no: this.getAttribute('data-tax_no') || 'N/A',
                bank_name: this.getAttribute('data-bank_name') || 'N/A',
                bank_branch: this.getAttribute('data-bank_branch') || 'N/A',
                account_number: this.getAttribute('data-account_number') || 'N/A',
                swift_code: this.getAttribute('data-swift_code') || 'N/A',
                status: this.getAttribute('data-status')
            };

            document.querySelector('#viewCustomerModal .view-name').textContent = customer.name;
            document.querySelector('#viewCustomerModal .view-email').textContent = customer.email;
            document.querySelector('#viewCustomerModal .view-phone').textContent = customer.phone;
            document.querySelector('#viewCustomerModal .view-address').textContent = customer.address;
            document.querySelector('#viewCustomerModal .view-company_name').textContent = customer.company_name;
            document.querySelector('#viewCustomerModal .view-company_email').textContent = customer.company_email;
            document.querySelector('#viewCustomerModal .view-company_phone_number').textContent = customer.company_phone_number;
            document.querySelector('#viewCustomerModal .view-company_address').textContent = customer.company_address;
            document.querySelector('#viewCustomerModal .view-tax_no').textContent = customer.tax_no;
            document.querySelector('#viewCustomerModal .view-bank_name').textContent = customer.bank_name;
            document.querySelector('#viewCustomerModal .view-bank_branch').textContent = customer.bank_branch;
            document.querySelector('#viewCustomerModal .view-account_number').textContent = customer.account_number;
            document.querySelector('#viewCustomerModal .view-swift_code').textContent = customer.swift_code;
            document.querySelector('#viewCustomerModal .view-status').textContent = customer.status.charAt(0).toUpperCase() + customer.status.slice(1);
        });
    });
</script>