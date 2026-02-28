<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\SalesPayment;
use App\Models\SalesInvoice;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class SalesPaymentController extends Controller
{

    protected $notificationRepo;

    public function __construct(
        NotificationRepository $notificationRepo
    )
    {
        $this->notificationRepo = $notificationRepo;
    }


    /**
     * Display all sales payments.
     */
    public function index()
    {
        $payments = SalesPayment::with('invoice')->latest()->get();

        return response()->json($payments);
    }

    /**
     * Store a new sales payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:Cash,Bank Transfer,Credit Card,Cheque,Wire,Other',
            'reference_no' => 'nullable|string|max:100',
            'amount' => ['required', 'numeric', 'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    $invoice = DB::table('sales_invoices')->find($request->sales_invoice_id);
                    $salesOrder = DB::table('sales_orders')->where('id', $invoice->sales_order_id)->first();
                    if ($salesOrder && $value > $salesOrder->paid_amount) {
                        $fail("The {$attribute} must not be greater than the purchase order due ({$salesOrder->paid_amount}).");
                    }
                }],
            'notes' => 'nullable|string',
        ]);

        $invoice = DB::table('sales_invoices')->find($request->sales_invoice_id);
        $salesOrder = DB::table('sales_orders')->where('id', $invoice->sales_order_id)->first();
        $usdToJmdRate = Setting::where('key', 'usd_to_jmd_rate')->first()->value;
        $wallet = Wallet::where('cashier_id', $salesOrder->created_by)->where('date',date(Carbon::now()->toDateString()))->first();
        $wallet->balance = $wallet->balance + (round($usdToJmdRate * $request->amount, 2));
        $wallet->save();

        $invoice = DB::table('sales_invoices')->find($request->sales_invoice_id);
        $saleOrder = DB::table('sales_orders')->where('id', $invoice->sales_order_id)->first();
        if ($saleOrder) {
            DB::table('sales_orders')->where('id', $invoice->sales_order_id)->update([
                'paid_amount' => $saleOrder->paid_amount - $request->amount,
            ]);
        }

        $payment = SalesPayment::create([
            'sales_invoice_id' => $validated['sales_invoice_id'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'reference_no' => $validated['reference_no'] ?? null,
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id() ?? null,
        ]);

        $userId = Auth::user()->id;
        $users = User::whereIn('id', [3, $salesOrder->created_by])->pluck('id');
        foreach($users as $key => $user){
        $this->notificationRepo->create([
            'user_id' => $user,
            'title' => 'New Sales Payment',
            'message' => 'User #' . Auth::user()->name . ' Added a Sales Payment ' . number_format($request->amount, 2) . ' USD.',
            'type' => 'sales payments',
            'is_read' => false,
        ]);
        }
        

        // Update invoice balance and status
        $this->updateInvoiceBalance($validated['sales_invoice_id']);

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show a single payment.
     */
    public function show($id)
    {
        $payment = SalesPayment::with('invoice')->findOrFail($id);
        return response()->json($payment);
    }

    /**
     * Update payment record.
     */
    public function update(Request $request, $id)
    {
        $payment = SalesPayment::findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'sometimes|date',
            'payment_method' => 'sometimes|in:Cash,Bank Transfer,Credit Card,Cheque,Wire,Other',
            'reference_no' => 'nullable|string|max:100',
            'amount' => 'sometimes|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        $this->updateInvoiceBalance($payment->sales_invoice_id);

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Delete a payment.
     */
    public function destroy($id)
    {
        $payment = SalesPayment::findOrFail($id);
        $invoiceId = $payment->sales_invoice_id;
        $payment->delete();

        $this->updateInvoiceBalance($invoiceId);

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Helper function to recalculate invoice balance and status.
     */
    protected function updateInvoiceBalance($invoiceId)
    {
        $invoice = SalesInvoice::find($invoiceId);

        if ($invoice) {
            $totalPaid = $invoice->salesPayments()->sum('amount');
            $balance = max(0, $invoice->grand_total - $totalPaid);

            $invoice->balance_amount = $balance;
            $invoice->partially_paid = $totalPaid;

            if ($balance == 0) {
                $invoice->status = 'Paid';
            } elseif ($totalPaid > 0 && $balance > 0) {
                $invoice->status = 'Unpaid'; // or 'Partially Paid' if you want a new enum
            } else {
                $invoice->status = 'Unpaid';
            }

            $invoice->save();
        }
    }
}
