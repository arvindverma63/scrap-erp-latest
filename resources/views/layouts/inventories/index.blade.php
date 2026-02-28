<x-head title="Inventories" />


<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Inventory Management</h3>
                <!-- <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file-csv text-success"></i> Export CSV
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file-pdf text-danger"></i> Export PDF
                            </a>
                        </li>
                    </ul>
                </div> -->
            </div>

            <!-- Filters -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="mb-0" action="{{ route('admin.inventories.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="name" value="{{ request('name') }}"
                                    class="form-control form-control-sm" placeholder="Search Product...">
                            </div>
                            <div class="col-md-3">
                                <select name="product_id" class="form-select form-select-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="inventory_type" class="form-select form-select-sm">
                                    <option value="">Inventory Type</option>
                                    <option value="Purchase" {{ request('inventory_type') == 'Purchase' ? 'selected' : '' }}>
                                        Purchase</option>
                                    <option value="Sale" {{ request('inventory_type') == 'Sale' ? 'selected' : '' }}>Sale
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.inventories.index') }}" class="btn btn-light flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="table-responsive card shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 fs-6">
                    <thead class="table-light">
                        <tr>
                            <th>Sr.No.</th>
                            <th>Product</th>
                            <th>Unit</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Inventory Type</th>
                            <th>User (Supplier/Customer)</th>
                            <th>Approved By</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $index => $inventory)
                            <tr>
                                <td>{{ $inventories->firstItem() + $index }}</td>
                                <td>{{ $inventory->product?->name ?? '-' }}</td>
                                <td>{{ $inventory->weightUnit?->name ?? '-' }}</td>
                                <td>@if($inventory->inventory_type == 'Purchase')
                                    {{$inventory->product->purchase_price ?? '-'}}
                                    {{ App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value }}
                                @elseif($inventory->inventory_type == 'Sale')
                                        {{$inventory->product->sale_price ?? '-'}}
                                        {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}
                                    @endif
                                </td>
                                <td>{{ $inventory->quantity }}</td>
                                <td>{{ number_format($inventory->amount, 2) }}
                                    @if($inventory->inventory_type == 'Purchase')
                                    {{ App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value }}
                                    @elseif($inventory->inventory_type == 'Sale')
                                    {{ App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge 
                                                                @if($inventory->inventory_type == 'Purchase') bg-success 
                                                                @elseif($inventory->inventory_type == 'Sale') bg-danger 
                                                                @endif">
                                        {{ $inventory->inventory_type }}
                                    </span>
                                </td>
                                <td>
                                    @if($inventory->inventory_type == 'Purchase')
                                        Supplier : {{ $inventory->supplier->name }}
                                    @elseif($inventory->inventory_type == 'Sale')
                                        Customer ID: {{ $inventory->customer->name }}
                                    @endif
                                </td>
                                <td>{{ $inventory->creator->name }}</td>

                                <td>{{ $inventory->updated_at->format('m-d-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No inventory records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2">
                    {{ $inventories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>