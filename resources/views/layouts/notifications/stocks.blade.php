<x-head title="Notifications" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center page-title-box">
            <h4 class="fw-bold mb-0">Stocks Notifications</h4>
            <!-- <button id="mark-all-read" class="btn btn-primary btn-sm">Mark All as Read</button> -->
        </div>

        <div class="card">
            <div class="card-body border-0 p-0">
                @if ($notifications->isEmpty())
                    <div class="text-center p-4">
                        <small class="text-muted">No notifications found.</small>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-start
                                        @if (!$notification->is_read) bg-light @endif">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $notification->title }}</div>
                                    <small class="text-muted">{{ $notification->message }}</small>
                                </div>
                                <small class="float-end text-muted ps-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                    • {{ $notification->created_at->format('d M Y, h:i A') }}
                                </small>

                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        {{ $notifications->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    </div>
    
</x-app-layout>
