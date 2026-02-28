@include('layouts.buying.partials.calculate-js')

<!-- jQuery (Select2 depends on jQuery) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#supplierSelect').on('change', function (e) {

            // 1️⃣ Get selected value
            var supplier_id = $(this).val();
            console.log('Selected value:', supplier_id);

            // 2️⃣ Prepare dynamic route
            const routeName = "{{ route('admin.supplier.show', ':id') }}";
            const route = routeName.replace(':id', supplier_id);
            console.log('Fetch route:', route);

            // 3️⃣ Example fetch request
            fetch(route)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("supplier_address").value = data.street_address;
                    document.getElementById("supplier_email").value = data.email;
                    document.getElementById("supplier_phone").value = data.phone;
                })
                .catch(error => console.error('Error fetching supplier:', error));
        });
    });
</script>


<script>
    $(document).ready(function () {
        const routeTemplate = "{{ route('admin.product.get', ':id') }}";

        // Initialize Select2 in a specific container
        function initSelect2(container) {
            $(container).find('.select2-product').select2({
                placeholder: "Select Product",
                allowClear: true,
                width: '100%'
            });
            $(container).find('.unit-select').select2({
                placeholder: "Select Unit",
                allowClear: true,
                width: '100%'
            });

            // $(container).find('.select2-supplier').select2({
            //     placeholder: "-- Select Supplier --",
            //     allowClear: true,
            //     width: '100%'
            // });

            // $(container).find('.select2').select2({
            //     placeholder: "-- Select Supplier --",
            //     allowClear: true,
            //     width: '100%'
            // });
        }

        // Initialize Select2 on page load
        initSelect2(document);

        // Add new row dynamically (no clone)
        $('#addRow').on('click', function () {
            const $tableBody = $('#productsTable tbody');

            const newRow = `
            <tr>
                <td>
                    <select name="product_id[]" class="form-select form-select-sm product-select select2-product" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
            <option value="{{ $product->id }}" data-price="{{ $product->price ?? '' }}">
                                {{ $product->name }}
            </option>
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
            <input type="number" name="total_amount[]" min="0" step="0.01"
                class="form-control form-control-sm total-input bg-light" placeholder="Total" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
`;

            $tableBody.append(newRow);
            initSelect2($tableBody.find('tr:last'));
            updateRemoveButtons();
        });

        // Enable or disable remove buttons
        function updateRemoveButtons() {
            const $rows = $('#productsTable tbody tr');
            $rows.each(function () {
                $(this).find('.removeRow').prop('disabled', $rows.length === 1);
            });
        }

        // Remove row
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            recalcSubtotal();
            updateRemoveButtons();
        });

        // When product selected → fetch details
        $(document).on('select2:select', '.product-select', function () {
            const selectedId = $(this).val();
            if (!selectedId) return;

            const $row = $(this).closest('tr');
            const url = routeTemplate.replace(':id', selectedId);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    const price = parseFloat(data.purchase_price || 0);
                    const qty = parseFloat($row.find('.qty-input').val()) || 0;

                    $row.find('.price-input').val(price.toFixed(2));
                    $row.find('.unit-selected').val(data.weight_unit_id || '').trigger(
                        'change');
                    $row.find('.unit-name-selected').val(data.weight_unit_name || '').trigger(
                        'change');
                    $row.find('.total-input').val((qty * price).toFixed(2));

                    recalcSubtotal();
                },
                error: function (xhr) {
                    console.error('Fetch error:', xhr);
                }
            });
        });

        // Quantity or price change → recalc total
        $(document).on('input', '.qty-input, .price-input', function () {
            const $row = $(this).closest('tr');
            const qty = parseFloat($row.find('.qty-input').val()) || 0;
            const price = parseFloat($row.find('.price-input').val()) || 0;
            $row.find('.total-input').val((qty * price).toFixed(2));
            recalcSubtotal();
        });

        // Scale fee or haulage fee input → recalc totals
        $('#lessscalefee, #haulage_fee, #handling_fee').on('input', recalcSubtotal);

        // Recalculate subtotal and payable amount
        function recalcSubtotal() {
            let subtotal = 0;

            // Sum all line totals
            $('.total-input').each(function () {
                subtotal += parseFloat($(this).val()) || 0;
            });

            // Update subtotal fields
            $('#subtotal').val(subtotal.toFixed(2));
            $('#subtotalDisplay').text(subtotal.toFixed(2));

            // Get both fees
            const scaleFee = parseFloat($('#lessscalefee').val()) || 0;
            const haulageFee = parseFloat($('#haulage_fee').val()) || 0;
            const handlingFee = parseFloat($('#handling_fee').val()) || 0;
            $('#partially_paid').val(0);

            // Subtract both from subtotal
            const balance = subtotal - scaleFee - haulageFee + handlingFee;

            // Update UI
            $('#payableamount').val(balance.toFixed(2));
            $('#balanceDisplay').text(balance.toFixed(2));
            $('#feeDisplay').text(scaleFee.toFixed(2));
            $('#balance_amount').val(balance.toFixed(2));
        }


        // Initialize remove buttons on load
        updateRemoveButtons();
    });
</script>
