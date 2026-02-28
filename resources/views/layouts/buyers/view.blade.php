<!-- View Supplier Modals -->
@foreach($customers as $customer)
    <div class="modal fade" id="viewCustomerModal{{ $customer->id }}" tabindex="-1"
         aria-labelledby="viewCustomerModalLabel{{ $customer->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCustomerModalLabel{{ $customer->id }}">Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th>Name</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>Customer Type</th>
                            <td>{{ $customer->customer_type }}</td>
                        </tr>
                        <tr>
                            <th>Street Address</th>
                            <td>{{ $customer->street_address }}</td>
                        </tr>

                        <tr>
                            <th>City</th>
                            <td>{{ $customer->city }}</td>
                        </tr>
                        <tr>
                            <th>Post Code</th>
                            <td>{{ $customer->postal_code }}</td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>{{ $customer->country }}</td>
                        </tr>
                        <tr>
                            <th>Company</th>
                            <td>{{ $customer->company_name ?? '-' }} ({{ $customer->company_email ?? '-' }})
                            </td>
                        </tr>
                        <tr>
                            <th>Bank Details</th>
                            <td>{{ $customer->bank_name ?? '-' }} /
                                {{ $customer->account_number ?? '-' }} /
                                {{$customer->bank_branch}}</td>
                        </tr>
                        <tr>
                            <th>Tax</th>
                            <td>{{ $customer->tax ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td id="customer_status{{ $customer->id }}">{{ $customer->status ?? '-' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endforeach