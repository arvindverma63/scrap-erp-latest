<!-- View Sales Payments Modal -->
<div class="modal fade" id="viewSalesPaymentsModal{{ $payment->invoice->sellingOrder->id }}" tabindex="-1"
    aria-labelledby="viewSalesPaymentsModalLabel{{ $payment->invoice->sellingOrder->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="viewSalesPaymentsModalLabel{{ $payment->invoice->sellingOrder->id }}">
                    Payments for Invoice #{{ $payment->invoice->sellingOrder->invoice->invoice_number ?? '' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if ($payment->invoice->sellingOrder->invoice && $payment->invoice->sellingOrder->invoice->salesPayments && $payment->invoice->sellingOrder->invoice->salesPayments->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Reference No</th>
                                <th>Amount</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->invoice->sellingOrder->invoice->salesPayments ?? [] as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Paid:</th>
                                <th colspan="2">
                                    {{ number_format(optional($payment->invoice->sellingOrder->invoice)->salesPayments->sum('amount') ?? 0, 2) }}
                                </th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Balance Due:</th>
                                <th colspan="2">
                                    {{ number_format(optional($payment->invoice->sellingOrder->invoice)->grand_total - (optional($payment->invoice->sellingOrder->invoice)->salesPayments->sum('amount') ?? 0), 2) }}
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