<section class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom-0">
        <h5 class="card-title mb-1">{{ __('Update Password') }}</h5>
        <p class="text-muted small mb-0">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </div>

    <div class="card-body">
        <form method="post" action="">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div class="mb-3">
                <label for="update_password_current_password" class="form-label">
                    {{ __('Current Password') }}
                </label>
                <input type="password" id="update_password_current_password" name="current_password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    autocomplete="current-password">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="update_password_password" class="form-label">
                    {{ __('New Password') }}
                </label>
                <input type="password" id="update_password_password" name="password"
                    class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label">
                    {{ __('Confirm Password') }}
                </label>
                <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Save Button & Status -->
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                @if (session('status') === 'password-updated')
                    <p class="text-success small mb-0">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
