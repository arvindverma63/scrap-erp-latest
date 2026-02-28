<x-head title="Permission Groups" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">

            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h2 class="h4 fw-bold">Permission Groups</h2>
                <!-- <a href="{{ route('admin.permission-groups.create') }}" class="btn btn-primary">
                    + Create Group
                </a> -->
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0 fs-6 border" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    {{-- <th style="width: 150px;">Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $group)
                                    <tr>
                                        <td>{{ $group->name }}</td>
                                        {{-- <td>
                                            <a href="{{ route('admin.permission-groups.edit', $group->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.permission-groups.destroy', $group->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this group?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td> --}}
                                    </tr>
                                @endforeach

                                @if($groups->isEmpty())
                                    <tr>
                                        <td colspan="2" class="text-center">No permission groups found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('layouts.common.datatable')
        </div>
    </div>
   <style>
    

    </style>
</x-app-layout>