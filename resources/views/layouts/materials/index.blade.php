<x-head title="Materials"/>

<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Heading & Actions -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Materials</h3>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                    <i class="fas fa-plus me-1"></i> Add Material
                </button>
            </div>

            <!-- Materials Table -->
            <div class="table-responsive card shadow-sm rounded-3">
                <table class="table table-hover align-middle mb-0 fs-6">
                    <thead class="table-light">
                        <tr>
                            <th>Sr.No.</th>
                            <th>Material Name</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Rate (₹/unit)</th>
                            <th>Stock Qty</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Heavy Melting Scrap</td>
                            <td>Ferrous Scrap</td>
                            <td>Ton</td>
                            <td>₹40,000</td>
                            <td>120</td>
                            <td><span class="badge bg-success">Available</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit "></i></button>
                                <button class="btn btn-sm btn-outline-danger"> <i class="fas fa-edit "></i> </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Copper Wire Scrap</td>
                            <td>Non-Ferrous Scrap</td>
                            <td>Kg</td>
                            <td>₹700</td>
                            <td>2500</td>
                            <td><span class="badge bg-warning text-dark">Low Stock</span></td>
                           <td>
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit "></i></button>
                                <button class="btn btn-sm btn-outline-danger"> <i class="fas fa-edit "></i> </button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>HDPE Granules</td>
                            <td>Plastic Scrap</td>
                            <td>Kg</td>
                            <td>₹120</td>
                            <td>5000</td>
                            <td><span class="badge bg-success">Available</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit "></i></button>
                                <button class="btn btn-sm btn-outline-danger"> <i class="fas fa-edit "></i> </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add Material Modal -->
            <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="addMaterialModalLabel">Add New Material</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="materialName" class="form-label">Material Name</label>
                                        <input type="text" class="form-control form-control-sm" id="materialName"
                                            placeholder="Enter material name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="materialCategory" class="form-label">Category</label>
                                        <select class="form-select form-select-sm" id="materialCategory">
                                            <option value="">Select Category</option>
                                            <option>Ferrous Scrap</option>
                                            <option>Non-Ferrous Scrap</option>
                                            <option>Plastic Scrap</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="materialUnit" class="form-label">Unit</label>
                                        <select class="form-select form-select-sm" id="materialUnit">
                                            <option value="Ton">Ton</option>
                                            <option value="Kg">Kg</option>
                                            <option value="Piece">Piece</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="materialRate" class="form-label">Rate (₹/unit)</label>
                                        <input type="number" class="form-control form-control-sm" id="materialRate"
                                            placeholder="Enter rate">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="materialStock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control form-control-sm" id="materialStock"
                                            placeholder="Enter stock qty">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="materialStatus" class="form-label">Status</label>
                                        <select class="form-select form-select-sm" id="materialStatus">
                                            <option>Available</option>
                                            <option>Low Stock</option>
                                            <option>Out of Stock</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm">  <i class="fas fa-save me-1"></i> Save Material</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>