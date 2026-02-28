<x-head title="Low Stocks Alert"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-rapper">
            <!-- Heading -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Stock Alerts</h3>

            </div>


            <!-- Table -->
            <div class="card">
                <div class="table-responsive card-body border-0 shadow-sm rounded-3">
                    <table class="table table-hover align-middle mb-0 fs-6" id="datatable_1">
                        <thead class="table-light">
                        <tr>
                            <th>SNo.</th>
                            <th>Product Name</th>
                            <th>Current Stock</th>
                            <th>Weight Unit</th>
                            <th>Status</th>
{{--                            <th>Action</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($productWise as $index => $item)
                            <tr>
                                <td>{{ $productWise->firstItem() + $index}}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['total_quantity'] }}</td>
                                <td>{{ $item['weightUnit']['name'] }}</td>
                                <td>
                                    @if ($item['total_quantity'] <= App\Models\Product::first()->low_stock_limit ?? 0)
                                        <span class="badge bg-danger">Low Stock</span>
                                    @elseif ($item['total_quantity'] >= App\Models\Product::first()->high_stock_limit ?? 0)
                                        <span class="badge bg-warning">High Stock</span>
                                    @else
                                        <span class="badge bg-success">Stock OK</span>
                                    @endif
                                </td>
{{--                                <td>--}}
{{--                                    <a href="{{route('admin.orders.purchase.index')}}"--}}
{{--                                       class="btn btn-sm btn-primary">Order</a>--}}
{{--                                </td>--}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No low stock products found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="float-end">{{ $productWise->links('pagination::bootstrap-5') }}</p>
            </div>
        </div>
    </div>

    @include('layouts.common.datatable')
</x-app-layout>