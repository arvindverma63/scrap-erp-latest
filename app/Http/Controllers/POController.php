<?php

namespace App\Http\Controllers;

use App\Repositories\PurchaseOrderRepository;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\WeightUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class POController extends Controller
{
    protected $purchaseOrderRepository;

    public function __construct(PurchaseOrderRepository $purchaseOrderRepository)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }

    /**
     * Display all purchase orders with filters.
     */
    public function index(Request $request)
    {
        $purchaseOrders = $this->purchaseOrderRepository->getOrders($request);
        $suppliers = Supplier::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.PO.index', compact('purchaseOrders', 'suppliers', 'products', 'weightUnits'))
            ->with('reportType', 'All');
    }

    /**
     * Display pending purchase orders with filters.
     */
    public function pendingReport(Request $request)
    {
        $purchaseOrders = $this->purchaseOrderRepository->getOrders($request, 'Pending');
        $suppliers = Supplier::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.purchase-reports.pending', compact('purchaseOrders', 'suppliers', 'products', 'weightUnits'))
            ->with('reportType', 'Pending');
    }

    /**
     * Display completed purchase orders with filters.
     */
    public function completedReport(Request $request)
    {
        $purchaseOrders = $this->purchaseOrderRepository->getOrders($request, 'Completed');
        $suppliers = Supplier::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.purchase-reports.completed', compact('purchaseOrders', 'suppliers', 'products', 'weightUnits'))
            ->with('reportType', 'Completed');
    }

    /**
     * Display voided purchase orders with filters.
     */
    public function voidedReport(Request $request)
    {
        $purchaseOrders = $this->purchaseOrderRepository->getOrders($request, 'Voided');
        $suppliers = Supplier::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.purchase-reports.voided', compact('purchaseOrders', 'suppliers', 'products', 'weightUnits'))
            ->with('reportType', 'Voided');
    }

    /**
     * Show purchase order details for modal.
     */
    public function show($id)
    {
        try {
            $purchaseOrder = $this->purchaseOrderRepository->findWithDetails($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $purchaseOrder->transaction_id,
                    'supplier_name' => $purchaseOrder->supplier->name ?? 'N/A',
                    'cashier_name' => $purchaseOrder->cashier->name ?? 'N/A',
                    'status' => $purchaseOrder->status,
                    'created_at' => $purchaseOrder->created_at->format('Y-m-d H:i:s'),
                    'total_amount' => $purchaseOrder->total_amount,
                    'paid_amount' => $purchaseOrder->paid_amount,
                    'balance' => $purchaseOrder->balance(),
                    'invoice_number' => $purchaseOrder->latestInvoice->invoice_number ?? 'N/A',
                    'invoice_date' => $purchaseOrder->latestInvoice->invoice_date->format('Y-m-d') ?? 'N/A',
                    'items' => $purchaseOrder->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->name ?? 'N/A',
                            'weight_unit_name' => $item->weightUnit->name ?? 'N/A',
                            'quantity' => $item->quantity,
                            'total_amount' => $item->total_amount,
                        ];
                    })->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching purchase order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order details.',
            ], 500);
        }
    }
}
