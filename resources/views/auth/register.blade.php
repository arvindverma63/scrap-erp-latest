<x-guest-layout>
    <style>
        .container-xxl {padding: 0}
    </style>

    <div class="login-wrapper">
        <div class="auth-container">
            <div class="auth-left">
                <img src="{{ asset('assets/images/cm-logo.png') }}" alt="logo">
            </div>
            <div class="auth-right">
                <h3>Sign Up</h3>

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

                <form method="POST" action="{{ route('auth.register') }}">
                    @csrf
                    <div class="mb-sm-3 mb-1">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" name="name" class="form-control" placeholder="Enter Your Name" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="mb-sm-3 mb-1">
                        <label for="email" class="form-label">E-mail Address</label>
                        <input id="email" type="email" name="email" class="form-control" placeholder="Enter Your Email" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-sm-3 mb-1">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Enter Your Password" required>
                    </div>

                    <div class="mb-sm-3 mb-1">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Confirm Your Password" required>
                    </div>

                    <div class="mb-sm-3 mb-1">
                        <label for="role" class="form-label">Select Role</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="">-- Choose Role --</option>
                            <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                            <option value="customer" {{ old('role')=='customer'?'selected':'' }}>Customer</option>
                            <option value="supplier" {{ old('role')=='supplier'?'selected':'' }}>Supplier</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100 mb-sm-2 mb-1" type="submit">
                        Register <i class="fas fa-user-plus ms-1"></i>
                    </button>
                    <div class="text-center mb-2">
                        <p class="text-muted">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-primary ms-2 text-decoration-underline">Sign In</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
