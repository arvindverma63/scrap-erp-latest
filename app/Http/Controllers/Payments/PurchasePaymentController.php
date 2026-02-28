<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use App\Models\Invoice;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchasePaymentController extends Controller
{

    protected $notificationRepo;

    public function __construct(
        NotificationRepository $notificationRepo
    )
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Show all payments.
     */
    public function index()
    {
        $payments = PurchasePayment::with('invoice')->latest()->get();
        return response()->json($payments);
    }

    /**
     * Store a new payment for an invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:Cash,Bank Transfer,Credit Card,Cheque,Wire,Other',
            'reference_no' => 'nullable|string|max:100',
            'amount' => ['required', 'numeric', 'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    $invoice = DB::table('invoices')->find($request->invoice_id);
                    $purchaseOrder = DB::table('purchase_orders')->where('id', $invoice->purchase_order_id)->first();
                    if ($purchaseOrder && $value > $purchaseOrder->paid_amount) {
                        $fail("The {$attribute} must not be greater than the purchase order due ({$purchaseOrder->paid_amount}).");
                    }
                }],
            'notes' => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {
            $invoice = DB::table('invoices')->find($request->invoice_id);
            $purchaseOrder = DB::table('purchase_orders')->where('id', $invoice->purchase_order_id)->first();
            $wallet = Wallet::where('cashier_id', $purchaseOrder->created_by)->where('date', date(Carbon::now()->toDateString()))->first();
            if ($wallet->balance >= $request->amount) {
                $wallet->balance = $wallet->balance - $request->amount;
                $wallet->save();
            } else {
                return back('error', 'Wallet balance is too low');
            }


            $payment = PurchasePayment::create([
                'invoice_id' => $validated['invoice_id'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_no' => $validated['reference_no'] ?? null,
                'amount' => $validated['amount'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            $userId = Auth::user()->id;
            $users = User::whereIn('id', [3, $purchaseOrder->created_by])->pluck('id');
            foreach($users as $user){
                $this->notificationRepo->create([
                    'user_id' => $user,
                    'title' => 'New Purchase Payment',
                    'message' => 'User #' . Auth::user()->name . ' Created a Purchase Payment ' . number_format($request->amount, 2) . ' JMD.',
                    'type' => 'purchase payments',
                    'is_read' => false,
                ]);
            }
            

            // Optionally update invoice balance
            $invoice = Invoice::find($validated['invoice_id']);
            if ($invoice) {
                $paid = $invoice->purchasePayments()->sum('amount');
                $invoice->balance_amount = max(0, $invoice->grand_total - $paid);
                $invoice->status = ($invoice->balance_amount == 0) ? 'Paid' : 'Unpaid';
                $invoice->save();

                $purchasePayment = PurchaseOrder::find($invoice->purchase_order_id);
                $purchasePayment->paid_amount = $purchasePayment->paid_amount - $request->amount;
                $purchasePayment->save();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }


    }

    /**
     * Show a single payment.
     */
    public function show($id)
    {
        $payment = PurchasePayment::with('invoice')->findOrFail($id);
        return response()->json($payment);
    }

    /**
     * Update payment.
     */
    public function update(Request $request, $id)
    {
        $payment = PurchasePayment::findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'sometimes|date',
            'payment_method' => 'sometimes|in:Cash,Bank Transfer,Credit Card,Cheque,Wire,Other',
            'reference_no' => 'nullable|string|max:100',
            'amount' => 'sometimes|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        // Update invoice balance again
        $invoice = $payment->invoice;
        if ($invoice) {
            $paid = $invoice->purchasePayments()->sum('amount');
            $invoice->balance_amount = max(0, $invoice->grand_total - $paid);
            $invoice->status = ($invoice->balance_amount == 0) ? 'Paid' : 'Unpaid';
            $invoice->save();
        }

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Delete payment.
     */
    public function destroy($id)
    {
        $payment = PurchasePayment::findOrFail($id);
        $invoice = $payment->invoice;

        $payment->delete();

        // Update invoice balance
        if ($invoice) {
            $paid = $invoice->purchasePayments()->sum('amount');
            $invoice->balance_amount = max(0, $invoice->grand_total - $paid);
            $invoice->status = ($invoice->balance_amount == 0) ? 'Paid' : 'Unpaid';
            $invoice->save();
        }

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }
}
