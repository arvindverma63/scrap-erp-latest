<x-head title="Activity Logs" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Heading -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Activity Logs</h3>
                
            </div>

            <!-- Filters -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="mb-0" action="{{ route('admin.audit.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search User..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="action_type" class="form-select form-select-sm">
                                    <option value="">All Actions</option>
                                    <option value="Logged In" {{ request('action_type') == 'Logged In' ? 'selected' : '' }}>Login</option>
                                    <option value="Logged Out" {{ request('action_type') == 'Logged Out' ? 'selected' : '' }}>Logout</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date" class="form-control form-control-sm"
                                    value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-fill">
                                    Filter
                                </button>
                                <a href="{{ route('admin.audit.index') }}" class="btn btn-light flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="table-responsive card shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 fs-6">
                    <thead class="table-light">
                        <tr>
                            <th>SNo.</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                     @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td>
                                @if($log->status === 'Logged In')
                                    <span class="badge bg-success">Login</span>
                                @elseif($log->status === 'Logged Out')
                                    <span class="badge bg-secondary">Logout</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td>
                                @switch($log->status)
                                    @case('Logged In')
                                        Successful login
                                        @break
                                    @case('Logged Out')
                                        Logged out successfully
                                        @break
                                    @default
                                        {{ $log->details ?? '-' }}
                                @endswitch
                            </td>
                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            <td>
                                @if($log->login_at)
                                    {{ \Carbon\Carbon::parse($log->login_at)->format('m-d-Y h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No logs found.
                            </td>
                        </tr>
                    @endforelse

                    </tbody>

                </table>
            </div>
            <p class="float-end">{{ $logs->links('pagination::bootstrap-5') }}</p>
        </div>
    </div>
</x-app-layout>