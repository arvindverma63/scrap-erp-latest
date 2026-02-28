<x-app-layout>
    <div class="page-content">

        <div class="modal fade" id="confirmApplyModal" tabindex="-1" aria-labelledby="confirmApplyLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmApplyLabel">Confirm Apply</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to apply these limits to all items?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" id="confirmApply">Yes, Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Stock Alert Settings</h3>
            </div>

            <!-- Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Settings Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <form action="{{ route('admin.stock-alert.update') }}" method="POST" id="bulk-stock-form">
                        @csrf
                        @method('PUT')

                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div>
                                <input type="number" id="setLowLimit" class="form-control form-control-sm"
                                    placeholder="Set Low Limit">
                            </div>
                            <div>
                                <input type="number" id="setHighLimit" class="form-control form-control-sm"
                                    placeholder="Set High Limit">
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="applyToAll">
                                Apply to All
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>SNo.</th>
                                        <th>Product Name</th>
                                        <th>Low Stock Limit</th>
                                        <th>High Stock Limit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $key => $product)
                                        <tr>
                                            <td>
                                                {{$key+1}}
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                <input type="number" name="products[{{ $product->id }}][low_stock_limit]"
                                                    class="form-control form-control-sm low-limit"
                                                    value="{{ $product->low_stock_limit ?? 0 }}" min="0">
                                            </td>
                                            <td>
                                                <input type="number" name="products[{{ $product->id }}][high_stock_limit]"
                                                    class="form-control form-control-sm high-limit"
                                                    value="{{ $product->high_stock_limit ?? 0 }}" min="1">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i> Save All Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk apply script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const applyBtn = document.getElementById('applyToAll');
            const confirmBtn = document.getElementById('confirmApply');
            const modalEl = document.getElementById('confirmApplyModal');
            const modal = new bootstrap.Modal(modalEl);

            // Step 1: Show confirmation modal when Apply to All is clicked
            applyBtn.addEventListener('click', function () {
                modal.show();
            });

            // Step 2: When user confirms, apply the logic
            confirmBtn.addEventListener('click', function () {
                const low = document.getElementById('setLowLimit').value;
                const high = document.getElementById('setHighLimit').value;

                if(high >= low){
                    if (low !== '') {
                        document.querySelectorAll('.low-limit').forEach(el => el.value = low);
                    }
                    if (high !== '') {
                        document.querySelectorAll('.high-limit').forEach(el => el.value = high);
                    }
                }else{
                    alert('Please ensure the high limit is greater than the low limit.');
                }
                // Close modal after applying
                modal.hide();
            });
        });
    </script>

</x-app-layout>