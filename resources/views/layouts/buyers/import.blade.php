<x-head title="Products Import" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <div class="row justify-content-center mt-5">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-dark">Import Customer(Buyer) CSV</h4>
                        </div>
                        <div class="card-body">
                            <!-- Form -->
                            <form action="{{ route('admin.customer.import.csv') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <!-- Bootstrap Alert -->
                                @if (session('success') || session('error'))
                                    <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show mb-4"
                                        role="alert">
                                        {{ session('success') ?? session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="csv_file" class="form-label">Choose CSV File</label>
                                    <input type="file" name="file" id="csv_file" class="form-control"
                                        accept=".csv, .xls, .xlsx" required>
                                </div>

                                <button type="submit" class="btn btn-success">Import CSV</button>
                            </form>

                            <!-- Optional download sample CSV -->
                            <hr>
                            <a href="{{asset('assets/csv/customers_sample.csv')}}" class="btn btn-outline-primary btn-sm">Download
                                Sample CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
