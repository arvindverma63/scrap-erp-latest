<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-rapper">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Weight Units</h3>
                @can('setting_weight_unit_read')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                        <i class="fas fa-plus"></i> Add Unit
                    </button>
                @endcan
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Units Table -->
            <div class="card">
                <div class="table-responsive card-body">
                    <table class="table table-hover align-middle mb-0 fs-6" id="datatable_1">
                        <thead class="table-light">
                        <tr>
                            <th>SNo.</th>
                            <th>Unit Name</th>
                            <th>Description</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($units as $index => $unit)
                            <tr>
                                <td>{{ $units->firstItem() + $index}}</td>
                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->description }}</td>
                                <td>{{ $unit->created_at->format('m-d-Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#editUnitModal{{ $unit->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.weight_units.destroy', $unit->id) }}" method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Unit Modal -->
                            <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Weight Unit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.weight_units.update', $unit->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Unit Name</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="name" value="{{ $unit->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="description" value="{{ $unit->description }}"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary btn-sm"><i
                                                            class="fas fa-pen-to-square"></i> Update Unit
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="float-end">{{ $units->links('pagination::bootstrap-5') }}</p>
            </div>

            <!-- Add Unit Modal -->
            <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="addUnitModalLabel">Add New Weight Unit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.weight_units.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Unit Name</label>
                                    <input type="text" class="form-control form-control-sm" name="name"
                                           placeholder="Enter unit name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control form-control-sm" name="description"
                                           placeholder="Enter description" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i>Save
                                    Unit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div id="unitToast" class="toast align-items-center text-bg-success border-0" role="alert"
                     aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Action completed successfully!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast Script -->
    <script>
        @if (session('success'))
        var toastEl = document.getElementById('unitToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
        @endif
    </script>

    @include('layouts.common.datatable')
</x-app-layout>
