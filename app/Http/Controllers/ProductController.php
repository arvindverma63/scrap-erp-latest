<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;
use App\Models\WeightUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDO;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display all products.
     */
    public function index(Request $request)
    {
        $products = $this->productRepository->all($request);
        $weightUnits = WeightUnit::all();
        return view('layouts.products.index', compact('products', 'weightUnits'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $weightUnits = WeightUnit::all();
        return view('layouts.products.create', compact('weightUnits'));
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')],
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'low_stock_limit' => 'nullable|numeric|min:0',
            'high_stock_limit' => 'nullable|numeric|min:0',
            'weight_unit_id' => 'nullable|exists:weight_units,id',
            'company_sale_price' => 'nullable|numeric|min:0',
            'loyal_sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            // Log input data before saving
            Log::info('Attempting to create new product', [
                'user_id' => auth()->id(),
                'data' => $validated
            ]);

            // Create product
            $product = $this->productRepository->create($validated);

            // Log successful creation
            Log::info('Product created successfully', [
                'product_id' => $product->id ?? null,
                'product_name' => $product->name ?? null,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
        } catch (\Exception $e) {
            // Log the error details
            Log::error('Failed to create product', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return back()->withErrors(['error' => 'Failed to create product.'])->withInput();
        }
    }


    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $product = $this->productRepository->find($id);
        $weightUnits = WeightUnit::all();
        return view('layouts.products.edit', compact('product', 'weightUnits'));
    }

    /**
     * Update product.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'low_stock_limit' => 'nullable|numeric|min:0',
            'high_stock_limit' => 'nullable|numeric|min:0',
            'weight_unit_id' => 'nullable|exists:weight_units,id',
            'company_sale_price' => 'nullable|numeric|min:0',
            'loyal_sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $this->productRepository->update($id, $validated);
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update product.'])->withInput();
        }
    }

    /**
     * Delete product.
     */
    public function destroy($id)
    {
        try {
            $this->productRepository->delete($id);
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete product.']);
        }
    }

    /**
     * Get product details for JSON response.
     */
    public function getProduct($id)
    {
        $response = $this->productRepository->getProduct($id);
        return response()->json(
            array_filter($response, fn($key) => $key !== 'status', ARRAY_FILTER_USE_KEY),
            $response['status'] ?? 200
        );
    }

    public function import()
    {
        return view('layouts.products.import');
    }

    public function importCsv(Request $request)
    {
        $import = new ProductImport();
        $import->import($request->file('file'));
        if ($import->failures()->isNotEmpty()) {
            $failureMessages = $import->failures()->map(function ($failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                return "Row $row - $attribute: $errors";
            })->toArray();
            $failureString = implode(', ', $failureMessages);
            return back()->with('error', "Import failed due to validation errors: $failureString");
        }
        return back()->with('success', 'Products imported successfully!');
    }
}
