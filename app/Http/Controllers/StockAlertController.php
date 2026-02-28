<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    public function index(Request $request)
    {
        // Base query with eager loading
        // $query = Inventory::with(['product', 'weightUnit']);

        // // Apply filters if provided (e.g., search or product select)
        // if ($request->filled('search')) {
        //     $query->whereHas('product', function ($q) use ($request) {
        //         $q->where('name', 'like', '%' . $request->search . '%');
        //     });
        // }

        // if ($request->filled('product')) {
        //     $query->where('product_id', $request->product);
        // }

        // // Group by product_id and inventory_type, sum quantity
        // $inventories = $query->groupBy('product_id', 'inventory_type')
        //     ->selectRaw('product_id, inventory_type, SUM(quantity) as total_quantity')
        //     ->get();

        // // Aggregate by product_id to calculate net stock
        // $productWise = $inventories->groupBy('product_id')->map(function ($rows) {
        //     $first = $rows->first() ?? null;
        //     $totalPurchased = $rows->where('inventory_type', 'Purchase')->sum('total_quantity') ?? 0;
        //     $totalSold = $rows->where('inventory_type', 'Sale')->sum('total_quantity') ?? 0;
        //     $netStock = $totalPurchased - $totalSold;

        //     return [
        //         'product_id'      => $first ? $first->product_id : null,
        //         'product_name'    => $first ? (optional($first->product)->name ?? 'Unknown Product') : 'Unknown Product',
        //         'weight_unit'     => $first ? (optional($first->product->weightUnit)->name ?? 'Unknown Unit') : 'Unknown Unit',
        //         'total_quantity'  => $netStock,  // Net stock (purchased - sold) as "Current Stock"
        //     ];
        // })->values();  // Reset keys for clean array    

        $productWise = Product::with(['weightUnit'])->paginate(30);

        return view('layouts.low-stock.index', compact('productWise'));
    }
}
