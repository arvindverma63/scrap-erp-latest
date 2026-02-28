<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Services\Invoice as ServicesInvoice;
use App\Models\CashCount;
use App\Models\Country;
use App\Models\PurchaseOrder;
use App\Models\OrderToProduct;
use App\Models\Inventory;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\WeightUnit;
use App\Models\Notification;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\TwilioService;
use Carbon\Carbon;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use PDF;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */

    public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'supplier',
            'cashier',
            'orderItems.product',
            'invoice',
            'invoices',
        ])->when(!empty($request->from_date) && !empty($request->to_date), function ($q) use ($request) {
            $q->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // search in invoices
                $q->orWhereHas('invoices', function ($sub) use ($search) {
                    $sub->where('invoice_number', 'like', "%{$search}%");
                });

                // search in suppliers
                $q->orWhereHas('supplier', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                // search in purchase_orders.status
                $q->orWhere('status', 'like', "%{$search}%");
            });
        }


        // 🔹 Still allow specific filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $user = auth()->user();
        if (!in_array($user->roles->first()->id, [1, 5])) {
            $query->where('created_by', $user->id);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        foreach ($purchaseOrders as &$order) {
            $order->cash_count = CashCount::where('cashier_id', $order->created_by)
                ->where('date', date(Carbon::now()->toDateString()))->count();
        }


        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $weightUnits = WeightUnit::orderBy('name')->get();
        $receiptNumber = Setting::where('key', 'receipt_prefix')->value('value')
            . str_pad(Invoice::max('id') + 1, 5, '0', STR_PAD_LEFT);

        // $todayCashCount = CashCount::where('date', now()->format('Y-m-d'))->count();

        return view('layouts.buying.index', compact('purchaseOrders', 'suppliers', 'products', 'weightUnits', 'receiptNumber'));
    }


    public function show() {}


    /**
     * Show the form for creating a new purchase order.
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $weightUnits = WeightUnit::orderBy('name')->get();
        $receiptNumber = Setting::where('key', 'receipt_prefix')->value('value')
            . str_pad(Invoice::max('id') + 1, 5, '0', STR_PAD_LEFT);
        $countries = Country::orderBy('name', 'ASC')->get();
        return view('layouts.buying.create', compact('suppliers', 'products', 'weightUnits', 'receiptNumber', 'countries'));
    }

    /**
     * Store a newly created purchase order and related order_to_products.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'invoice_number' => 'nullable|unique:invoices,invoice_number|max:100',
            'product_id.*' => 'required|exists:products,id',
            'weight_quantity.*' => 'required|numeric|min:0',
            'weight_unit_id.*' => 'required|exists:weight_units,id',
            'rate_per_unit.*' => 'required|numeric|min:0',
            'total_amount.*' => 'required|numeric|min:0',
            'partially_paid' => 'nullable|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'balance_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'less_scale_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank,Online,Credit,Wire',
            'status' => 'required|in:Completed,Pending,Voided',
            'haulage_fee' => 'nullable|numeric|min:0',
            'handling_fee' => 'nullable|numeric|min:0',
        ]);

        // Calculate total purchase amount
        $totalAmount = array_sum($request->total_amount);
        $finalAmount = $totalAmount - ($request->less_scale_fee ?? 0) - ($request->haulage_fee ?? 0) + $request->handling_fee;
        if ($request->partially_paid > $finalAmount) {
            return back()->with('error', "Paid amount {$request->partially_paid} cannot be greater than final amount {$finalAmount}.");
        }
        // Include fees (minus scale, plus haulage)
        try {
            $purchaseOrder = DB::transaction(function () use ($request, $totalAmount, $finalAmount) {
                // Create Purchase Order
                $purchaseOrder = PurchaseOrder::create([
                    'supplier_id' => $request->supplier_id,
                    'weight_unit_id' => null,
                    'weight_quantity' => null,
                    'rate_per_unit' => null,
                    'less_scale_fee' => $request->less_scale_fee,
                    'haulage_fee' => $request->haulage_fee,
                    'handling_fee' => $request->handling_fee,
                    'total_amount' => $finalAmount,
                    'paid_amount' => $request->paid_amount ?? 0,
                    'payment_method' => $request->payment_method,
                    'status' => $request->status,
                    'created_by' => Auth::id(),
                ]);

                // Create related items
                foreach ($request->product_id as $index => $productId) {
                    $purchaseOrder->orderItems()->create([
                        'product_id' => $productId,
                        'quantity' => $request->weight_quantity[$index],
                        'weight_unit_id' => $request->weight_unit_id[$index],
                        'unit_price' => $request->rate_per_unit[$index],
                        'total_amount' => $request->total_amount[$index],
                    ]);
                }

                // Create invoice
                Invoice::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'invoice_number' => $request->invoice_number,
                    'invoice_date' => $request->invoice_date ?? now(),
                    'due_date' => now()->addDays(7),
                    'sub_total' => $totalAmount,
                    'tax_amount' => 0,
                    'partially_paid' => $request->partially_paid ?? 0,
                    'balance_amount' => $request->balance_amount,
                    'discount' => 0,
                    'grand_total' => $finalAmount,
                    'notes' => 'Auto-generated invoice for purchase order',
                    'status' => 'Unpaid',
                    'created_by' => Auth::id(),
                ]);
                return $purchaseOrder;
            });

            return redirect()
                ->route('admin.receiptPage', $purchaseOrder->id)
                ->with('success', 'Receipt created successfully.');
        } catch (\Throwable $e) {
            \Log::error('Purchase order creation failed: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Something went wrong while creating the purchase order.');
        }
    }


    /**
     * Update purchase order only (no products in this example).
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
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

        $purchaseOrder->update([
            'supplier_id' => $request->supplier_id,
            'weight_unit_id' => $request->weight_unit_id,
            'weight_quantity' => $request->weight_quantity,
            'rate_per_unit' => $request->rate_per_unit,
            'less_scale_fee' => $request->less_scale_fee,
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount ?? 0,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('admin.orders.purchase.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    /**
     * Delete purchase order.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('admin.orders.purchase.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    /**
     * Update status to Completed and create inventory entries.
     */
    public function updateStatusPurchase(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::with(['orderItems', 'invoice', 'supplier'])->findOrFail($id);
        if ($purchaseOrder->status === "Completed") {
            return back()->with('error', 'Purchase order is already completed.');
        }

        if ($request->status == 'Completed') {
            $wallet = Wallet::where('cashier_id', $purchaseOrder->created_by)->where('date', date(Carbon::now()->toDateString()))->first();
            if (!$wallet) {
                return back()->with('error', "Wallet balance is insufficient or today's cash has not been deposited");
            }

            if ($wallet->balance < ($purchaseOrder->total_amount - $purchaseOrder->paid_amount) + ($purchaseOrder->less_scale_fee + $purchaseOrder->haulage_fee)) {
                return back()->with('error', 'Wallet balance is too low');
            }
        }
        DB::beginTransaction();
        try {

            if ($request->status == 'Completed') {

                $wallet = Wallet::where('cashier_id', $purchaseOrder->created_by)->where('date', date(Carbon::now()->toDateString()))->first();
                if ($wallet->balance >= $purchaseOrder->paid_amount + $purchaseOrder->less_scale_fee + $purchaseOrder->haulage_fee) {
                    $wallet->balance = $wallet->balance - (($purchaseOrder->total_amount - $purchaseOrder->paid_amount) +
                        $purchaseOrder->less_scale_fee + $purchaseOrder->haulage_fee);
                    $wallet->save();
                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'cashier_id' => $purchaseOrder->created_by,
                        'type' => 'debit',
                        'amount' => ($purchaseOrder->total_amount - $purchaseOrder->paid_amount),
                        'description' => 'Purchase Order #' . $purchaseOrder->id,
                        'created_by' => auth()->user()->id,
                    ]);
                    // create initial payment record
                    PurchasePayment::create([
                        'invoice_id' => $purchaseOrder->invoice->id,
                        'payment_date' => date('Y-m-d H:i:s'),
                        'payment_method' => $purchaseOrder->payment_method,
                        'reference_no' => 'Initial Payment',
                        'amount' => $purchaseOrder->total_amount - $purchaseOrder->paid_amount,
                        'notes' => 'Initial Payment',
                        'created_by' => auth()->user()->id,
                    ]);

                    $users = User::whereIn('id', [3, $purchaseOrder->created_by])->pluck('id')->toArray();
                    foreach ($users as $userId) {
                        Notification::create([
                            'user_id' => $userId,
                            'title' => "Receipt " . $purchaseOrder->invoice->invoice_number . " is completed",
                            'message' => "Receipt " . $purchaseOrder->invoice->invoice_number . " is completed and paid amount,
                            less scale fee and haulage fee is deducted from wallet",
                            'type' => 'wallet',
                        ]);
                    }
                } else {
                    //                return redirect()->route('admin.receiptPage', $id)->with('info', 'Wallet balance is too low');
                    return back()->with('info', 'Wallet balance is too low');
                }

                foreach ($purchaseOrder->orderItems as $order) {
                    $product = Product::with(['weightUnit'])->find($order->product_id);
                    $product->total_quantity = (int)$product->total_quantity + (int)$order->quantity;
                    $product->save();
                    if ($product->total_quantity >= $product->high_stock_limit) {
                        $users = User::pluck('id')->toArray();
                        foreach ($users as $userId) {
                            Notification::create([
                                'user_id' => $userId,
                                'title' => $product->name . " Stock Alert",
                                'message' => "The stock level for " . $product->name . " is High. Total " . $product->total_quantity . " " . $product->weightUnit->weight_unit_id . " units Available.",
                                'type' => 'HIGH_STOCK',
                            ]);
                        }
                    }
                }
            }

            $purchaseOrder->status = $request->status;

            // Handle signature
            $signatureData = $request->input('signature_data');
            if ($signatureData && $purchaseOrder->invoice) {
                // Ensure signatures folder exists
                $signaturePath = public_path('signatures');
                if (!file_exists($signaturePath)) {
                    mkdir($signaturePath, 0755, true);
                }
                if (str_starts_with($signatureData, 'data:image/png;base64,')) {
                    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
                    $filename = 'signature_' . time() . '.png';
                    file_put_contents($signaturePath . '/' . $filename, $data);
                    $purchaseOrder->invoice->digital_signature = '/signatures/' . $filename;
                } else {
                    // Pre-uploaded URL
                    $purchaseOrder->invoice->digital_signature = $signatureData;
                }
                $purchaseOrder->invoice->save();
            }
            $purchaseOrder->save();
            // Create inventory
            foreach ($purchaseOrder->orderItems as $item) {
                Inventory::create([
                    'product_id' => $item->product_id,
                    'order_id' => $purchaseOrder->id,
                    'weight_unit_id' => $item->weight_unit_id,
                    'quantity' => $item->quantity,
                    'amount' => $item->total_amount,
                    'inventory_type' => 'Purchase',
                    'created_by' => auth()->user()->id,
                    'user_id' => $purchaseOrder->supplier_id,
                ]);
            }

            if ($purchaseOrder->supplier->email) {
                try {
                    $order = PurchaseOrder::with(['supplier', 'orderItems.product', 'orderItems.weightUnit'])
                        ->findOrFail($id);
                    $invoiceNumber = 'PO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
                    $setting = Setting::all();
                    $pdf = \PDF::loadView('invoices.purchase-order', compact('order', 'invoiceNumber', 'setting'))
                        ->setPaper([0, 0, 230, 800], 'portrait')
                        // 80mm paper width
                        ->setOption('margin-top', 0)
                        ->setOption('margin-bottom', 0)
                        ->setOption('margin-left', 0)
                        ->setOption('margin-right', 0);

                    $fileName = 'order_' . time() . '.pdf';
                    $filePath = public_path('pdf/' . $fileName);
                    // Create directory if not exists
                    if (!file_exists(public_path('pdf'))) {
                        mkdir(public_path('pdf'), 0777, true);
                    }
                    file_put_contents($filePath, $pdf->output());
                    $data = ['name' => $purchaseOrder->supplier->name];
                    // 3. Send email with attachment
                    Mail::to($purchaseOrder->supplier->email)
                        ->send(new OrderMail($data, $filePath));
                } catch (\Exception $e) {
                }
            }


            try {
                $to = $purchaseOrder->supplier->supplier_type == 'individual' ?
                    $purchaseOrder->supplier->country_code . $purchaseOrder->supplier->phone :
                    $purchaseOrder->supplier->country_code . $purchaseOrder->supplier->company_phone_number;
                $name = $purchaseOrder->supplier->supplier_type == 'individual' ?
                    $purchaseOrder->supplier->name : $purchaseOrder->supplier->company_name;
                $url = env('APP_URL') . '/supplier/receipt/' . $id;
                $message = 'Hello ' . $name . ', Here is your receipt ' . $url . ' Thank you. Regards CM Recycling.';
                $twilio = new TwilioService();
                $twilio->sendSms($to, $message);
                DB::commit();
                return back()->with('success', 'Status updated, signature saved, and inventory created successfully!');
            } catch (\Exception $e) {
                DB::commit();
                return back()->with('success', 'Status updated, signature saved, and inventory created successfully, but the message was not sent.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('info', $e->getMessage() . '_' . $e->getLine());
        }
    }


    public function downloadInvoice($id)
    {
        $invoiceService = new \App\Http\Services\Invoice();
        // return  $invoiceService->stream();
        return $invoiceService->downloadInvoice($id);
    }

    /**
     * Update status to Completed and create inventory entries.
     */
    public function updateStatusPurchaseVoided($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::with('orderItems')->findOrFail($id);

            // Check if already completed
            if ($purchaseOrder->status === "Completed") {
                return redirect()->back()->with('info', 'Purchase order is already completed.');
            }

            DB::transaction(function () use ($purchaseOrder) {
                // Update status
                $purchaseOrder->status = "Voided";
                $purchaseOrder->save();
            });

            return redirect()->back()->with('success', 'Status Updated & Inventory Created Successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update purchase order status: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function receiptPage($id)
    {
        $order = PurchaseOrder::with(['supplier', 'orderItems.product', 'orderItems.weightUnit'])
            ->findOrFail($id);
        $setting = Setting::all();
        return view('layouts.purchase-reports.receipt', compact('order', 'setting'));
    }
}