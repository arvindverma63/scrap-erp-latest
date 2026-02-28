<?php

namespace App\Http\Controllers;

use App\Repositories\SellingOrderRepository;
use App\Models\Customer;
use App\Models\Product;
use App\Models\WeightUnit;
use Illuminate\Http\Request;

class SalesInvoiceController extends Controller
{
    protected $sellingOrderRepository;

    public function __construct(SellingOrderRepository $sellingOrderRepository)
    {
        $this->sellingOrderRepository = $sellingOrderRepository;
    }

    /**
     * Display all sales orders with filters.
     */
    public function index(Request $request)
    {
        $salesOrders = $this->sellingOrderRepository->getOrders($request);
        $customers = Customer::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.SalesInvoice.index', compact('salesOrders', 'customers', 'products', 'weightUnits'))
            ->with('reportType', 'All');
    }

    /**
     * Display pending sales orders with filters.
     */
    public function pendingReport(Request $request)
    {
        $salesOrders = $this->sellingOrderRepository->getOrders($request, 'Pending');
        $customers = Customer::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.sales-reports.pending', compact('salesOrders', 'customers', 'products', 'weightUnits'))
            ->with('reportType', 'Pending');
    }

    /**
     * Display completed sales orders with filters.
     */
    public function completedReport(Request $request)
    {
        $salesOrders = $this->sellingOrderRepository->getOrders($request, 'Completed');
        $customers = Customer::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.sales-reports.completed', compact('salesOrders', 'customers', 'products', 'weightUnits'))
            ->with('reportType', 'Completed');
    }

    /**
     * Display voided sales orders with filters.
     */
    public function voidedReport(Request $request)
    {
        $salesOrders = $this->sellingOrderRepository->getOrders($request, 'Voided');
        $customers = Customer::all();
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.sales-reports.voided', compact('salesOrders', 'customers', 'products', 'weightUnits'))
            ->with('reportType', 'Voided');
    }

    /**
     * Show sales order details for modal.
     */
    public function show($id)
    {
        try {
            $salesOrder = $this->sellingOrderRepository->findWithDetails($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $salesOrder->transaction_id,
                    'customer_name' => $salesOrder->customer->name ?? 'N/A',
                    'cashier_name' => $salesOrder->cashier->name ?? 'N/A',
                    'status' => $salesOrder->status,
                    'created_at' => $salesOrder->created_at->format('Y-m-d H:i:s'),
                    'total_amount' => $salesOrder->total_amount,
                    'paid_amount' => $salesOrder->paid_amount,
                    'balance' => $salesOrder->balance(),
                    'invoice_number' => $salesOrder->latestInvoice->invoice_number ?? 'N/A',
                    'invoice_date' => $salesOrder->latestInvoice->invoice_date->format('Y-m-d') ?? 'N/A',
                    'items' => $salesOrder->items->map(function ($item) {
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
            Log::error('Error fetching sales order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order details.',
            ], 500);
        }
    }

    /**
     * Update sales order status and create inventory entries.
     */
    public function updateStatusPurchase($id)
    {
        try {
            $this->sellingOrderRepository->updateStatusAndCreateInventory($id);
            return redirect()->back()->with('success', 'Status Updated & Inventory Created Successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update status and create inventory.']);
        }
    }
}
