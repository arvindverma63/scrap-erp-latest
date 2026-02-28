<x-guest-layout>

    <style>
        .container-xxl {
            padding: 0
        }
    </style>



    <div class="login-wrapper">
        <div class="auth-container">
            <div class="auth-left">
                <img src="{{ asset('assets/images/cm-logo.png') }}" alt="logo">
            </div>
            <div class="auth-right">
                <h3>Sign In</h3>

                {{-- ✅ Bootstrap Alerts --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
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
                {{-- ✅ End Alerts --}}
                <form method="POST" action="{{ route('auth.login') }}">
                    @csrf
                    <div class="mb-sm-3 mb-1">
                        <label for="email" class="form-label">E-mail Address</label>
                        <input id="email" type="email" name="email" class="form-control" placeholder="Enter Your Email"
                            required autofocus>
                    </div>

                    <div class="mb-sm-3 mb-1">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-control"
                            placeholder="Enter Your Password" required>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-sm-3 mb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                            <label class="form-check-label fs-14" for="remember_me">Remember me</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="text-primary fs-14" href="{{ route('password.request') }}">
                                Forgot Password?
                            </a>
                        @endif
                    </div>
                    <button class="btn btn-primary w-100 mb-sm-2 mb-1" type="submit">Log In <i
                            class="fas fa-sign-in-alt ms-1"></i></button>
                    {{-- <div class="text-center  mb-2">
                        <p class="text-muted">Don't have an account ? <a href="{{ route('register') }}"
                                class="text-primary ms-2 text-decoration-underline">Sign Up</a></p>
                    </div> --}}
                </form>
            </div>
        </div>
    </div>

</x-guest-layout>