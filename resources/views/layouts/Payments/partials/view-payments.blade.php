<!-- View Payments Modal -->
<div class="modal fade" id="viewPaymentsModal{{ $payment->invoice->purchaseOrder->id }}" tabindex="-1"
    aria-labelledby="viewPaymentsModalLabel{{ $payment->invoice->purchaseOrder->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="viewPaymentsModalLabel{{ $payment->invoice->purchaseOrder->id }}">
                    Payments for Invoice #{{ $payment->invoice->purchaseOrder->invoice->invoice_number ?? '' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if ($payment->invoice->purchaseOrder->invoice && $payment->invoice->purchaseOrder->invoice->purchasePayments->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Reference No</th>
                                <th>Notes</th>
                                <th>Amount {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->invoice->purchaseOrder->invoice->purchasePayments as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}
                                        {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total Paid:</th>
                                <th colspan="2">
                                    {{ number_format($payment->invoice->purchaseOrder->invoice->purchasePayments->sum('amount'), 2) }}
                                    {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Balance Due:</th>
                                <th colspan="2">
                                    {{ number_format($payment->invoice->purchaseOrder->invoice->grand_total - $payment->invoice->purchaseOrder->invoice->purchasePayments->sum('amount'), 2) }}
                                    {{ App\Models\Setting::where('key', 'currency_symbol')->first()->value }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <p class="text-muted">No payments recorded yet for this invoice.</p>
                @endif
            </div>
        </div>
    </div>
</div>
