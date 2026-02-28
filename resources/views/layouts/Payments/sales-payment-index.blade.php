<x-head title="Payments"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Sales Payments</h3>
                <div class="d-flex gap-2">


                    <!-- Export Dropdown -->
                    <!-- <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv text-success"></i>
                                    Export CSV</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf text-danger"></i>
                                    Export PDF</a></li>
                        </ul>
                    </div> -->
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <form method="GET" class="row mb-0" id="searchForm">
                             <div class="col-md-3">
                                <select class="form-control dropdown" name="cashier_id">
                                    <option value="">All Cashiers</option>
                                    @foreach($cashiers as $cashier)
                                    <option value="{{$cashier->id}}"
                                    {{request('cashier_id') == $cashier->id ? 'selected' : null }}>{{$cashier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Search anything..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-filter me-1"></i> Search
                                </button>
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-light flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive card shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 fs-6">
                    <thead class="table-light">
                    <tr>
                        <th>SNo.</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Cashier</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Payment Mode</th>
                        <th>Status</th>
                        <th>Paid Amount</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $key => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $key}}</td>
                            <td>{{ $payment->invoice->invoice_number ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('m-d-Y') }}</td>
                            <td>{{$payment->invoice->sellingOrder->cashier->name}}</td>
                            <td>{{ $payment->invoice->sellingOrder->customer->name ?? '-' }}</td>
                            <td>{{ $payment->invoice->sellingOrder->customer->email ?? '-' }}</td>
                            <td> @php
                                    $statusClass = match ($payment->invoice->status) {
                                        'Unpaid' => 'bg-warning text-dark',
                                        'Paid' => 'bg-success',
                                        'Cancelled' => 'bg-secondary',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $payment->invoice->status }}</span>
                            </td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ number_format($payment->invoice->grand_total, 2) }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm bg-transparent border-0"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu">

                                        <li>
                                            <a class="dropdown-item" data-bs-toggle="modal"
                                               data-bs-target="#viewSalesPaymentsModal{{ $payment->invoice->sellingOrder->id }}">
                                                <i class="fa-solid fa-list"></i> View Payments
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No payments found.</td>
                        </tr>
                    @endforelse
                    </tbody>


                </table>
                @foreach ($payments as $payment)
                    @include('layouts.Payments.partials.view-sales-payments')
                @endforeach
                <div class="mt-3">
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            const input = document.getElementById('search');
            if (input.value.trim() === '') {
                e.preventDefault();
                input.classList.add('is-invalid');
                input.placeholder = "Enter something to search...";
            } else {
                input.classList.remove('is-invalid');
            }
        });
    </script>

</x-app-layout>
