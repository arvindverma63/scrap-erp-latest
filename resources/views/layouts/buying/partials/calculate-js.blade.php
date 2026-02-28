<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('orderForm');
        const tableBody = document.querySelector('#productsTable tbody');
        const subtotalInput = document.getElementById('subtotal');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const feeDisplay = document.getElementById('feeDisplay');
        const balanceDisplay = document.getElementById('balanceDisplay');
        const paidAmountInput = document.querySelector('.paid-amount');
        const scaleFeeInput = document.querySelector('.scale-fee');
        const submitBtn = document.getElementById('submitBtn');

        const lessScaleFee = document.getElementById("lessscalefee");
        const haulageFee = document.getElementById("haulage_fee");
        const handlingFee = document.getElementById("handling_fee");
        const totalPayAmount = document.getElementById("payableamount");
        const dueAmount = document.getElementById("balance_amount");
        const paidAmount = document.getElementById("partially_paid");
        // const supplierMsg = document.getElementById("supplierMsg");
        // 🧩 Store the original payable amount for reference
        if (!totalPayAmount.dataset.original) {
            totalPayAmount.dataset.original = totalPayAmount.value || 0;
        }

        // 🧮 Main function to calculate payable, less, and due amounts safely
        function calculateAmounts() {
            const base = parseFloat(totalPayAmount.dataset.original) || 0;
            const less = parseFloat(lessScaleFee.value) || 0;
            const haulage = parseFloat(haulageFee.value) || 0;
            const handling = parseFloat(handlingFee.value) || 0;
            const paid = parseFloat(paidAmount.value) || 0;
            const payable = Math.max(0, base - less - haulage - paid);

            if (base !== 0 && (+paidAmount.value > +totalPayAmount.dataset.original+handling)) {
                paidAmount.className = "";
                paidAmount.classList.add("form-control", "form-control-sm", "border-danger");
                toastr.error(`The entered paid amount (${paidAmount.value}) JMD cannot exceed the total payable amount (${totalPayAmount.dataset.original}) JMD.`);
            } else {
                paidAmount.className = "";
                paidAmount.classList.add("form-control", "form-control-sm", "is-valid");
            }
            // const supplier = document.forms["orderForm"]["supplier_id"];
            // if (!supplier.value) {
            //     supplierMsg.classList = "";
            //     supplierMsg.classList.add("text-sm", "text-danger", "d-block");
            // } else {
            //     supplierMsg.classList = "";
            //     supplierMsg.classList.add("text-sm", "text-danger", "d-none");
            // }

            totalPayAmount.value = payable.toFixed(2);
            dueAmount.value = payable.toFixed(2);
        }

        lessScaleFee.addEventListener("input", calculateAmounts);
        paidAmount.addEventListener("input", calculateAmounts);

        // 🧮 Auto-populate unit price when product is selected
        tableBody.addEventListener('change', function (e) {
            if (e.target.classList.contains('product-select')) {
                const row = e.target.closest('tr');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const priceInput = row.querySelector('.price-input');

                if (price && price !== '') {
                    priceInput.value = parseFloat(price).toFixed(2);
                } else {
                    priceInput.value = '';
                }

                calculateRowTotal(row);
                calculateGrandTotal();
            }
        });

        // 🧾 Calculate row total when qty or price changes
        ['input', 'change'].forEach(eventType => {
            tableBody.addEventListener(eventType, function (e) {
                if (e.target.classList.contains('qty-input') ||
                    e.target.classList.contains('price-input') ||
                    e.target.classList.contains('unit-select')) {
                    const row = e.target.closest('tr');
                    calculateRowTotal(row);
                    calculateGrandTotal();
                }
            });
        });

        // ❌ Remove row
        tableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                const rows = tableBody.querySelectorAll('tr');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                    calculateGrandTotal();
                }
            }
        });

        // ➕ Calculate individual row total
        function calculateRowTotal(row) {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = (qty * price).toFixed(2);
            row.querySelector('.total-input').value = total;
        }

        // 💰 Calculate grand total
        function calculateGrandTotal() {
            let subtotal = 0;
            tableBody.querySelectorAll('tr').forEach(row => {
                const total = parseFloat(row.querySelector('.total-input').value) || 0;
                subtotal += total;
            });

            subtotalInput.value = subtotal.toFixed(2);
            subtotalDisplay.textContent = subtotal.toFixed(2);

            // update the base totalPayAmount (used by less and paid)
            totalPayAmount.dataset.original = subtotal.toFixed(2);
            calculateAmounts(); // update payable & due based on latest subtotal

            // update fee and balance displays
            updateBalance(subtotal);
        }

        // 🧾 Update balance considering paid & fee
        function updateBalance(subtotal) {
            const paid = parseFloat(paidAmountInput.value) || 0;
            const fee = parseFloat(scaleFeeInput.value) || 0;
            const netTotal = subtotal - fee;
            const balance = Math.max(0, netTotal - paid);

            feeDisplay.textContent = fee.toFixed(2);
            balanceDisplay.textContent = balance.toFixed(2);
        }

        // mark paid as user-modified
        paidAmountInput.addEventListener('input', function () {
            paidAmountInput.classList.add('user-modified');
            calculateGrandTotal();
        });

        scaleFeeInput.addEventListener('input', calculateGrandTotal);

        // ✅ Form validation
        form.addEventListener('submit', function (e) {


            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }

            let supplierSelected = document.getElementById('supplierSelect');
            if (!supplierSelected.value) {
                toastr.error('Supplier is required')
                e.preventDefault();
                e.stopPropagation();
            }


            let isValid = true;
            tableBody.querySelectorAll('tr').forEach((row) => {
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

            if (!isValid) {
                e.preventDefault();
                alert('Please fill all product details (Product, Quantity, Unit Price) for each row.');
                return false;
            }

            const transactionId = form.querySelector('[name="transaction_id"]').value.trim();
            if (!transactionId) {
                alert('Receipt Number is required.');
                e.preventDefault();
                return false;
            }

            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        });

        // 💡 Real-time field validation
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

        // Initial calculation
        calculateGrandTotal();
    });
</script>

<!-- Fetch product data dynamically -->
<script>
    const routeTemplate = "{{ route('admin.product.get', ':id') }}";

    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('product-select')) {
            const select = e.target;
            const selectedId = select.value;

            if (!selectedId) return;
            const row = select.closest('tr');
            const url = routeTemplate.replace(':id', selectedId);

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Error fetching product.');
                        return;
                    }

                    row.querySelector('.price-input').value = parseFloat(data.sale_price || 0).toFixed(2);

                    const unitSelect = row.querySelector('.unit-select');
                    if (unitSelect) unitSelect.value = data.weight_unit_id;

                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    row.querySelector('.total-input').value = (qty * parseFloat(data.sale_price || 0)).toFixed(2);
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    alert('Network error while fetching product.');
                });
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target && e.target.classList.contains('qty-input')) {
            const row = e.target.closest('tr');
            const qty = parseFloat(e.target.value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            row.querySelector('.total-input').value = (qty * price).toFixed(2);
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target && e.target.classList.contains('price-input')) {
            const row = e.target.closest('tr');
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(e.target.value) || 0;
            row.querySelector('.total-input').value = (qty * price).toFixed(2);
        }
    });
</script>