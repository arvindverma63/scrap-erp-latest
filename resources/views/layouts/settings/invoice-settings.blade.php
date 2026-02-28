<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center page-title-box">
                <h3 class="fw-bold mb-0">Invoice & Receipt Settings</h3>
                <button class="btn btn-primary btn-sm" form="invoice-settings-form">
                    <i class="fas fa-gear"></i> Save Settings
                </button>
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

            <!-- Settings Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="invoice-settings-form" action="{{ route('admin.settings.invoice.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- Invoice Prefix -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Invoice Prefix</label>
                                <input type="text" name="prefix" class="form-control form-control-sm"
                                    value="{{ old('prefix', $settings['prefix']) }}"
                                    placeholder="e.g. {{ App\Models\Setting::where('key', 'invoice_prefix')->first()->value }}"
                                    required>
                            </div>

                            <!-- Starting Number -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Receipt Prefix</label>
                                <input type="text" name="receipt_prefix" class="form-control form-control-sm"
                                    value="{{ old('receipt_prefix', $settings['receipt_prefix']) }}"
                                    placeholder="{{ App\Models\Setting::where('key', 'receipt_prefix')->first()->value }}"
                                    required>
                            </div>

                            <!-- Receipt Signature Upload -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Receipt Signature</label>
                                <input type="file" name="receipt_signature" class="form-control form-control-sm"
                                    accept="image/*">

                                @if(!empty($settings['receipt_signature']))
                                    <div class="mt-2">
                                        <img src="{{ $settings['receipt_signature'] }}"
                                            alt="Receipt Signature" style="max-height:100px;">
                                    </div>
                                @endif
                            </div>

                            <!-- Save Button -->
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm ">
                                    <i class="fas fa-floppy-disk"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>