<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-rapper">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Customers</h3>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    @can('customer_create')
                        <a href="{{ route('admin.buyers.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Add Customer
                        </a>
                        <a href="{{ route('admin.customers.import') }}" class="btn btn-warning"><i
                                    class="fas fa-user-plus"></i>
                            Import Customers</a>
                    @endcan
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.buyers.index') }}">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="form-control" placeholder="Search Customer by name or email or phone">
                            </div>
                            <div class="col-md-4 text-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('admin.buyers.index') }}"
                                   class="btn btn-light btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- Customers Table -->
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive border-0 mb-0">
                        <table class="table table-hover align-middle mb-0 fs-6" id="datatable_1">
                            <thead class="table-light">
                            <tr>
                                <th>Sr.No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Customer Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($customers as $index => $customer)
                                <tr>
                                    <td>{{ $customers->firstItem() + $index}}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->customer_type }}</td>

                                    <td class="py-1">
                                        <label class="form-check form-switch mb-0">
                                            <input type="checkbox" class="form-check-input customer-status-toggle"
                                                   data-id="{{ $customer->id }}"
                                                    {{ $customer->status == 'active' ? 'checked' : '' }}>
                                        </label>

                                        <div>
                                                <span id="customer-status-badge-{{ $customer->id }}"
                                                      class="badge bg-{{ strtolower($customer->status) == 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($customer->status) }}
                                                </span>
                                        </div>
                                    </td>

                                    <td>
                                        <!-- View Button -->
                                        @include('layouts.buyers.view')
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#viewCustomerModal{{ $customer->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @can('customer_update')
                                            <!-- Edit Button -->
                                            <a class="btn btn-sm btn-outline-success"
                                               href="{{ route('admin.buyers.edit', $customer->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Customer found.</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                    </div>
                    <p class="float-end">{{ $customers->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            $('.customer-status-toggle').on('change', function () {
                let customerId = $(this).data('id');
                let status = $(this).is(':checked') ? 'active' : 'inactive';

                console.log(customerId);
                // Replace placeholder with actual customer ID
                let url = "{{ route('admin.customers.updateStatus', ':customerId') }}";
                url = url.replace(':customerId', customerId);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function (response) {
                        if (response.success) {
                            let badge = $('#customer-status-badge-' + customerId);
                            let customerStatus = $('#customer_status' + customerId);
                            badge.text(status.charAt(0).toUpperCase() + status.slice(1));
                            customerStatus.text(status);
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
</x-app-layout>
