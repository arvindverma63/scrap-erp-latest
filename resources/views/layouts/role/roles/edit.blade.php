<x-head title="Edit Role" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h2 class="h4 fw-bold">Edit Role</h2>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Role Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label small mb-1">Role Name</label>
                            <input type="text" name="name" id="name"
                                   class="form-control"
                                   value="{{ old('name', $role->name) }}" required {{in_array($role->id, [1,2,5]) ? 'readonly' : null }} readonly>
                        </div>

                        <div class="mb-3">
                            <label for="color">Choose a color:</label>
                            <input type="color" id="color" name="color" 
                            value="{{old('color', $role->color)}}" class="form-control" style="width:150px;">
                        </div>
                        
                        <!-- Permissions (grouped with "Check All") -->
                        <div class="mb-3">
                            <label class="form-label small mb-1">Assign Permissions</label>

                            @foreach ($permissions as $group => $groupPermissions)
                                @php
                                    $groupSlug = \Str::slug($group);
                                    $allChecked = $groupPermissions->every(function($perm) use ($rolePermissions) {
                                        return in_array($perm->id, $rolePermissions);
                                    });
                                @endphp
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input group-check"
                                               id="group_{{ $groupSlug }}_check"
                                               data-group="{{ $groupSlug }}"
                                               {{ $allChecked ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-primary" for="group_{{ $groupSlug }}_check">
                                            {{ $group }} (Check all)
                                        </label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    @foreach ($groupPermissions as $permission)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="permissions[]"
                                                       value="{{ $permission->id }}"
                                                       id="perm_{{ $permission->id }}"
                                                       class="form-check-input permission-box permission-{{ $groupSlug }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Role
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Check All Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.group-check').forEach(function(groupCheckbox) {
            groupCheckbox.addEventListener('change', function() {
                let group = this.getAttribute('data-group');
                document.querySelectorAll('.permission-' + group).forEach(function(cb) {
                    cb.checked = groupCheckbox.checked;
                });
            });
        });

        document.querySelectorAll('.permission-box').forEach(function(box) {
            box.addEventListener('change', function() {
                let groupClass = Array.from(this.classList).find(c => c.startsWith('permission-')).replace('permission-', '');
                let all = document.querySelectorAll('.permission-' + groupClass);
                let allChecked = Array.from(all).every(cb => cb.checked);
                document.getElementById('group_' + groupClass + '_check').checked = allChecked;
            });
        });
    });
    </script>
    <style>
        .card-body .small {
    margin-bottom: .5rem !important;
    font-weight: 500 !important;
    color: #656d9a !important;
    font-size: 0.812rem !important;
}
.card-body .form-check .text-primary {
    margin-bottom: .5rem;
    font-weight: 600 !important;
    color: #000 !important;
    font-size: 0.812rem !important;
        margin: 0 !important;
}

.card-body .form-check {
    margin-bottom: .5rem;
    font-weight: 500;
    color: #656d9a;
    font-size: 0.812rem;
    margin-top: 10px;
}


    </style>
</x-app-layout>
