<x-head title="Roles"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Role List</h3>
                <div class="d-flex justify-content-end align-items-center p-md-2 pt-1 gap-2">
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-outline-primary">+ Add Role</a>
                    <!-- <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-export me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-excel me-2"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-csv me-2"></i> CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-pdf me-2"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div> -->
                </div>
            </div>
            <div class="card">
                <div class="card-body border-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0 fs-6" id="datatable_1">
                            <thead class="table-light">
                            <tr>
                                <th>SNo.</th>
                                <th>Role Name</th>
                                <th>Assigned Permissions</th>
                                <th>Color</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($roles as $index => $role)
                                <tr>
                                    <td>{{ $roles->firstItem() + $index}}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->permissions_count ?? 0 }}</td>
                                    <td>
                                        @if ($role->color)
                                            <span style="display:inline-block; width:30px; height:20px; background-color: {{ $role->color }}; border:1px solid #999; border-radius:4px;"></span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>

                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!in_array($role->id, [1,2,5]))
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this role?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No roles found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="float-end">{{ $roles->links('pagination::bootstrap-5') }}</p>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.common.datatable')
    <style>
        table.dataTable {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 2px 2px rgba(61, 71, 81, 0.05);
        }


        table.dataTable > tbody > tr.odd {
            background: #fff;
        }

        table.dataTable > tbody > tr.even {
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

        table.dataTable.table-hover > tbody > tr:hover > * {
            box-shadow: none !important;
            background: #00000000;
        }


    </style>

</x-app-layout>