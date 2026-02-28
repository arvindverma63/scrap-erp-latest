<x-app-layout>
    <div class="page-content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-3 mt-4">General Settings</h3>

            @if(session('success'))
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

            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <form action="{{ route('admin.settings.general.update') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method("PUT")

                        {{-- 1st Row --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small mb-1">Website Name</label>
                                <input type="text" class="form-control form-control-sm" name="website_name"
                                       placeholder="Enter name" value="{{ $settings['website_name'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small mb-1">Company Name</label>
                                <input type="text" class="form-control form-control-sm" name="company_name"
                                       placeholder="Enter company name" value="{{ $settings['company_name'] ?? '' }}">
                            </div>
                        </div>

                        {{-- 2nd Row --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small mb-1">Contact Email</label>
                                <input type="email" class="form-control form-control-sm" name="website_email"
                                       placeholder="contact@example.com" value="{{ $settings['website_email'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small mb-1">Contact Phone</label>
                                <input type="text" class="form-control form-control-sm" name="phone_number"
                                       placeholder="9999999999" value="{{ $settings['phone_number'] ?? '' }}">
                            </div>
                        </div>

                        {{-- 3rd Row --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small mb-1">Currency Symbol</label>
                                <input type="text" class="form-control form-control-sm" name="currency_symbol"
                                       placeholder="Enter Symbol" value="{{ $settings['currency_symbol'] ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small mb-1">Favicon</label>
                                <div class="d-flex align-items-center">
                                    @php
                                        $favicon = $settings['admin_logo'] ?? null;
                                    @endphp
                                    <img src="{{ $favicon ?? asset('assets/images/cm-o-logo.png') }}" alt="Favicon"
                                         class="me-3 border rounded" width="32" height="32">
                                    <input type="file" class="form-control form-control-sm" name="admin_logo">
                                </div>
                                <div class="form-text">Recommended: 32x32 PNG or ICO.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small mb-1">Company Logo</label>
                                <div class="d-flex align-items-center">
                                    @php
                                        $companyLogo = $settings['company_logo'] ?? null;
                                    @endphp
                                    <img src="{{ $companyLogo ?? asset('assets/images/cm-o-logo.png') }}"
                                         alt="Company Logo" class="me-3 border rounded" width="64" height="64">
                                    <input type="file" class="form-control form-control-sm" name="company_logo">
                                </div>
                                <div class="form-text">Recommended: 64x64 PNG.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label small mb-1">Buying Currency</label>
                                <input type="text" class="form-control form-control-sm" name="buying_currency_symbol"
                                       placeholder="Enter Buying Currency"
                                       value="{{ $settings['buying_currency_symbol'] ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small mb-1">Selling Currency</label>
                                <input type="text" class="form-control form-control-sm" name="selling_currency_symbol"
                                       placeholder="Enter Selling Currency"
                                       value="{{ $settings['selling_currency_symbol'] ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small mb-1">USD to JMD Rate</label>
                                <input type="text" class="form-control form-control-sm" name="usd_to_jmd_rate"
                                       placeholder="Enter per Rate"
                                       value="{{ $settings['usd_to_jmd_rate'] ?? '' }}">
                                <span class="text-secondary">(1 USD in JMD rate)</span>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label small mb-1">Company Address</label>
                                <textarea class="form-control form-control-sm" rows="2" name="company_address"
                                          placeholder="Enter company address">{{ $settings['company_address'] ?? '' }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label small mb-1">Footer Text</label>
                                <input type="text" class="form-control form-control-sm" name="footer_text"
                                       placeholder="© 2025 My ERP System. All rights reserved."
                                       value="{{ $settings['footer_text'] ?? '' }}">
                            </div>

                            <div class="col-12 mb-3">
                                <div class="modal-title"><h5>Cash Count Currency</h5></div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="currencyTable">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Currency (JMD)</th>
                                                <th style="width: 50px;">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($settings['currency_count'] == null)
                                                <tr>
                                                    <td>
                                                        <input type="text" name="currency_count[]" value=""
                                                               class="form-control currency"
                                                               placeholder="e.g. 2000" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-success btn-sm addRow">
                                                            <i class="fa fa-plus-circle"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#currencyTable tbody');


            // Get currency_count from Blade as JSON
            let currencyData = @json($settings['currency_count'] ?? '[]'); // default to empty array

            // If it comes as a JSON string, parse it
            if (typeof currencyData === 'string') {
                currencyData = JSON.parse(currencyData);
            }

            // Render existing currency values as rows
            currencyData.forEach((value, index) => {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
            <td>
                <input type="text" name="currency_count[]" class="form-control" placeholder="e.g. 2000" value="${value}" required>
            </td>
            <td class="text-center">
                ${index === 0
                    ? `<button type="button" class="btn btn-success btn-sm addRow">
                           <i class="fa fa-plus-circle"></i>
                       </button>`
                    : `<button type="button" class="btn btn-danger btn-sm deleteRow">
                           <i class="fa fa-trash"></i>
                       </button>`}
            </td>
        `;
                tableBody.appendChild(newRow);
            });


            // Add new row
            tableBody.addEventListener('click', function (e) {
                if (e.target.closest('.addRow')) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                <td><input type="text" name="currency_count[]" class="form-control" placeholder="e.g. 2000" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm deleteRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;
                    tableBody.appendChild(newRow);
                }

                // Delete a row
                if (e.target.closest('.deleteRow')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
</x-app-layout>