<x-head title="Create Permission Group" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                    <h2 class="h4 fw-bold">Create Permission Group</h2>
                    <a href="{{ route('admin.permission-groups.index') }}" class="btn btn-outline-primary">
                         <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            <div class="card shadow rounded-3 p-3">
               <form action="{{ route('admin.permission-groups.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label small mb-1">Group Name</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter group name" required value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create Group</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
