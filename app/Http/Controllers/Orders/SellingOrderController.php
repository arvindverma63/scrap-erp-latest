<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Services\Invoice as ServicesInvoice;
use App\Jobs\SendInvoiceEmail;
use App\Mail\OrderMail;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use App\Models\Inventory;
use App\Models\SalesPayment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WeightUnit;
use App\Models\SellingOrder;
use App\Models\Setting;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Allmails\SendInvoiceMail;
use App\Models\CashCount;
use App\Models\Country;
use App\Models\Notification;
use App\Models\SellingToProduct;
use Carbon\Carbon;
use PDF;

class SellingOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */

    public function index(Request $request)
    {
        $query = SellingOrder::with(['customer', 'cashier', 'items.product', 'invoice'])
            ->when(!empty($request->from_date) && !empty($request->to_date), function ($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59'
                ]);
            });
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Search invoice number from related invoices
                $q->orWhereHas('invoice', function ($sub) use ($search) {
                    $sub->where('invoice_number', 'like', "%{$search}%");
                });

                // Search customer name
                $q->orWhereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                // Search status
                $q->orWhere('status', 'like', "%{$search}%");
            });
        }

        $user = auth()->user();
        if (!in_array($user->roles->first()->id, [1, 5])) {
            $query->where('created_by', $user->id);
        }

        $invoiceNumber = Setting::where('key', 'invoice_prefix')->value('value')
            . str_pad(SalesInvoice::max('id') + 1, 5, '0', STR_PAD_LEFT);

        $sellingOrders = $query->orderBy('created_at', 'desc')->paginate(30);


        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $weightUnits = WeightUnit::orderBy('name')->get();


        foreach ($sellingOrders as &$order) {
            $order->cash_count = CashCount::where('cashier_id', $order->created_by)
                ->where('date', date(Carbon::now()->toDateString()))->count();
        }

        return view('layouts.selling.index', compact(
            'sellingOrders',
            'customers',
            'products',
            'weightUnits',
            'invoiceNumber',
        ));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $weightUnits = WeightUnit::orderBy('name')->get();
        $invoiceNumber = Setting::where('key', 'invoice_prefix')->value('value')
            . str_pad(SalesInvoice::max('id') + 1, 5, '0', STR_PAD_LEFT);

        $countries = Country::orderBy('name', 'ASC')->get();

        return view('layouts.selling.create', compact(
            'customers',
            'products',
            'weightUnits',
            'invoiceNumber',
            'countries'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'nullable|unique:sales_invoices,invoice_number|max:100', // adjust table name
            'customer_id' => 'nullable|exists:customers,id',

            'product_id.*' => 'required|exists:products,id',
            'weight_quantity.*' => 'required|numeric|min:0',
            'weight_unit_id.*' => 'required|exists:weight_units,id',
            'rate_per_unit.*' => 'required|numeric|min:0',
            'total_amount.*' => 'required|numeric|min:0',

            'partially_paid' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'less_scale_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank,Online,Credit,Wire',
            'status' => 'required|in:Completed,Pending,Voided',
        ]);

        try {
            // compute total from line items
            $totalAmount = array_sum($request->total_amount);
            $finalAmount = $totalAmount - ($request->less_scale_fee ?? 0);
            if ($totalAmount < $request->less_scale_fee) {
                return back()->with('error', 'Less Scale fee not greater then total amount');
            }
            if ($request->partially_paid > $finalAmount) {
                return back()->with('error', "Paid amount {$request->partially_paid} cannot be greater than final amount {$finalAmount}.");
            }
            $sellingOrder = DB::transaction(function () use ($request, $totalAmount, $finalAmount) {

                // 2.1 Create Selling Order
                $sellingOrder = SellingOrder::create([
                    'customer_id' => $request->customer_id,
                    'less_scale_fee' => $request->less_scale_fee,
                    'total_amount' => $finalAmount,
                    'paid_amount' => $request->paid_amount ?? 0,
                    'payment_method' => $request->payment_method,
                    'status' => $request->status,
                    'created_by' => Auth::id(),
                ]);

                // 2.2 Create Selling Order line items
                foreach ($request->product_id as $index => $productId) {
                    SellingToProduct::create([
                        'selling_order_id' => $sellingOrder->id,
                        'product_id' => $request->product_id[$index],
                        'quantity' => $request->weight_quantity[$index],
                        'weight_unit_id' => $request->weight_unit_id[$index],
                        'unit_price' => $request->rate_per_unit[$index],
                        'total_amount' => $request->total_amount[$index],
                    ]);
                }

                // 2.3 Handle invoice number (auto-generate if empty)
                $invoiceNumber = $request->invoice_number;
                if (!$invoiceNumber) {
                    $invoiceNumber = 'SINV-' . now()->format('Ymd') . '-' . strtoupper(uniqid());
                }

                // 2.4 Create Sales Invoice
                SalesInvoice::create([
                    'sales_order_id' => $sellingOrder->id, // 👈 ensure column exists
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(7),
                    'sub_total' => $totalAmount,
                    'tax_amount' => 0,
                    'partially_paid' => $request->partially_paid ?? 0,
                    'discount' => 0,
                    'grand_total' => $finalAmount,
                    'notes' => 'Auto-generated invoice for selling order',
                    'status' => 'Unpaid',
                    'created_by' => Auth::id(),
                ]);

                return $sellingOrder; // 🔴 important
            });

            // 3️⃣ Redirect after success
            return redirect()->route('admin.orders.selling.index')
                ->with('success', 'Selling order & invoice created successfully.');
        } catch (\Throwable $e) {
            // 4️⃣ Log error and redirect back
            \Log::error('Selling order creation failed: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the selling order. Please try again.');
        }
    }

    /**
     * Update Sales order only (no products in this example).
     */
    public function update(Request $request, SellingOrder $sellingOrder)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'weight_unit_id' => 'nullable|exists:weight_units,id',
            'weight_quantity' => 'required|numeric|min:0',
            'rate_per_unit' => 'required|numeric|min:0',
            'less_scale_fee' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank,Online,Credit,Wire',
        ]);

        $sellingOrder->update([
            'transaction_id' => $request->transaction_id,
            'supplier_id' => $request->supplier_id,
            'weight_unit_id' => $request->weight_unit_id,
            'weight_quantity' => $request->weight_quantity,
            'rate_per_unit' => $request->rate_per_unit,
            'less_scale_fee' => $request->less_scale_fee,
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount ?? 0,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('admin.orders.selling.index')
            ->with('success', 'Sales order updated successfully.');
    }

    /**
     * Delete purchase order.
     */
    public function destroy(SellingOrder $sellingOrder)
    {
        $sellingOrder->delete();

        return redirect()->route('admin.orders.selling.index')
            ->with('success', 'Sales order deleted successfully.');
    }

    public function updateStatusSales($id)
    {

        try {
            // SendInvoiceEmail::dispatch(['name' => 'test1']);

            $sellingOrder = SellingOrder::with('items')->findOrFail($id);

            $order = SellingOrder::with(['customer', 'items.product', 'items.weightUnit'])
                ->findOrFail($id);
            $setting = Setting::all();
            Mail::to('abc@test.com')->send(new SendInvoiceMail($order, $setting));

            // Check if already completed
            if ($sellingOrder->status === "Completed") {
                return redirect()->back()->with('info', 'Order is already completed.');
            }

            DB::transaction(function () use ($sellingOrder) {
                // Update status
                $sellingOrder->status = "Completed";
                $sellingOrder->save();

                // Create Inventory entries
                foreach ($sellingOrder->items as $item) {
                    Inventory::create([
                        'product_id' => $item->product_id,
                        'order_id' => $sellingOrder->id,
                        'weight_unit_id' => $item->weight_unit_id,
                        'quantity' => $item->quantity,
                        'amount' => $item->total_amount,
                        'inventory_type' => 'Sale',
                        'created_by' => Auth::id(),
                        'user_id' => $sellingOrder->customer_id,
                    ]);
                }
            });


            return redirect()->back()->with('success', 'Status Updated & Inventory Created Successfully');
        } catch (\Exception $e) {
            return $e->getMessage();
            // Log the error for debugging
            \Log::error('Failed to update order status: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateStatusVoided($id)
    {
        try {
            $sellingOrder = SellingOrder::with('items')->findOrFail($id);

            // Check if already completed
            if ($sellingOrder->status === "Completed") {
                return redirect()->back()->with('info', 'Purchase order is already completed.');
            }

            DB::transaction(function () use ($sellingOrder) {
                // Update status
                $sellingOrder->status = "Voided";
                $sellingOrder->save();
            });

            return redirect()->back()->with('success', 'Status Updated & Inventory Created Successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update purchase order status: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function downloadSalesInvoice(ServicesInvoice $invoice, $id)
    {
        return $invoice->downloadSalesInvoice($id);
    }

    public function invoicePage($id)
    {
        $order = SellingOrder::with(['customer', 'items.product', 'items.weightUnit'])
            ->findOrFail($id);

        $setting = Setting::all();

        return view('layouts.sales-reports.invoice', compact('order', 'setting'));
    }

    public function updateStatus(Request $request, $id)
    {

        $sellingOrder = SellingOrder::with(['invoice', 'items', 'customer'])->findOrFail($id);
        $sellingOrder->status = $request->status;
        if ($request->status == 'Completed') {

            $wallet = Wallet::where('cashier_id', $sellingOrder->created_by)->where('date', date(Carbon::now()->toDateString()))->first();
            if (!$wallet) {
                return back()->with('error', "Wallet balance is insufficient or today's cash has not been deposited");
            }
            $usdToJmdRate = Setting::where('key', 'usd_to_jmd_rate')->first()->value;
            if ($wallet->balance < ($sellingOrder->less_scale_fee * $usdToJmdRate)) {
                return back()->with('error', 'Wallet balance is too low');
            }

            foreach ($sellingOrder->items as $item) {
                $product = Product::find($item->product_id);
                $quantitys = (int)$product->total_quantity - (int)$item->quantity;
                if ($quantitys < 0) {
                    return back()->with(
                        'error',
                        "Insufficient stock for " . $product->name . " Available quantity: " . $product->total_quantity
                    );
                }
            }
        }
        DB::beginTransaction();
        try {
            // DB::transaction(function () use ($sellingOrder, $request) {
            if ($request->status == 'Completed') {
                $wallet = Wallet::where('cashier_id', $sellingOrder->created_by)->where('date', date(Carbon::now()->toDateString()))->first();
                $usdToJmdRate = Setting::where('key', 'usd_to_jmd_rate')->first()->value;
                $wallet->balance = (float)$wallet->balance
                    + round((($sellingOrder->total_amount - $sellingOrder->paid_amount) * $usdToJmdRate), 2)
                    - round((($sellingOrder->less_scale_fee) * $usdToJmdRate), 2);
                $wallet->save();
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'cashier_id' => $sellingOrder->created_by,
                    'type' => 'credit',
                    'amount' => ($sellingOrder->total_amount - $sellingOrder->paid_amount),
                    'description' => 'Purchase Order #' . $sellingOrder->id,
                    'created_by' => Auth::id(),
                ]);
                SalesPayment::create([
                    'sales_invoice_id' => $sellingOrder->invoice->id,
                    'payment_date' => date('Y-m-d H:i:s'),
                    'payment_method' => $sellingOrder->payment_method,
                    'reference_no' => 'Initial Payment',
                    'amount' => ($sellingOrder->total_amount - $sellingOrder->paid_amount),
                    'notes' => 'Initial Payment',
                    'created_by' => Auth::id(),
                ]);

                $users = User::whereIn('id', [3, $sellingOrder->created_by])->pluck('id')->toArray();
                foreach ($users as $userId) {
                    Notification::create([
                        'user_id' => $userId,
                        'title' => "Invoice " . $sellingOrder->invoice->invoice_number . " is completed",
                        'message' => "Invoice " . $sellingOrder->invoice->invoice_number . " is completed and paid amount is credited,
                            less scale fee is deducted from wallet",
                        'type' => 'wallet',
                    ]);
                }

                $sellingOrder->items;
                foreach ($sellingOrder->items as $item) {
                    $product = Product::with(['weightUnit'])->find($item->product_id);
                    $quantity = (int)$product->total_quantity - (int)$item->quantity;
                    if ($quantity >= 0) {
                        $product->total_quantity = $quantity;
                        $product->save();
                        if ($product->total_quantity <= $product->low_stock_limit) {
                            $users = User::pluck('id')->toArray();
                            foreach ($users as $userId) {
                                Notification::create([
                                    'user_id' => $userId,
                                    'title' => $product->name . " Stock Alert",
                                    'message' => "The stock level for " . $product->name . " is low.Only " . $product->total_quantity . " " . $product->weightUnit->weight_unit_id . " units remaining.",
                                    'type' => 'LOW_STOCK',
                                ]);
                            }
                        }
                    } else {
                        return back()->with(
                            'error',
                            "Insufficient stock for " . $product->name . " Available quantity: " . $product->total_quantity
                        );
                    }

                    Inventory::create([
                        'product_id' => $item->product_id,
                        'order_id' => $sellingOrder->id,
                        'weight_unit_id' => $item->weight_unit_id,
                        'quantity' => $item->quantity,
                        'amount' => $item->total_amount,
                        'inventory_type' => 'Sale',
                        'created_by' => Auth::id(),
                        'user_id' => $sellingOrder->customer_id,
                    ]);
                }
            }

            $signatureData = $request->input('signature_data');
            if ($signatureData && $sellingOrder->invoice) {

                $signaturePath = public_path('signatures');
                if (!file_exists($signaturePath)) {
                    mkdir($signaturePath, 0755, true);
                }
                if (str_starts_with($signatureData, 'data:image/png;base64,')) {
                    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
                    $filename = 'signature_' . time() . '.png';
                    file_put_contents($signaturePath . '/' . $filename, $data);
                    $sellingOrder->invoice->digital_signature = '/signatures/' . $filename;
                } else {
                    $sellingOrder->invoice->digital_signature = $signatureData;
                }
                $sellingOrder->invoice->save();
            }
            $sellingOrder->save();
            // });
            //            $url = url('supplier/receipt/' . $id);

            if ($sellingOrder->customer->email) {
                try {
                    $order = SellingOrder::with(['customer', 'items.product', 'items.weightUnit', 'cashier'])
                        ->findOrFail($id);
                    // Generate invoice number
                    $invoiceNumber = 'CN-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
                    $setting = Setting::all();
                    $pdf = \PDF::loadView('invoices.sales-order', compact('order', 'invoiceNumber', 'setting'))
                        ->setPaper('A4', 'portrait')
                        ->setOption('margin-top', 0)
                        ->setOption('margin-bottom', 0)
                        ->setOption('margin-left', 0)
                        ->setOption('margin-right', 0);

                    $fileName = 'Invoice-' . time() . '.pdf';
                    $filePath = public_path('pdf/' . $fileName);
                    // Create directory if not exists
                    if (!file_exists(public_path('pdf'))) {
                        mkdir(public_path('pdf'), 0777, true);
                    }
                    file_put_contents($filePath, $pdf->output());
                    $data = ['name' => $sellingOrder->customer->name];
                    // 3. Send email with attachment
                    Mail::to($sellingOrder->customer->email)
                        ->send(new OrderMail($data, $filePath));
                } catch (\Exception $e) {

                }
            }

            try {
                $to = $sellingOrder->customer->customer_type == 'individual' ?
                    '+' . $sellingOrder->customer->country_code . $sellingOrder->customer->phone :
                    '+' . $sellingOrder->customer->country_code . $sellingOrder->customer->company_phone_number;
                $name = $sellingOrder->customer->customer_type == 'individual' ?
                    $sellingOrder->customer->name : $sellingOrder->customer->company_name;
                $url = env('APP_URL') . '/customer/invoice/' . $id;
                $message = 'Hello ' . $name . ', Here is your invoice ' . $url . ' Thank you. Regards CM Recycling.';
                $twilio = new TwilioService();
                $twilio->sendSms($to, $message);
                DB::commit();
                return redirect()->back()->with('success', 'Status Updated, Signature Saved & Inventory Created Successfully');
            } catch (\Exception $e) {
                DB::commit();
                return redirect()->back()->with('success', 'Status Updated, Signature Saved & Inventory Created Successfully, but message not send');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('info', $e->getMessage() . '_' . $e->getLine());
        }
    }
}
