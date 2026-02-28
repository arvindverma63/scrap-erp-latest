<x-head title="Edit Permission Group" />

<x-app-layout>
    <div class="page-content">
        <div class="container py-4">

           
                  <div class="d-flex justify-content-between align-items-center page-title-box">
                    <h2 class="h4 fw-bold">Edit Permission Group</h2>
                    <a href="{{ route('admin.permission-groups.index') }}" class="btn btn-outline-primary">
                       ← Back
                    </a>
                </div>
 <div class="card shadow rounded p-4">
                <form action="{{ route('admin.permission-groups.update', $group->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Group Name</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter group name" required value="{{ old('name', $group->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary px-4">Update Group</button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
