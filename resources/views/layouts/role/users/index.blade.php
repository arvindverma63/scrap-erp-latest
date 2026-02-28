<x-head title="Users" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">

          <div class="d-flex justify-content-between align-items-center page-title-box">
                <h2 class="h4 fw-bold">User List</h2>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0 fs-6"  id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>SNo.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th style="width:150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index}}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            {{ $user->getRoleNames()->join(', ') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="float-end">{{ $users->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>

            @include('layouts.common.datatable')
        </div>
    </div>

    <style>
        table.table-bordered.dataTable {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    box-shadow: 0 2px 2px rgba(61, 71, 81, 0.05);
}



table.table-bordered.dataTable  tr:nth-child(odd) {
    background-color: #fff;
}

table.table-bordered.dataTable tr:nth-child(even) {
    background-color: #d6e1e294;
}
table.table-bordered.dataTable table tr:nth-child(even) {
    background-color: #d6e1e294;
}
div.dataTables_wrapper .table tbody, 
div.dataTables_wrapper .table td, 
div.dataTables_wrapper .table tfoot,
 div.dataTables_wrapper .table th,
 div.dataTables_wrapper .table thead, 
 div.dataTables_wrapper .table tr {
    border-style: none; 
}
table.table-bordered.dataTable.table-hover>tbody>tr:hover>* {
    box-shadow: none !important;
    background: #00000000;
} 
    </style>
</x-app-layout>