<x-head title="Products" />


<x-app-layout>
    <div class="page-content">
        <div class="container-fluid role-wrapper">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Products</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-box me-1"></i> Add Product
                    </button>
                    <a href="{{route('admin.product.import.index')}}" class="btn btn-warning btn-sm">
                        <i class="fas fa-box me-1"></i> Import
                    </a>
                    
                </div>
            </div>
            <!-- Success/Error Messages -->
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
            <!-- Products Table -->
           <div class="card">
              <div class="card-body border-0">
                    <div class="table-responsive card shadow-sm border-0 rounded-3">
                        <table class="table table-hover align-middle mb-0 fs-6 border" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Product Name</th>
                                    <th>Unit</th>
                                    <th>Sale Price
                                    </th>
                                    <th>Purchase Price
                                    </th>
                                    <th>Company Price
                                    </th>
                                    <th>Loyal Price
                                    </th>
                                    <th>Stock</th>
                                    <th>Low Stock</th>
                                    <th>High Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                    <tr>
                                        <td>{{ $products->firstItem() + $index }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->weightUnit ? $product->weightUnit->name : '-' }}</td>
                                        <td>${{ number_format($product->sale_price, 2) }}
                                            {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                        </td>
                                        <td>${{ number_format($product->purchase_price, 2) }}
                                            {{App\Models\Setting::where('key', 'buying_currency_symbol')->first()->value}}
                                        </td>
                                        <td>${{ number_format($product->company_sale_price, 2) }}
                                            {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                        </td>
                                        <td>${{ number_format($product->loyal_sale_price, 2) }}
                                            {{App\Models\Setting::where('key', 'selling_currency_symbol')->first()->value}}
                                        </td>
                                        <td>{{$product->total_quantity}}</td>
                                        <td>{{$product->low_stock_limit . " " . $product->weightUnit->name}}</td>
                                        <td>{{$product->high_stock_limit . " " . $product->weightUnit->name}}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editProductModal{{ $product->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach
                                @if($products->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center">No products found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                  <p class="float-end">{{ $products->links('pagination::bootstrap-4') }}</p>
              </div>
           </div>
        </div>
    </div>

    <!-- Add Product Modal -->

    @include('layouts.products.create')

    @include('layouts.products.edit')

    @include('layouts.common.datatable')
</x-app-layout>