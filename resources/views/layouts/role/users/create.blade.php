<x-head title="Create User" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center page-title-box">
                <h2 class="h4 fw-bold">Create User</h2>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">← Back</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
 <div class="card shadow-sm rounded-3 mb-0 p-4">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name"
                           class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign Roles</label>
                    <div>
                        @foreach ($roles as $role)
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                       id="role_{{ $role->id }}" class="form-check-input"
                                       {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light ms-2">Cancel</a>
            </form>
 </div>

        </div>
    </div>
</x-app-layout>
