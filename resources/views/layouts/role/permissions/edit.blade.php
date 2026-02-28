<x-head title="Edit Permission" />

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Edit Permission</h3>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            

            <div class="card shadow ">
                <div class="card-body">
                    <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                
                                <label class="form-label small mb-1">Permission Name</label>
                                <input type="text" name="name" 
                                    value="{{ old('name', $permission->name) }}"
                                    class="form-control" 
                                    placeholder="Enter permission name" required>
                            
                            </div>
                            <div class="col-md-6 mb-3">
                                
                                <label class="form-label small mb-1">Permission Group</label>
                                <select name="group_name" class="form-select" required>
                                    <option value="">-- Select Group --</option>
                                    <option value="User Management" {{ $permission->group_name == 'User Management' ? 'selected' : '' }}>User Management</option>
                                    <option value="Role Management" {{ $permission->group_name == 'Role Management' ? 'selected' : '' }}>Role Management</option>
                                    <option value="Product Management" {{ $permission->group_name == 'Product Management' ? 'selected' : '' }}>Product Management</option>
                                    <option value="Order Management" {{ $permission->group_name == 'Order Management' ? 'selected' : '' }}>Order Management</option>
                                    <option value="Reports" {{ $permission->group_name == 'Reports' ? 'selected' : '' }}>Reports</option>
                                    <option value="Settings" {{ $permission->group_name == 'Settings' ? 'selected' : '' }}>Settings</option>
                                </select>
                            
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Permission
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>