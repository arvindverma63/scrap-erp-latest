<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2 for all modals
        function initSelect2(container, modalId) {
            $(container).find(`.select2-product-${modalId}`).select2({
                placeholder: "-- Select Product --",
                allowClear: true,
                width: '100%',
                dropdownParent: $(`#updateOrderModal${modalId}`)
            });

            $(container).find(`.select2-supplier-${modalId}`).select2({
                placeholder: "-- Select Supplier --",
                allowClear: true,
                width: '100%',
                dropdownParent: $(`#updateOrderModal${modalId}`)
            });
        }

        // Initialize Select2 for existing modals
        document.querySelectorAll('[id^="updateOrderModal"]').forEach(modal => {
            const modalId = modal.id.replace('updateOrderModal', '');
            initSelect2(document, modalId);
        });

        // Supplier select change
        document.querySelectorAll('[id^="supplierSelect"]').forEach(select => {
            const modalId = select.id.replace('supplierSelect', '');
            $(select).on('select2:select', function (e) {
                const supplierId = this.value;
                if (!supplierId) {
                    document.getElementById(`supplier_phone${modalId}`).value = '';
                    document.getElementById(`supplier_email${modalId}`).value = '';
                    document.getElementById(`supplier_address${modalId}`).value = '';
                    return;
                }

                const route = "{{ route('admin.supplier.show', ':id') }}".replace(':id', supplierId);
                fetch(route)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById(`supplier_phone${modalId}`).value = data.phone || '';
                        document.getElementById(`supplier_email${modalId}`).value = data.email || '';
                        document.getElementById(`supplier_address${modalId}`).value = data.address || '';
                    })
                    .catch(error => console.error('Error fetching supplier:', error));
            });
        });

        // Add row functionality
        document.querySelectorAll('[id^="addRow"]').forEach(btn => {
            const modalId = btn.id.replace('addRow', '');
            btn.addEventListener('click', function () {
                const tableBody = document.querySelector(`#productsTableBody${modalId}`);
                const firstRow = tableBody.querySelector('tr');

                // Destroy Select2 before cloning
                firstRow.querySelectorAll('select').forEach(select => {
                    if ($(select).hasClass('select2-hidden-accessible')) {
                        $(select).select2('destroy');
                    }
                });

                const newRow = firstRow.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelectorAll('select').forEach(select => select.value = '');
                newRow.querySelector('.removeRow').disabled = false;
                tableBody.appendChild(newRow);

                // Re-initialize Select2
                initSelect2(newRow, modalId);
                initSelect2(firstRow, modalId);
                updateRemoveButtons(modalId);
            });
        });

        // Update remove buttons
        function updateRemoveButtons(modalId) {
            const rows = document.querySelectorAll(`#productsTableBody${modalId} tr`);
            rows.forEach(row => {
                const btn = row.querySelector('.removeRow');
                if (btn) btn.disabled = rows.length === 1;
                btn.onclick = function () {
                    if (rows.length > 1) {
                        row.remove();
                        calculateGrandTotal(modalId);
                        updateRemoveButtons(modalId);
                    }
                };
            });
        }

        // Product select change
        $(document).on('select2:select', `[class*="select2-product-"]`, function (e) {
            const select = e.target;
            const modalId = select.className.match(/select2-product-(\d+)/)[1];
            const selectedId = select.value;
            if (!selectedId) return;

            const row = select.closest('tr');
            const url = "{{ route('admin.product.get', ':id') }}".replace(':id', selectedId);

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Error fetching product.');
                        return;
                    }
                    row.querySelector('.price-input').value = parseFloat(data.sale_price || 0).toFixed(2);
                    const unitSelect = row.querySelector('.unit-select');
                    if (unitSelect) unitSelect.value = data.weight_unit_id || '';
                    calculateRowTotal(row, modalId);
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    alert('Network error while fetching product.');
                });
        });

        // Quantity or price change
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                const row = e.target.closest('tr');
                const modalId = row.closest('table').id.replace('productsTable', '');
                calculateRowTotal(row, modalId);
            }
        });

        // Scale fee change
        document.querySelectorAll('[id^="lessscalefee"]').forEach(input => {
            const modalId = input.id.replace('lessscalefee', '');
            input.addEventListener('input', () => calculateGrandTotal(modalId));
        });

        // Calculate row total
        function calculateRowTotal(row, modalId) {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            row.querySelector('.total-input').value = total.toFixed(2);
            calculateGrandTotal(modalId);
        }

        // Calculate grand total
        function calculateGrandTotal(modalId) {
            let subtotal = 0;
            document.querySelectorAll(`#productsTableBody${modalId} .total-input`).forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });
            const subtotalInput = document.getElementById(`subtotal${modalId}`);
            const subtotalDisplay = document.getElementById(`subtotalDisplay${modalId}`);
            const scaleFeeInput = document.getElementById(`lessscalefee${modalId}`);
            const payableAmountInput = document.getElementById(`payableamount${modalId}`);
            const feeDisplay = document.getElementById(`feeDisplay${modalId}`);
            const balanceDisplay = document.getElementById(`balanceDisplay${modalId}`);

            subtotalInput.value = subtotal.toFixed(2);
            subtotalDisplay.textContent = subtotal.toFixed(2);

            const scaleFee = parseFloat(scaleFeeInput.value) || 0;
            const payable = subtotal - scaleFee;
            payableAmountInput.value = payable.toFixed(2);
            feeDisplay.textContent = scaleFee.toFixed(2);
            balanceDisplay.textContent = payable.toFixed(2);
        }

        // Form validation
        document.querySelectorAll('[id^="orderForm"]').forEach(form => {
            const modalId = form.id.replace('orderForm', '');
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                let isValid = true;
                document.querySelectorAll(`#productsTableBody${modalId} tr`).forEach(row => {
                    const productSelect = row.querySelector('.product-select');
                    const qtyInput = row.querySelector('.qty-input');
                    const priceInput = row.querySelector('.price-input');

                    [productSelect, qtyInput, priceInput].forEach(field => {
                        if (!field.value) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                            field.classList.add('is-valid');
                        }
                    });
                });

                const invoiceNumber = form.querySelector('[name="invoice_number"]').value.trim();
                if (!invoiceNumber) {
                    alert('Receipt Number is required.');
                    e.preventDefault();
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill all product details (Product, Quantity, Unit Price) for each row.');
                    return;
                }

                const submitBtn = document.getElementById(`submitBtn${modalId}`);
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            });

            // Real-time validation feedback
            form.addEventListener('input', function (e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
                    if (e.target.required && !e.target.value) {
                        e.target.classList.add('is-invalid');
                    } else {
                        e.target.classList.remove('is-invalid');
                        e.target.classList.add('is-valid');
                    }
                }
            });
        });

        // Initialize calculations for all modals
        document.querySelectorAll('[id^="updateOrderModal"]').forEach(modal => {
            const modalId = modal.id.replace('updateOrderModal', '');
            calculateGrandTotal(modalId);
            updateRemoveButtons(modalId);
        });
    });
</script>

<!-- jQuery (Select2 depends on jQuery) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.min.js"></script>