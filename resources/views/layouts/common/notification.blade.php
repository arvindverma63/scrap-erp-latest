<script>
document.addEventListener('DOMContentLoaded', () => {

    const notificationList = document.getElementById('notification-list');
    const notificationBadge = document.getElementById('notification-badge');
    const csrfToken = "{{ csrf_token() }}";

    async function loadNotifications() {
        try {
            const response = await fetch("{{ route('admin.notifications.fetch') }}");
            const data = await response.json();
            const notifications = data.notifications || [];

            let html = '';
            let unreadCount = 0;

            if (notifications.length === 0) {
                html = `<div class="text-center py-3">
                            <small class="text-muted">No notifications</small>
                        </div>`;
            } else {
                notifications.forEach((n) => {
                    let url = '#';

                    // Set URL based on type
                    if (n.type === 'wallet') {
                        url = "{{ route('admin.wallets.index') }}";
                    } else if (n.type === 'order') {
                        url = "/orders";
                    }

                    if (!n.is_read) unreadCount++;

                    html += `
                        <a href="${url}" 
                           class="dropdown-item py-3 notification-item ${!n.is_read ? 'bg-light' : ''}" 
                           data-id="${n.id}">
                            <small class="float-end text-muted ps-2">${moment(n.created_at).fromNow()}</small>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                    <i class="iconoir-bell fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-2 text-truncate">
                                    <h6 class="my-0 fw-normal text-dark fs-13">${n.title}</h6>
                                    <small class="text-muted mb-0">${n.message}</small>
                                </div>
                            </div>
                        </a>`;
                });
            }

            notificationList.innerHTML = html;
            notificationBadge.textContent = unreadCount > 0 ? unreadCount : '';

        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    // Load on start
    loadNotifications();

    // Poll every 30 seconds
    setInterval(loadNotifications, 6000);

    // Mark notification as read
    document.addEventListener('click', async (e) => {
        const item = e.target.closest('.notification-item');
        if (!item) return;

        e.preventDefault();
        const id = item.getAttribute('data-id');

        try {
            const response = await fetch(`/notifications/mark-as-read/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            });
            const result = await response.json();

            if (result.success) {
                item.classList.remove('bg-light');
                loadNotifications();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    });

});
</script>
