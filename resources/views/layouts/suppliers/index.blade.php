<x-head title="Suppliers"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-rapper">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Scrap Suppliers</h3>
                <div class="d-flex gap-2 mb-2 justify-content-end">
                    @can('suppliers_create')
                        <a href="{{ route('admin.supplier.create') }}" class="btn btn-primary"><i
                                    class="fas fa-user-plus"></i>
                            New Supplier</a>
                    @endcan
                    <a href="{{ route('admin.suppliers.import') }}" class="btn btn-warning"><i
                                class="fas fa-user-plus"></i>
                        Import Supplier</a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.supplier.index') }}">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="form-control" placeholder="Search Supplier by name or email or phone">
                            </div>
                            <div class="col-md-4 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('admin.supplier.index') }}"
                                   class="btn btn-light btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card shadow-sm">
                <div class="card-body table-container border-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover table-bordered align-middle mb-0" id="datatable_1">
                            <thead class="table-light">
                            <tr>
                                <th>Sr.No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Supplier Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($suppliers as $index => $supplier)
                                <tr>
                                    <td>{{ $suppliers->firstItem() + $index}}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->supplier_type }}</td>

                                    <td class="py-1">
                                        <label class="form-check form-switch mb-0">
                                            <input type="checkbox" class="form-check-input status-toggle"
                                                   data-id="{{ $supplier->id }}"
                                                    {{ $supplier->status == 'active' ? 'checked' : '' }}>
                                        </label>

                                        <div>
                                            <span id="status-badge-{{ $supplier->id }}"
                                                  class="badge bg-{{ strtolower($supplier->status) == 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($supplier->status) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#viewSupplierModal{{ $supplier->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @can('suppliers_update')
                                            <a class="btn btn-sm btn-outline-success"
                                               href="{{ route('admin.supplier.edit', $supplier->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No suppliers found.</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                        <p class="float-end">{{ $suppliers->links('pagination::bootstrap-5') }}</p>
                    </div>
                </div>
            </div>


            <!-- View Supplier Modals -->
            @foreach ($suppliers as $supplier)
                <div class="modal fade" id="viewSupplierModal{{ $supplier->id }}" tabindex="-1"
                     aria-labelledby="viewSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewSupplierModalLabel{{ $supplier->id }}">Supplier Details
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $supplier->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $supplier->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $supplier->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier Type</th>
                                        <td>{{ $supplier->supplier_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Street Address</th>
                                        <td>{{ $supplier->street_address }}</td>
                                    </tr>

                                    <tr>
                                        <th>City</th>
                                        <td>{{ $supplier->city }}</td>
                                    </tr>
                                    <tr>
                                        <th>Post Code</th>
                                        <td>{{ $supplier->postal_code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{ $supplier->country }}</td>
                                    </tr>
                                    <tr>
                                        <th>Company</th>
                                        <td>{{ $supplier->company_name ?? '-' }}
                                            ({{ $supplier->company_email ?? '-' }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Bank Details</th>
                                        <td>{{ $supplier->bank_name ?? '-' }} /
                                            {{ $supplier->account_number ?? '-' }} /
                                            {{$supplier->bank_branch}}
                                        </td>
                                    </tr>
                                    @if($supplier->product)
                                        <tr>
                                            <th>Product Category</th>
                                            <td>
                                                @foreach($supplier->product as $key => $product)
                                                    {{ $product->name }}{{count($supplier->product) < $key +1 ? '' : ', '}}
                                                @endforeach

                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Status</th>
                                        <td id="supplier_status{{ $supplier->id }}">{{ $supplier->status ?? '-' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Supplier Modal -->

            @endforeach

            <!-- Custom Styles -->
            @push('styles')
                <style>
                    .table-container {
                        max-height: 500px;
                        overflow-y: auto;
                    }
                </style>
            @endpush


            <script>
                document.getElementById('searchForm').addEventListener('submit', function (e) {
                    const input = document.getElementById('name'); // Correct ID here
                    if (input.value.trim() === '') {
                        e.preventDefault(); // stop form submission
                        input.classList.add('is-invalid');
                        input.placeholder = "Enter something to search...";
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
            </script>


            @include('layouts.common.footer')
        </div>
        <script>
            $(document).ready(function () {
                $('.status-toggle').on('change', function () {
                    let supplierId = $(this).data('id');
                    let status = $(this).is(':checked') ? 'active' : 'inactive';

                    // Generate URL from route with placeholder
                    let url = "{{ route('admin.suppliers.updateStatus', ':id') }}";
                    url = url.replace(':id', supplierId); // Replace placeholder with actual ID

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: status
                        },
                        success: function (response) {
                            if (response.success) {
                                let badge = $('#status-badge-' + supplierId);
                                let supplierStatus = $('#supplier_status' + supplierId);
                                badge.text(status.charAt(0).toUpperCase() + status.slice(1));
                                supplierStatus.text(status);
                                badge.removeClass('bg-success bg-secondary')
                                    .addClass(status === 'active' ? 'bg-success' : 'bg-secondary');
                            }
                        },
                        error: function () {
                            alert('Something went wrong!');
                        }
                    });
                });
            });
        </script>

    </div>
</x-app-layout>