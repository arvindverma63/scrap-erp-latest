<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">User Settings</h3>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-gear"></i> Save Settings
                    </button>
            </div>

                <!-- Settings Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Profile Picture -->
                            <h5 class="fw-bold mb-3">Profile Picture</h5>
                            <div class="mb-4 d-flex align-items-center">
                                <img src="{{ asset('assets/images/users/default-avatar.png') }}" 
                                    alt="User Avatar" 
                                    class="rounded-circle me-3" 
                                    style="width:70px;height:70px;object-fit:cover;">
                                <input type="file" name="avatar" class="form-control form-control-sm w-auto">
                            </div>

                            <!-- Personal Information -->
                            <h5 class="fw-bold mb-3">Personal Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Full Name</label>
                                    <input type="text" name="name" class="form-control form-control-sm" placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-sm" placeholder="john@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Phone</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" placeholder="+91 9876543210">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Role</label>
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                        <option value="manager">Manager</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Change Password -->
                            <h5 class="fw-bold mb-3">Change Password</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Current Password</label>
                                    <input type="password" name="current_password" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">New Password</label>
                                    <input type="password" name="new_password" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Notification Preferences -->
                            <h5 class="fw-bold mb-3">Notification Preferences</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="notify_email" id="notify_email" checked>
                                        <label class="form-check-label" for="notify_email">
                                            Email Notifications
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="notify_sms" id="notify_sms">
                                        <label class="form-check-label" for="notify_sms">
                                            SMS Notifications
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="notify_inapp" id="notify_inapp" checked>
                                        <label class="form-check-label" for="notify_inapp">
                                            In-App Notifications
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm">
                                   <i class="fas fa-floppy-disk"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
