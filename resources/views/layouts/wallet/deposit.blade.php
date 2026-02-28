<x-app-layout>

    <!-- Deposit Modal -->
    <div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Add Wallet Deposit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.wallets.create-deposit') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="amount" class="form-label">Cashier</label>
                        <select class="form-control" name="cashier_id" required>
                            <option value="">Select Cashier</option>
                            @foreach($cashiers as $cashier)
                                <option value="{{$cashier->id}}">{{$cashier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="balance" class="form-label">Deposit Amount</label>
                            <input type="number" name="balance" id="balance" class="form-control"
                                   placeholder="Enter amount" min="0" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="page-content">
        <div class="container-fluid role-rapper">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Per Day Wallet Deposits</h3>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    @can('wallet_deposit')
                        <!-- Deposit Button -->
                         @if(in_array(auth()->user()->roles->first()->id, [1,5]))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#depositModal">
                            <i class="fas fa-user-plus"></i> Credit Wallet
                        </button>
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
                    <form class="row g-3 align-items-end" method="GET"
                          action="{{ route('admin.wallets.deposit') }}">
                          @if(in_array(auth()->user()->roles->first()->id, [1,5]))
                        <div class="col-md-3">
                            <label class="form-label small">Cashier</label>
                            <select class="form-control" name="cashier_id" required>
                                <option value="">Select Cashier</option>
                                @foreach($cashiers as $cashier)
                                    <option value="{{$cashier->id}}">{{$cashier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                            @endif
                            @if(!in_array(auth()->user()->roles->first()->id, [1,5]))
                        <div class="col-md-3" ></div>
                            @endif
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
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit">
                                    Filter
                                </button>
                                <a href="{{ route('admin.wallets.deposit') }}" class="btn btn-light flex-fill">
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
                            <tr>
                                <th>Sr.No.</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>Initial Balance</th>
                                <th>Final Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($wallets as $index => $wallet)
                                <tr>
                                    <td>{{ $wallets->firstItem() + $index}}</td>
                                    <td>{{ $wallet->date->format('m-d-Y') }}</td>
                                    <td>{{$wallet->cashier->name ?? '-'}}</td>
                                    <td>{{number_format($wallet->initial_balance,2) }}
                                        {{\App\Models\Setting::where('key', 'currency_symbol')->first()->value}}
                                    </td>
                                    <td>{{ number_format($wallet->balance, 2)}}
                                        {{\App\Models\Setting::where('key', 'currency_symbol')->first()->value}}
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
                    <p class="float-end">{{ $wallets->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>

    </script>
</x-app-layout>
