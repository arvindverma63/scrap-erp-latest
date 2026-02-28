<x-head title="Permission" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Permission List</h3>
                <!-- <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">+ Add Permission</a> -->
            </div>

            @forelse ($permissions as $group => $groupPermissions)
                <div class="card">
                    <div class="card-header py-2">
                        <h5 class="card-title">{{ $group ?? 'Ungrouped' }}</h5>
                    </div>
                    <div class="table-responsive card shadow-sm rounded-0 mb-0">
                        <table class="table table-hover align-middle mb-0 fs-6">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Permission Name</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupPermissions as $index => $permission)
                                    <tr>
                                        <td>{{ $loop->iteration ?? null }}</td>
                                        <td>{{ $permission->name ?? null }}</td>
                                        {{-- <td>
                                            <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">No permissions found.</div>
            @endforelse

            @include('layouts.common.datatable')
        </div>
    </div>
    <style>
        .table-responsive.card.shadow-sm tr td {
    width: 25%;
}
    </style>
</x-app-layout>