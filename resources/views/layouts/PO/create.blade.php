<x-app-layout>
    <div class="container py-4">
        <!-- Page Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold">Create Purchase Order</h4>
            <a href="{{ route('admin.PO.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>

        <!-- Purchase Order Form -->
        <div class="card shadow-sm rounded-3">
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <!-- Purchase Order No -->
                        <div class="col-md-4">
                            <label class="form-label">PO Number</label>
                            <input type="text" class="form-control" placeholder="e.g. PO-1001">
                        </div>

                        <!-- Date -->
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control">
                        </div>

                        <!-- Buyer -->
                        <div class="col-md-4">
                            <label class="form-label">Buyer</label>
                            <select class="form-select">
                                <option value="">Select Buyer</option>
                                <option>Steel Industries</option>
                                <option>AluCorp</option>
                                <option>Plastic Recyclers</option>
                                <option>Copper Works</option>
                            </select>
                        </div>

                        <!-- Material -->
                        <div class="col-md-6">
                            <label class="form-label">Material</label>
                            <input type="text" class="form-control" placeholder="Material name">
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" class="form-control" placeholder="e.g. 5 Tons">
                        </div>

                        <!-- Rate -->
                        <div class="col-md-3">
                            <label class="form-label">Rate</label>
                            <input type="text" class="form-control" placeholder="₹/kg">
                        </div>

                        <!-- Delivery Location -->
                        <div class="col-md-6">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" placeholder="City/Address">
                        </div>

                        <!-- Delivery Date -->
                        <div class="col-md-6">
                            <label class="form-label">Delivery Date</label>
                            <input type="date" class="form-control">
                        </div>

                        <!-- Contact Number -->
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" placeholder="+91 98765 43210">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Order Status</label>
                            <select class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label">Additional Notes</label>
                            <textarea class="form-control" rows="3" placeholder="Enter any notes about the order"></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light">Reset</button>
                        <button type="submit" class="btn btn-primary">Save Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
