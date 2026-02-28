<x-guest-layout>
        <style>
            .container-xxl{padding: 0}
        </style>
        <div class="login-wrapper">
            <div class="auth-container">
                <div class="auth-left">
                    <img src="{{ asset('assets/images/cm-logo.png') }}" alt="logo">
                </div>
                <div class="auth-right">
                    <h3>Forgot your password?</h3>
                   <div class="mb-3 text-sm text-gray-600">
                    {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </div>
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label class="form-label" for="email">Email Address</label>
                            <input id="email" class="form-control" type="email" name="email" placeholder="Old Email" required autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
   
</x-guest-layout>
