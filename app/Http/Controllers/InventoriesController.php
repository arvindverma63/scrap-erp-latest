<?php

namespace App\Http\Controllers;

use App\Repositories\InventoryRepository;
use App\Models\Product;
use App\Models\WeightUnit;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;

class InventoriesController extends Controller
{
    protected $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Display a listing of the inventory items with filters.
     */
    public function index(Request $request)
    {
        $inventories = $this->inventoryRepository->all($request);
        $products = Product::all();
        $weightUnits = WeightUnit::all();

        return view('layouts.inventories.index', compact('inventories', 'products', 'weightUnits'));
    }

    /**
     * Show form to create inventory.
     */
    public function create()
    {
        $products = Product::all();
        $weightUnits = WeightUnit::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();

        return view('layouts.inventories.create', compact('products', 'weightUnits', 'suppliers', 'customers'));
    }

    /**
     * Store new inventory.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'weight_unit_id' => 'required|exists:weight_units,id',
            'quantity' => 'required|numeric|min:1',
            'average_cost' => 'required|numeric|min:0',
            'inventory_type' => 'required|in:Purchase,Sale',
            'user_id' => 'nullable|integer',
        ]);

        try {
            $this->inventoryRepository->create($validated);
            return redirect()->route('inventories.index')->with('success', 'Inventory added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create inventory.'])->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $inventory = $this->inventoryRepository->find($id);
        $products = Product::all();
        $weightUnits = WeightUnit::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();

        return view('layouts.inventories.edit', compact('inventory', 'products', 'weightUnits', 'suppliers', 'customers'));
    }

    /**
     * Update inventory.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'weight_unit_id' => 'required|exists:weight_units,id',
            'quantity' => 'required|numeric|min:1',
            'average_cost' => 'required|numeric|min:0',
            'inventory_type' => 'required|in:Purchase,Sale',
            'user_id' => 'nullable|integer',
        ]);

        try {
            $this->inventoryRepository->update($id, $validated);
            return redirect()->route('inventories.index')->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update inventory.'])->withInput();
        }
    }

    /**
     * Delete inventory.
     */
    public function destroy($id)
    {
        try {
            $this->inventoryRepository->delete($id);
            return redirect()->route('inventories.index')->with('success', 'Inventory deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete inventory.']);
        }
    }
}
