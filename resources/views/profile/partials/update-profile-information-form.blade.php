<section class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom-0">
        <h5 class="card-title mb-1">{{ __('Profile Information') }}</h5>
        <p class="text-muted small mb-0">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </div>

    <div class="card-body">
        <!-- Hidden form for email verification -->
        <form id="send-verification" method="post" action="">
            @csrf
        </form>

        <!-- Profile Update Form -->
        <form method="post" action="">
            @csrf
            @method('patch')

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}"
                    required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input type="email" id="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}"
                    required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="small text-muted mb-1">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification"
                                class="btn btn-link btn-sm p-0 align-baseline text-decoration-underline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="text-success small mb-0">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Submit Button & Status -->
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                @if (session('status') === 'profile-updated')
                    <p class="text-success small mb-0">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
