<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Notification Settings</h3>
                <button class="btn btn-primary btn-sm">
                        <i class="fas fa-gear"></i>  Save Settings
                </button>
            </div>
            <!-- Settings Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf

                        <!-- Email Notifications -->
                        <h5 class="fw-bold mb-3">Email Notifications</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notify_new_order" id="notify_new_order">
                                    <label class="form-check-label" for="notify_new_order">
                                        New Order Notifications
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notify_payment_received" id="notify_payment_received">
                                    <label class="form-check-label" for="notify_payment_received">
                                        Payment Received Notifications
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notify_low_stock" id="notify_low_stock">
                                    <label class="form-check-label" for="notify_low_stock">
                                        Low Stock Alerts
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Email Sender Settings -->
                        <h5 class="fw-bold mb-3">Email Sender Settings</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Sender Name</label>
                                <input type="text" name="sender_name" class="form-control form-control-sm" placeholder="Company Notifications">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Sender Email</label>
                                <input type="email" name="sender_email" class="form-control form-control-sm" placeholder="noreply@example.com">
                            </div>
                        </div>

                        <!-- SMS Notifications -->
                        <h5 class="fw-bold mb-3">SMS Notifications</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sms_new_order" id="sms_new_order">
                                    <label class="form-check-label" for="sms_new_order">
                                        New Order SMS
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sms_payment_received" id="sms_payment_received">
                                    <label class="form-check-label" for="sms_payment_received">
                                        Payment Received SMS
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sms_low_stock" id="sms_low_stock">
                                    <label class="form-check-label" for="sms_low_stock">
                                        Low Stock SMS
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Push/In-App Notifications -->
                        <h5 class="fw-bold mb-3">In-App Notifications</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="inapp_all" id="inapp_all">
                                    <label class="form-check-label" for="inapp_all">
                                        Enable All In-App Notifications
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-floppy-disk"></i>  Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>   
</x-app-layout>
