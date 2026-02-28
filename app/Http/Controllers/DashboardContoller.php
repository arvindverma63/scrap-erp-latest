<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SellingOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WeightUnit;
use App\Repositories\InventoryRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class DashboardContoller extends Controller
{

    protected $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function index()
    {
        $query = Inventory::with(['product', 'weightUnit']);
        $inventories = $query->groupBy('product_id', 'inventory_type')
            ->selectRaw('product_id, inventory_type, SUM(quantity) as total_quantity')
            ->get();
        $productWise = $inventories->groupBy('product_id')->map(function ($rows) {
            $first = $rows->first() ?? null;
            $totalPurchased = $rows->where('inventory_type', 'Purchase')->sum('total_quantity') ?? 0;
            $totalSold = $rows->where('inventory_type', 'Sale')->sum('total_quantity') ?? 0;
            $netStock = $totalPurchased - $totalSold;
            return [
                'product_id' => $first ? $first->product_id : null,
                'product_name' => $first ? (optional($first->product)->name ?? 'Unknown Product') : 'Unknown Product',
                'weight_unit' => $first ? (optional($first->product->weightUnit)->name ?? 'Unknown Unit') : 'Unknown Unit',
                'total_quantity' => $netStock,
            ];
        })->values();
        $lowToHigh = $productWise->sortBy(function ($item) {
            return $item['total_quantity'];
        })->take(5)->values();
        $highToLow = $productWise->sortByDesc(function ($item) {
            return $item['total_quantity'];
        })->take(5)->values();
        $wallet = Wallet::where('date', date(Carbon::now()->toDateString()))->first();
        $lastRequest = WalletTransaction::where('type', 'topup')
            ->latest()
            ->first();
        $pendingCount = WalletTransaction::where('status', 'pending')->count();
        $year = now()->year;
        $chartData = [];
        $user = auth()->user();
        if(in_array($user->roles->first()->id, [1,5])){
        for ($i = 1; $i <= 12; $i++) {
            $data = [
                'receipt' => Inventory::whereYear('created_at', $year)->whereMonth('created_at', $i)->count(),
                'invoice' => PurchaseOrder::whereYear('created_at', $year)->whereMonth('created_at', $i)->count()
            ];
            $chartData[] = $data;
        }
        }else{
            $purchaseOrderIds = PurchaseOrder::where('created_by', auth()->user()->id)->pluck('id');
            $sellingOrderIds = SellingOrder::where('created_by', auth()->user()->id)->pluck('id');
        for ($i = 1; $i <= 12; $i++) {
            $purchseInventory = Inventory::whereIn('order_id', $purchaseOrderIds)->where('inventory_type', 'Purchase')->whereYear('created_at', $year)->whereMonth('created_at', $i)->count();
            $sellingInventory = Inventory::whereIn('order_id', $sellingOrderIds)->where('inventory_type', 'Sale')->whereYear('created_at', $year)->whereMonth('created_at', $i)->count();
            $data = [
                'receipt' => $purchseInventory + $sellingInventory,
                'invoice' => PurchaseOrder::where('created_by', auth()->user()->id)->whereYear('created_at', $year)->whereMonth('created_at', $i)->count()
            ];
            $chartData[] = $data;
        }
        }

        $data['pending_topup'] = WalletTransaction::where('status', 'pending')->count();
        $data['scrap_type'] = Product::count();
        $data['buyers'] = Customer::count();
        $data['suppliers'] = Supplier::count();
        // $data['ledger_count'] = PurchaseOrder::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count()
        //     + SellingOrder::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $data['ledger_count'] = PurchaseOrder::count() + SellingOrder::count();
        $data['pending_invoice'] = SellingOrder::where('status', 'Pending')->count();
//        $data['pending_receipts'] = PurchaseOrder::where('status', 'Pending')->count();
        $data['today_receipts'] = PurchaseOrder::whereDate('created_at', today())->where('status', 'Completed')->count();
        $data['today_invoices'] = SellingOrder::whereDate('created_at', today())->where('status', 'Completed')->count();


        return view('dashboard', compact(
            'lowToHigh',
            'highToLow',
            'wallet',
            'lastRequest',
            'pendingCount',
            'chartData',
            'data',
        ));
    }


    public function inventoryReportData(Request $request)
    {
        $filter = $request->get('filter', 'month'); // default = month

        $query = \App\Models\Inventory::query();

        if ($filter === 'today') {
            $query->whereDate('created_at', today())
                ->selectRaw('HOUR(created_at) as period, inventory_type, SUM(quantity) as total')
                ->groupBy('period', 'inventory_type');
        } elseif ($filter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->selectRaw('DAYNAME(created_at) as period, inventory_type, SUM(quantity) as total')
                ->groupBy('period', 'inventory_type');
        } elseif ($filter === 'year') {
            $query->whereYear('created_at', now()->year)
                ->selectRaw('MONTHNAME(created_at) as period, inventory_type, SUM(quantity) as total')
                ->groupBy('period', 'inventory_type');
        } else { // month
            $query->whereMonth('created_at', now()->month)
                ->selectRaw('DATE(created_at) as period, inventory_type, SUM(quantity) as total')
                ->groupBy('period', 'inventory_type');
        }

        $data = $query->orderBy('period', 'asc')->get()->groupBy('period');

        $labels = [];
        $purchases = [];
        $sales = [];

        foreach ($data as $period => $records) {
            $labels[] = $period;
            $purchases[] = $records->where('inventory_type', 'Purchase')->sum('total');
            $sales[] = $records->where('inventory_type', 'Sale')->sum('total');
        }

        return response()->json([
            'labels' => $labels,
            'purchases' => $purchases,
            'sales' => $sales,
        ]);
    }

    public function reports(Request $request)
    {

        $user = auth()->user();

        $cashiers = User::role(['cashier'])->get();

        if ($user->roles->first()->id == 2) {
            $data['final_wallet_balance'] = Wallet::where('cashier_id', $user->id)->whereBetween('date',
                [request('from_date'), request('to_date')])->sum('balance');
            $data['initial_balance'] = Wallet::where('cashier_id', $user->id)->whereBetween('date',
                [request('from_date'), request('to_date')])->sum('initial_balance');

            if (request('web_page') == 'RECEIPT') {
                $receipts = PurchaseOrder::where('created_by', $user->id)->with(['invoice' => function ($q) {
                    $q->select(['id', 'purchase_order_id', 'invoice_number']);
                }, 'supplier' => function ($q) {
                    $q->select(['id', 'name']);
                }, 'orderItems' => function ($q) {
                    $q->select(['id', 'purchase_order_id', 'product_id', 'quantity'])->with(['product:id,name']);
                }])->whereBetween('created_at', [request('from_date') . ' 00:00:00', request('to_date') . ' 23:59:59'])
                    ->where('status', 'Completed')->orderBy('id', 'DESC')->get();
                return view('layouts.reports.purchase-sale-report', compact('receipts', 'data', 'cashiers'));
            } elseif (request('web_page') == 'INVOICE') {
                $invoices = SellingOrder::where('created_by', $user->id)->with(['cashier' => function ($q) {
                    $q->select(['id', 'name']);
                }, 'customer' => function ($q) {
                    $q->select(['id', 'name']);
                }, 'invoice' => function ($q) {
                    $q->select(['id', 'invoice_number', 'sales_order_id']);
                }, 'items' => function ($q) {
                    $q->select(['id', 'selling_order_id', 'product_id', 'quantity'])->with(['product:id,name']);
                }])->whereBetween('created_at', [request('from_date') . ' 00:00:00', request('to_date') . ' 23:59:59'])
                ->when(!empty($request->cashier_id), function($q) use ($request){
                    $q->where('created_by', $request->cashier_id);
                })->where('status', 'Completed')->orderBy('id', 'DESC')->get();
                return view('layouts.reports.purchase-sale-report', compact('invoices', 'data', 'cashiers'));
            }

        } else {
            $data['final_wallet_balance'] = Wallet::whereBetween('date',
                [request('from_date'), request('to_date')])->sum('balance');
            $data['initial_balance'] = Wallet::whereBetween('date',
                [request('from_date'), request('to_date')])->sum('initial_balance');

            if (request('web_page') == 'RECEIPT') {
                $receipts = PurchaseOrder::with(['cashier','invoice' => function ($q) {
                    $q->select(['id', 'purchase_order_id', 'invoice_number']);
                }, 'supplier' => function ($q) {
                    $q->select(['id', 'name']);
                }, 'orderItems' => function ($q) {
                    $q->select(['id', 'purchase_order_id', 'product_id', 'quantity'])->with(['product:id,name']);
                }])->whereBetween('created_at', [request('from_date') . ' 00:00:00', request('to_date') . ' 23:59:59'])
               ->when(!empty($request->cashier_id), function($q) use ($request){
                    $q->where('created_by', $request->cashier_id);
                })
                    ->where('status', 'Completed')->orderBy('id', 'DESC')->get();
                return view('layouts.reports.purchase-sale-report', compact('receipts', 'data', 'cashiers'));
            } elseif (request('web_page') == 'INVOICE') {
                $invoices = SellingOrder::with(['cashier' => function ($q) {
                    $q->select(['id', 'name']);
                }, 'customer' => function ($q) {
                    $q->select(['id', 'name']);
                }, 'invoice' => function ($q) {
                    $q->select(['id', 'invoice_number', 'sales_order_id']);
                }, 'items' => function ($q) {
                    $q->select(['id', 'selling_order_id', 'product_id', 'quantity'])->with(['product:id,name']);
                }])->whereBetween('created_at', [request('from_date') . ' 00:00:00', request('to_date') . ' 23:59:59'])
                ->when(!empty($request->cashier_id), function($q) use ($request){
                    $q->where('created_by', $request->cashier_id);
                })
                    ->where('status', 'Completed')->orderBy('id', 'DESC')->get();
                return view('layouts.reports.purchase-sale-report', compact('invoices', 'data', 'cashiers'));
            }
        }
    }

    public function receiptDownload(Request $request)
    {
        $data['final_wallet_balance'] = Wallet::whereBetween('date',
            [request('from_date'), request('to_date')])->sum('balance');
        $data['initial_balance'] = Wallet::whereBetween('date',
            [request('from_date'), request('to_date')])->sum('initial_balance');

        $receipts = PurchaseOrder::with(['invoice' => function ($q) {
            $q->select(['id', 'purchase_order_id', 'invoice_number']);
        }, 'supplier' => function ($q) {
            $q->select(['id', 'name']);
        }, 'orderItems' => function ($q) {
            $q->select(['id', 'purchase_order_id', 'product_id', 'quantity'])->with(['product:id,name']);
        }])->whereBetween('created_at', [request('from_date') . ' 00:00:00', request('to_date') . ' 23:59:59'])
            ->where('status', 'Completed')->orderBy('id', 'DESC')->get();
        $pdf = \PDF::loadView('reports.receipt-report-pdf-template', compact('receipts', 'data'));
        $filename = 'Receipt-' . $receipts[0]?->invoice->invoice_number . '.pdf';
        if (request()->query('action') === 'view') {
            return $pdf->stream($filename);
        }
        return $pdf->download($filename);
    }

    public function invoiceDownload(Request $request)
    {
        $data['final_wallet_balance'] = Wallet::whereBetween('date',
            [request('from_date'), request('to_date')])->sum('balance');
        $data['initial_balance'] = Wallet::whereBetween('date',
            [request('from_date'), request('to_date')])->sum('initial_balance');

        $invoices = SellingOrder::with(['cashier' => function ($q) {
            $q->select(['id', 'name']);
        }, 'customer' => function ($q) {
            $q->select(['id', 'name']);
        }, 'invoice' => function ($q) {
            $q->select(['id', 'invoice_number', 'sales_order_id']);
        }, 'items' => function ($q) {
            $q->select(['id', 'selling_order_id', 'product_id', 'quantity'])->with(['product:id,name']);
        }])->whereBetween('created_at', [request('from_date') . ' 00:00:00', request('to_date') . ' 23:59:59'])
            ->where('status', 'Completed')->orderBy('id', 'DESC')->get();
        $pdf = \PDF::loadView('reports.invoice-report-pdf-template', compact('invoices', 'data'));
        $filename = 'Invoice-' . $invoices[0]?->invoice->invoice_number . '.pdf';
        if (request()->query('action') === 'view') {
            return $pdf->stream($filename);
        }
        return $pdf->download($filename);
    }
}
