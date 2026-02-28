<!-- Top Bar Start -->
<link rel="stylesheet" href="{{ asset('assets/css/topbar/style.css') }}">
<div class="topbar d-print-none">
    <div class="container-fluid">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu"></i>
                    </button>
                </li>

            </ul>
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">

                <li class="topbar-item d-flex align-items-center">
                    <i class="fas fa-wallet fa-2xl me-2"></i>
                    <span class="wallet-balance">
                        @if(in_array(auth()->user()->roles->first()->id,[2]))
                            {{number_format( \App\Models\Wallet::where('cashier_id', auth()->user()->id)->where('date', \Carbon\Carbon::now()->toDateString())?->first()?->balance, 2) ?? 0 }}
                        @else
                            {{number_format( \App\Models\Wallet::where('date', \Carbon\Carbon::now()->toDateString())?->sum('balance'), 2) ?? 0 }}
                        @endif
                        {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                    </span>
                </li>


                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon position-relative" data-bs-toggle="dropdown"
                       href="#"
                       role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                        <i class="iconoir-bell"></i>
                        <span class="alert-badge bg-warning" id="notification-badge"></span>
                    </a>
                    @php
                        use App\Models\Notification;

                        if (auth()->user()->hasRole('super-admin')) {
                            // Super admin: show all notifications
                            $notifications = Notification::latest()->take(10)->get();
                        } else {
                            // Normal user: show their own notifications
                            $notifications = Notification::where('user_id', auth()->id())
                                ->latest()
                                ->take(10)
                                ->get();
                        }
                    @endphp

                    <style>
                        @media (max-width: 576px) {
                            #notification-list .notification-item {
                                padding: 0.75rem;
                            }

                            #notification-list .thumb-md {
                                width: 36px;
                                height: 36px;
                            }

                            #notification-list h6 {
                                font-size: 13px;
                            }

                            #notification-list small {
                                font-size: 12px;
                            }
                        }
                    </style>

                    <div class="dropdown-menu stop dropdown-menu-end dropdown-lg py-0">
                        <h5 class="dropdown-item-text m-0 py-3 d-flex justify-content-between align-items-center">

                            @if ($notifications->where('is_read', 0)->count() > 0)
                                Notifications
                                <span class="badge bg-danger"
                                      id="inner-count-card">{{ $notifications->where('is_read', 0)->count() }}</span>
                            @else
                                No Notifications
                            @endif
                        </h5>

                        <div id="notification-list" class="ms-0" style="max-height:230px; overflow-y:auto;">
                            @forelse($notifications as $n)
                                <a href="javascript:void(0)"
                                   class="dropdown-item py-3 notification-item {{ !$n->is_read ? 'bg-light' : '' }}"
                                   data-id="{{ $n->id }}" data-route="{{ $n->route }}">

                                    <div class="d-flex align-items-start w-100">
                                        <!-- Icon -->
                                        <div
                                                class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle d-flex justify-content-center align-items-center">
                                            <i class="iconoir-bell fs-4"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="my-0 fw-normal text-dark fs-13 text-wrap">{{ $n->title }}
                                            </h6>
                                            <small class="text-muted text-wrap d-block mb-1">{{ $n->message }}</small>
                                            <small class="text-muted d-block text-end">
                                                {{ $n->created_at->format('d M Y, h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-3 text-muted">No notifications</div>
                            @endforelse
                        </div>

                        <a href="{{route('admin.notifications.markAllAsRead')}}" class="float-end m-2">
                            <button class="btn btn-sm btn-warning">Clear All</button>
                        </a>


                        {{--                        <a href="{{ route('admin.notifications.index') }}"--}}
                        {{--                           class="dropdown-item text-center text-dark fs-13 py-2">--}}
                        {{--                            View All <i class="fi-arrow-right"></i>--}}
                        {{--                        </a>--}}
                    </div>


                </li>

                @php
                function truncateFirstWord($str) {
                        // get first word
                        preg_match('/^\s*(\S+)/', $str, $match);
                        $first = $match[1] ?? '';

                        // truncate first word if longer than 5 characters
                        if (strlen($first) > 10) {
                            return substr($first, 0, 5) . '...';
                        }

                        return $first;
                    }
                @endphp
                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon d-flex flex-column" data-bs-toggle="dropdown"
                       href="#"
                       role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                        <img src="{{ asset('assets/images/users/user.png') }}" alt=""
                             class="thumb-md rounded-circle">
                        <p class="mb-0">
                            {{truncateFirstWord(auth()?->user()->name)}}
                        </p>
                        <p class="mb-0">
                            ({{auth()?->user()?->roles?->first()?->name}})
                        </p>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/users/user.png') }}" alt=""
                                     class="thumb-md rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">{{ Auth::user()->name }}</h6>
                                <small class="text-muted mb-0">{{ Auth::user()->email }}</small>
                            </div><!--end media-body-->
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <small class="text-muted px-2 pb-1 d-block">Account</small>
                        <a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i
                                    class="las la-user fs-18 me-1 align-text-bottom"></i> My Profile</a>

                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>

                </li>
            </ul><!--end topbar-nav-->
        </nav>
        <!-- end navbar-->
    </div>
</div>


@include('layouts.common.topup-modal')
<!-- Top Bar End -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const redirectRoute = this.getAttribute('data-route');

                fetch("{{ route('admin.notifications.markAsRead', ':id') }}".replace(':id',
                    id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Remove bg-light visually
                            this.classList.remove('bg-light');

                            // Optionally update badge count
                            const badge = document.querySelector(
                                '.dropdown-item-text .badge');
                            if (badge) {
                                let count = parseInt(badge.innerText) - 1;
                                if (count > 0) badge.innerText = count;
                                else badge.remove();
                            }

                            // Redirect based on type
                            window.location.href = redirectRoute;
                        }
                    })
                    .catch(err => console.error(err));
            });
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(window).on('load', function () {
        // Function to call your Laravel route
        let notificationIds = [];

        function fetchNotifications() {
            $.ajax({
                url: "{{ route('admin.check.notifications') }}",
                type: "GET",
                success: function (response) {
                    // toastr.success('hello');
                    // Example: update a div or alert
                    $('#notification-badge').text(response.count > 99 ? '99+' : response.count);
                    $('#inner-count-card').text(response.count);
                    $('.wallet-balance').text(response.current_wallet_balance+' JMD');

                    let list = $('#notification-list');
                    list.empty();

                    response.notifications.forEach(n => {

                        let route = '#javascript(0);';
                        if (n.type == 'wallet') {
                            route = "{{route('admin.notifications.index')}}";
                        } else if (n.type == 'purchase payments' || n.type == 'sales payments') {
                            route = "{{route('admin.notifications.payments')}}";
                        } else if (n.type == 'HIGH_STOCK' || n.type == 'LOW_STOCK') {
                            route = "{{route('admin.notifications.stocks')}}";
                        } else {
                            route = "{{route('admin.notifications.index')}}";
                        }


                        list.append(`
                    <a href=${route}
                        class="dropdown-item py-3 notification-item ${!n.is_read ? 'bg-light' : ''}"
                        data-id="${n.id}" data-route="${n.route || '#'}">

                        <div class="d-flex align-items-start w-100">
                            <!-- Icon -->
                            <div
                                class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle d-flex justify-content-center align-items-center">
                                <i class="iconoir-bell fs-4"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-grow-1 ms-2">
                                <h6 class="my-0 fw-normal text-dark fs-13 text-wrap">${n.title}</h6>
                                <small class="text-muted text-wrap d-block mb-1">${n.message}</small>
                                <small class="text-muted d-block text-end">
                                    ${new Date(n.created_at).toLocaleString('en-US', {
                            timeZone: 'America/Jamaica',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        })}
                                </small>
                            </div>
                        </div>
                    </a>
                `);


                        if (n.is_sound == false) {
                            notificationIds.push(n.id);
                        }
                    });


                    if (notificationIds.length > 0) {
                        setTimeout(updateStatus, 2500);
                    }

                },
                error: function (xhr) {
                    console.error("Error fetching notifications:", xhr.responseText);
                }
            });
        }

        function playSound(url) {
            const audio = new Audio(url);
            toastr.success('New notification arrived!');
            audio.play().catch(err => console.error("Sound play failed:", err));
        }

        function updateStatus() {
            playSound("{{ asset('assets/sound/slack_notification.mp3') }}");

            $.ajax({
                url: "{{ route('admin.updateStatus.notifications') }}",
                type: "GET",
                data: {notificationIds: notificationIds},
                success: function (response) {

                    notificationIds = [];
                },
                error: function (xhr) {
                    console.error("Error updating notifications:", xhr.responseText);
                }
            });
        }

        // Call immediately when page loads
        fetchNotifications();

        // Then repeat every 10 seconds (10,000 ms)
        setInterval(fetchNotifications, 4000);
    });
</script>
