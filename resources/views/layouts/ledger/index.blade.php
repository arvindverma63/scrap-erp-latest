<x-head title="Ledger" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid" id="sales-container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Something went wrong.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Page Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold text-dark mb-0">Ledger</h3>
            </div>

            <!-- Filters Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.audit.ledger') }}">
                        <div class="col-md-2">
                            <label class="form-label small">From Date</label>
                            <input type="date" name="from_date" class="form-control"
                                value="{{ $filters['from_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">To Date</label>
                            <input type="date" name="to_date" class="form-control"
                                value="{{ $filters['to_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select">
                                <!-- <option value="">All</option> -->
                                <option value="Purchase"
                                    {{ ($filters['type'] ?? '') === 'Purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="Sales" {{ ($filters['type'] ?? '') === 'Sales' ? 'selected' : '' }}>
                                    Sales</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Keyword</label>
                            <input type="text" name="keyword" class="form-control"
                                value="{{ $filters['keyword'] ?? '' }}" placeholder="Customer / Supplier / Amount">
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit">
                                     Filter
                                </button>
                                <a href="{{ route('admin.audit.ledger') }}" class="btn btn-light flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Ledger Table -->
            <div class="card shadow-sm">
                <div class="table-responsive rounded-3 mb-0">
                    <table class="table table-hover align-middle mb-0 fs-6">
                        <thead class="table-light">
                            <tr>
                                <th>Sr.No.</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningBalance = 0; @endphp

                            @forelse ($ledger as $index => $entry)
                                @php
                                    $amount = $entry->total_amount;
                                    $runningBalance +=
                                        $entry->type === 'Sales'
                                            ? $amount // Credit
                                            : -$amount; // Debit
                                @endphp
                                <tr>
                                    <td>{{ $ledger->firstItem() + $index }}</td>
                                    <td>{{ \Carbon\Carbon::parse($entry->created_at)->format('m-d-Y') }}</td>
                                    <td>{{ $entry->cashier->name ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $entry->type === 'Purchase' ? 'bg-danger' : 'bg-success' }}">
                                            {{ $entry->type }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ number_format($amount, 2) }}
                                        @if($entry->type == 'Purchase')
                                            {{\App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value}}
                                        @else
                                            {{\App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                        @endif
                                    </td>
                                    <td>

                                        <a class="dropdown-item py-0 cursor-pointer" data-bs-toggle="modal"
                                            data-bs-target="#orderModal{{ $entry->id }}">
                                            <i class="fa-solid fa-eye"></i> 
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No ledger records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer d-flex justify-content-end">
                    {{ $ledger->links('pagination::bootstrap-5') }}
                </div>
                 <!-- <p class="float-end">{{ $ledger->links('pagination::bootstrap-5') }}</p> -->
            </div>

            @foreach ($ledger as $order)
                @include('layouts.ledger.partials.user-details')
            @endforeach

            @include('layouts.common.footer')

        </div>
    </div>
</x-app-layout>
