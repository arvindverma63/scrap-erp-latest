<x-head title="Wallets" />
<x-app-layout>

    <!-- Bootstrap 5 Confirmation Modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="confirmActionMessage">
                    <!-- Message will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="confirmActionForm" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Yes, Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

     <div class="page-content">
        <div class=" container-fluid">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h5 class="card-title mb-0">Wallet Management</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#topUpModal">
                    <i class="fa fa-wallet"></i> Top-up {{ auth()->user()->roles()->first()->name != 1 ? 'Request' : ''}}
                </button>
            </div>


            <div class="card mb-0">
                <div class="card-body p-0">
                    @php
                    $activeTab = (auth()->user()->roles->first()->name != 'super-admin' || auth()->user()->roles->first()->name != 'super-admin') ? request('tab', 'transaction') : request('tab', 'request');
                    @endphp
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item bg-light border-1 {{auth()->user()->roles->first()->name != 'super-admin' || auth()->user()->roles->first()->name != 'super-admin' ? 'd-none' : ''}}" role="presentation">
                            <a class="nav-link {{ $activeTab == 'request' ? 'active' : '' }}" data-bs-toggle="tab" href="#navpills2-home" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-home-account"></i></span>
                                <span class="d-none d-sm-block">Request</span>
                            </a>
                        </li>
                        <li class="nav-item bg-light border-1" role="presentation">
                            <a class="nav-link  {{ $activeTab == 'transaction' ? 'active' : '' }}" data-bs-toggle="tab" href="#navpills2-profile" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account-outline"></i></span>
                                <span class="d-none d-sm-block">Transaction</span>
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content text-muted">
                        <div class="tab-pane {{auth()->user()->roles->first()->name != 'super-admin' || auth()->user()->roles->first()->name != 'super-admin' ? 'd-none' : ''}} {{ $activeTab == 'request' ? 'active show' : '' }}" id="navpills2-home" role="tabpanel">

                            <div>
                                <h5 class="fw-bold my-2 px-3"><i class="fas fa-wallet me-2"></i> Pending Top-Up Requests</h5>

                                @if ($pending->count())
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>Date</th>
                                                @can('wallet_approve')
                                                <th>Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pending as $key => $txn)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $txn->requester->name ?? 'super admin' }}</td>
                                                <td>{{ number_format($txn->amount, 2) }}
                                                    {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                                </td>
                                                <td><span class="badge bg-info">{{ ucfirst($txn->type) }}</span></td>
                                                <td><span class="badge bg-warning">{{ ucfirst($txn->status) }}</span></td>
                                                <td>{{$txn->remarks ?? 'N/A'}}</td>
                                                <td>{{$txn->created_at->format('m-d-Y, h:i A')}}</td>
                                                <td>
                                                    @can('wallet_approve')
                                                        @if($txn->created_at->isToday())
                                                            <button type="button" class="btn btn-success btn-sm action-btn" data-action="{{ route('admin.wallets.approve', $txn->id) }}" data-message="Are you sure you want to approve this transaction?">Approve</button>
                                                            <button type="button" class="btn btn-danger btn-sm action-btn" data-action="{{ route('admin.wallets.reject', $txn->id) }}" data-message="Are you sure you want to reject this transaction?">Reject</button>
                                                        @else
                                                            <button type="button" class="btn btn-seconadry btn-sm bg-secondary" style="cursor: not-allowed;">Approve</button>
                                                            <button type="button" class="btn btn-seconadry btn-sm bg-secondary" style="cursor: not-allowed;">Reject</button>
                                                        @endif
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <p class="text-muted">No pending top-up requests.</p>
                                @endif
                            </div>

                        </div>

                        <div class="tab-pane {{ $activeTab == 'transaction' ? 'active show' : '' }}" id="navpills2-profile" role="tabpanel">

                            <div>
                                 <h5 class="fw-bold my-2 px-3"><i class="fas fa-history me-2"></i> 
                                Transaction History</h5>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($history as $key => $txn)
                                            <tr>
                                                <td>{{ $history->firstItem() + $key }}</td>
                                                <td>{{ $txn->requester->name ?? 'Admin' }}</td>
                                                <td>{{ number_format($txn->amount, 2) }}
                                                    {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                                </td>
                                                <td>
                                                    @can('wallet_approve')
                                                    @if ($txn->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                    @elseif($txn->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                    <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                    @endcan
                                                </td>
                                                <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                                <p class="float-end">{{ $history->appends(['tab' => 'transaction'])->links('pagination::bootstrap-4') }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @include('layouts.common.datatable')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const actionButtons = document.querySelectorAll('.action-btn');
            const modalEl = document.getElementById('confirmActionModal');
            const modal = new bootstrap.Modal(modalEl);
            const confirmForm = document.getElementById('confirmActionForm');
            const confirmMessage = document.getElementById('confirmActionMessage');

            actionButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const actionUrl = this.dataset.action;
                    const message = this.dataset.message;

                    confirmForm.setAttribute('action', actionUrl);
                    confirmMessage.textContent = message;

                    modal.show();
                });
            });
        });
    </script>
</x-app-layout>