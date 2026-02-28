<style>
    /* Remove Bootstrap valid checkmark */
    .form-control.is-valid,
    .was-validated .form-control:valid {
        background-image: none !important;
        border-color: #ced4da !important;
        padding-right: .75rem !important;
    }
</style>

<!-- jQuery (Select2 depends on it) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.min.js"></script>

@include('layouts.selling.partials.calculate-js')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // -------------------------
        // GLOBAL VARIABLES
        // -------------------------
        let customer_type = "";
        let is_loyal = "";
        const customerRouteTemplate = "{{ route('admin.buyer.get', ':id') }}";
        const productRouteTemplate = "{{ route('admin.product.get', ':id') }}";
        const JmdRate = {{ App\Models\Setting::where('key', 'usd_to_jmd_rate')->first()->value }};


        // -------------------------
        // INITIALIZE SELECT2
        // -------------------------
        function initSelect2(container) {
            $(container).find('.select2-product').select2({
                placeholder: "-- Select Product --",
                allowClear: true,
                width: '100%',
            });

            $(container).find('.select2-customer').select2({
                placeholder: "-- Select Customer --",
                allowClear: true,
                width: '100%',
            });
        }

        initSelect2(document);

        // -------------------------
        // FETCH CUSTOMER DETAILS
        // -------------------------
        $('#customerSelect').on('change', function () {
            const customer_id = $(this).val();
            if (!customer_id) return;

            const route = customerRouteTemplate.replace(':id', customer_id);

            fetch(route)
                .then(res => res.json())
                .then(data => {
                    console.log('Customer data:', data);

                    // Fill fields
                    document.getElementById("customer_address").value = data.street_address || '';
                    document.getElementById("customer_email").value = data.email || '';
                    document.getElementById("customer_phone").value = data.phone || '';

                    // Set global vars
                    customer_type = data.customer_type || "";
                    is_loyal = data.is_loyal || "";

                    // 🔁 Update all product prices dynamically after customer change
                    updateAllProductPrices();
                })
                .catch(err => console.error('Error fetching customer:', err));
        });

        // -------------------------
        // ADD NEW PRODUCT ROW
        // -------------------------
        document.getElementById('addRow').addEventListener('click', function () {
            const tableBody = document.querySelector('#productsTable tbody');

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="product_id[]" class="form-select form-select-sm product-select select2-product" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="weight_quantity[]" min="0" step="0.01"
                class="form-control form-control-sm qty-input" placeholder="Qty" required>
        </td>
        <td>
            <input name="weight_unit_id[]" class="form-control unit-selected d-none" value=""  readonly />
        <input name="weight_unit_name[]" class="form-control unit-name-selected" value=""  readonly />
        </td>
        <td>
            <input type="number" name="rate_per_unit[]" min="0" step="0.01"
                class="form-control form-control-sm price-input" placeholder="Price" readonly required>
        </td>
        <td>
            <input type="number" name="total_amount[]" min="0" step="0.01" readonly
                class="form-control form-control-sm total-input bg-light" placeholder="Total">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow">
                <i class="fas fa-trash"></i>
            </button>
        </td>
`;

            tableBody.appendChild(newRow);
            initSelect2(newRow);
            updateRemoveButtons();
        });

        // -------------------------
        // ENABLE/DISABLE REMOVE BUTTONS
        // -------------------------
        function updateRemoveButtons() {
            console.log('updateRemoveButtons');
            const rows = document.querySelectorAll('#productsTable tbody tr');
            rows.forEach(row => {
                const btn = row.querySelector('.removeRow');
                if (btn) btn.disabled = rows.length === 1;
            });
        }

        // -------------------------
        // REMOVE ROW
        // -------------------------
        // document.addEventListener('click', function (e) {
        //     console.log('addEventListener click');
        //     if (e.target.closest('.removeRow')) {
        //         e.target.closest('tr').remove();
        //         recalcSubtotal();
        //         updateRemoveButtons();
        //     }
        // });

        // -------------------------
        // FETCH PRODUCT DATA ON SELECT
        // -------------------------
        $(document).on('select2:select', '.product-select', function (e) {
            const select = e.target;
            const selectedId = select.value;
            if (!selectedId) return;

            const row = select.closest('tr');
            const url = productRouteTemplate.replace(':id', selectedId);

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    applyPriceToRow(row, data);
                })
                .catch(err => console.error('Fetch error:', err));
        });

        // -------------------------
        // APPLY PRICE TO ROW BASED ON CUSTOMER TYPE
        // -------------------------
        function applyPriceToRow(row, productData) {
            console.log('applyPriceToRow')
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;

            let price = 0;
            if (is_loyal === "Yes") price = parseFloat(productData.loyal_sale_price) || 0;
            else if (customer_type === "company") price = parseFloat(productData.company_sale_price) || 0;
            else price = parseFloat(productData.sale_price) || 0;

            row.querySelector('.price-input').value = price.toFixed(2);
            if (row.querySelector('.unit-selected'))
                row.querySelector('.unit-selected').value = productData.weight_unit_id || '';

            if (row.querySelector('.unit-name-selected'))
                row.querySelector('.unit-name-selected').value = productData.weight_unit_name || '';

            row.querySelector('.total-input').value = (((qty * price).toFixed(2)) / JmdRate).toFixed(2);
            recalcSubtotal();
        }

        // -------------------------
        // UPDATE ALL ROW PRICES WHEN CUSTOMER CHANGES
        // -------------------------
        function updateAllProductPrices() {
            console.log('updateAllProductPrices')
            document.querySelectorAll('#productsTable tbody tr').forEach(row => {
                const select = row.querySelector('.product-select');
                const productId = select ? select.value : null;
                if (!productId) return;

                const url = productRouteTemplate.replace(':id', productId);
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        applyPriceToRow(row, data);
                    })
                    .catch(err => console.error('Error updating product prices:', err));
            });
        }

        // -------------------------
        // QUANTITY OR PRICE INPUT → UPDATE TOTAL
        // -------------------------
        document.addEventListener('input', function (e) {
            console.log('addEventListener input');
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.classList.contains('qty-input') || e.target.classList.contains(
                'price-input')) {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                row.querySelector('.total-input').value = ((((qty * price).toFixed(2))) / JmdRate).toFixed(2);
                recalcSubtotal();
            }
        });

        // -------------------------
        // RECALCULATE SUBTOTAL AND BALANCE
        // -------------------------
        function recalcSubtotal() {
            console.log('recalcSubtotal')
            let subtotal = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });

            const subtotalEl = document.getElementById('subtotal');
            const subtotalDisplay = document.getElementById('subtotalDisplay');
            const payableAmount = document.getElementById('payableamount');

            if (subtotalEl) subtotalEl.value = subtotal.toFixed(2);
            if (payableAmount) payableAmount.value = subtotal.toFixed(2);
            // if (subtotalDisplay) subtotalDisplay.textContent = (subtotal.toFixed(2) / JmdRate).toFixed(2);
            console.log(JmdRate)

            const scaleFeeInput = document.querySelector('.scale-fee');
            const scaleFee = parseFloat(scaleFeeInput ? scaleFeeInput.value : 0) || 0;

            const balance = subtotal - scaleFee;
            if (document.getElementById('balanceDisplay'))
                document.getElementById('balanceDisplay').textContent = balance.toFixed(2);
            if (document.getElementById('feeDisplay'))
                document.getElementById('feeDisplay').textContent = scaleFee.toFixed(2);
        }

        // Initialize on page load
        updateRemoveButtons();
    });
</script>
