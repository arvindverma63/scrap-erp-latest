<x-app-layout>

    <!-- Deposit Modal -->
    <div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Add Cash Count</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.wallets.create-cash-count') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="currencyTable">
                                <thead class="table-light">
                                <tr>
                                    <th>Currency (JMD)</th>
                                    <th>Count</th>
                                    {{--                                    <th style="width: 50px;">Action</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $currencies = \App\Models\Setting::where('key', 'currency_count')->first()->value;
                                    $currencies = json_decode($currencies, true);
                                @endphp
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td>
                                            <input type="text" name="currency[]" value="{{$currency}}"
                                                   class="form-control currency"
                                                   placeholder="e.g. 2000 or USD" readonly required>
                                        </td>
                                        <td>
                                            <input type="number" name="count[]" value="" class="form-control count"
                                                   placeholder="Count"
                                                   min="0" required>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td>Total Count</td>
                                    <td><p id="totalCount"></p></td>
                                </tr>
                                <tr>
                                    <td>Total Amount</td>
                                    <td><p id="totalAmount"></p></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <textarea name="remarks" class="col-12 form-input"
                                                  placeholder="Write Remarks..."></textarea>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Generate & Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Filters Card -->


    <div class="page-content">

        <div class="container-fluid role-rapper">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Cash Count</h3>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    @can('wallet_deposit')
                        <!-- Deposit Button -->
                        @if(auth()->user()->roles->first()->id == 2)
                            @if($todayCashCountExist > 0)
                                <button type="button" class="btn btn-secondary" style="cursor: not-allowed">
                                    <i class="fas fa-plus-circle"></i> Create Cash Count
                                </button>
                            @else
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#depositModal">
                                    <i class="fas fa-plus-circle"></i> Create Cash Count
                                </button>
                            @endif
                        @endif



                        {{--                        <a href="{{ route('admin.customers.import') }}" class="btn btn-warning"><i--}}
                        {{--                                    class="fas fa-user-plus"></i>--}}
                        {{--                            Import Customers</a>--}}
                    @endcan
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

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.wallets.cash-count') }}">
                        <div class="col-md-3">
                            <label class="form-label small">From Date</label>
                            <input type="date" name="from_date" class="form-control"
                                   value="{{ request('from_date') ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">To Date</label>
                            <input type="date" name="to_date" class="form-control"
                                   value="{{ request('to_date') ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit">
                                    Filter
                                </button>
                                <a href="{{ route('admin.wallets.cash-count') }}" class="btn btn-light flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Customers Table -->
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive border-0 mb-0">
                        <table class="table table-hover align-middle mb-0 fs-6" id="datatable_1">
                            <thead class="table-light">
                            <tr class="text-center">
                                <th>Sr.No.</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Cashier</th>
                                <th>Count</th>
                                <th>Total<span class="text-sm text-center">(currency * count)</span></th>
                                <th>Initial Amount <span class="text-sm text-center">(Wallet)</span></th>
                                <th>Remaining Amount <span class="text-sm text-center">(Wallet)</span></th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($cashCounts as $index => $cash)
                                <tr>
                                    <td>{{ $cashCounts->firstItem() + $index}}</td>
                                    <td>{{ $cash->created_at->format('m-d-Y') }}</td>
                                    <td>{{ $cash->created_at->format('h:i:s a') }}</td>
                                    <td>{{$cash->cashier_name ?? '-'}}</td>
                                    <td>{{ $cash->total_count}}</td>
                                    <td>
                                        {{ number_format($cash->total_value, 2) }}
                                        {{\App\Models\Setting::where('key', 'currency_symbol')->first()->value}}
                                    </td>
                                    <td>{{number_format($cash->wallet_initial_balance, 2)}}</td>
                                    <td>{{number_format($cash->wallet_balance, 2)}}</td>
                                    <td>
                                        @if($cash->total_value == $cash->wallet_balance)
                                            {{'EQUAL'}}
                                        @elseif($cash->total_value > $cash->wallet_balance)
                                            {{'HIGH'}}
                                        @else
                                            {{'LOW'}}
                                        @endif
                                    </td>
                                    <td>{{$cash->remarks}}</td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="{{route('admin.wallets.cash-count-download', $cash->id)}}"
                                           title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <a class="btn btn-sm btn-outline-info"
                                           target="_blank"
                                           href="{{route('admin.wallets.cash-count-download', ['id' => $cash->id, 'action' => 'view'])}}"
                                           title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No cash count found.</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                    </div>
                    <p class="float-end">{{ $cashCounts->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#currencyTable tbody');

            // Add new row
            tableBody.addEventListener('click', function (e) {
                if (e.target.closest('.addRow')) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                <td><input type="text" name="currency[]" class="form-control" placeholder="e.g. 500" required></td>
                <td><input type="number" name="count[]" class="form-control" placeholder="Count" min="0" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm deleteRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;
                    tableBody.appendChild(newRow);
                }

                // Delete a row
                if (e.target.closest('.deleteRow')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>

    <script>
        const counts = document.querySelectorAll('.count');
        const currency = document.querySelectorAll('.currency');


        function calculateTotal() {
            let totalCount = 0;
            let totalAmount = 0;

            counts.forEach((input, key) => {
                const value = parseFloat(input.value) || 0;
                totalCount += value;
            });

            currency.forEach((input, key) => {
                const curr = parseFloat(input.value) || 0;
                totalAmount += (counts[key].value) * curr;
            });

            document.getElementById('totalCount').textContent = totalCount;
            document.getElementById('totalAmount').textContent = totalAmount;
        }

        // Listen for keyup on each input
        counts.forEach(input => {
            input.addEventListener('keyup', calculateTotal);
        });
    </script>
</x-app-layout>
