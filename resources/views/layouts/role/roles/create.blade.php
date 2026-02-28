<x-head title="Create Role" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">

         
             <div class="d-flex justify-content-between align-items-center page-title-box">
                    <h4 class="card-title">Create Role</h4>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">← Back</a>
                </div>

              <div class="card">
                  <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <!-- Role Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter role name"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="color">Choose a color:</label>
                            <input type="color" id="color" name="color" 
                            value="" class="form-control" style="width:150px;">
                        </div>

                        <!-- Permissions -->
                        <!-- Permissions -->
                        <div class="mb-3">
                            <label class="form-label">Assign Permissions</label>

                            @foreach ($permissions as $group => $groupPermissions)
                                <div class="mb-2">
                                    <div class="form-check fw-bold text-primary">
                                        <input type="checkbox" id="group-{{ \Str::slug($group) }}-all"
                                            class="form-check-input group-all" data-group="{{ \Str::slug($group) }}">
                                        <label for="group-{{ \Str::slug($group) }}-all" class="form-check-label text-black">
                                            {{ $group }} (Check all)
                                        </label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    @foreach ($groupPermissions as $permission)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                    id="perm_{{ $permission->id }}"
                                                    class="form-check-input perm-checkbox group-{{ \Str::slug($group) }}">
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
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Save Role</button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
               
            </div>
              </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.group-all').forEach(function (groupCheckbox) {
                groupCheckbox.addEventListener('change', function () {
                    let selector = '.group-' + this.getAttribute('data-group');
                    document.querySelectorAll(selector).forEach(cb => cb.checked = this.checked);
                });
            });
        });
    </script>

</x-app-layout>