<x-head title="Create Permission" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Create Permission</h3>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card shadow rounded-xl p-4">

                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf

                   <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small mb-1">Permission Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter permission name"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small mb-1">Permission Group</label>
                            <div class="input-group">
                                <select name="group_id" class="form-select" required>
                                    <option value="">-- Select Group --</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('admin.permission-groups.create') }}" class="btn btn-outline-primary"
                                    role="button" title="Create Group">
                                    + Create Group
                                </a>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                 <i class="fa-solid fa-floppy-disk me-1"></i> Save Permission
                            </button>
                        </div>
                   </div>
                    
                </form>
            </div>

        </div>
    </div>
</x-app-layout>