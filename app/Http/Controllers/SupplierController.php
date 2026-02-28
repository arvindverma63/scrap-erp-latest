<?php

namespace App\Http\Controllers;

use App\Imports\SupplierImport;
use App\Models\Country;
use App\Models\SupplierProduct;
use App\Repositories\SupplierRepository;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\DB;

// ✅ Add this line
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Display all suppliers.
     */
    public function index(Request $request)
    {
        $suppliers = $this->supplierRepository->all($request);
        $products = Product::all();
        $countries = Country::orderBy('name', 'ASC')->get();
        return view('layouts.suppliers.index', compact('suppliers', 'products', 'countries'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $products = Product::all();
        $countries = Country::orderBy('name', 'ASC')->get();
        return view('layouts.suppliers.create', compact('products', 'countries'));
    }

    /**
     * Store a new supplier.
     */
    public function store(Request $request)
    {
        $rules = [
            'supplier_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'phone')],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'country_code' => 'required',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'company_email')],
            'company_phone_number' => ['nullable', 'string', 'max:20', Rule::unique('suppliers', 'company_phone_number')],
            'product_id' => 'required|array',
            'product_id.*' => 'required',
            'tax' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive',
        ];
        $validated = $request->validate($rules, [
            'product_id.required' => 'Product category is required.',
            'product_id.array'    => 'Product category format is invalid.',
            'product_id.*.required' => 'Please select at least one product category.',
        ]);
        $validated['status'] = $validated['status'] ?? 'Active';

        DB::beginTransaction();
        try {
            $supplier = Supplier::create($validated);
            if ($supplier) {
                foreach ($request->product_id as $productId) {
                    SupplierProduct::create([
                        'supplier_id' => $supplier->id,
                        'product_id' => $productId
                    ]);
                }
                DB::commit();
                return redirect()->route('admin.supplier.index')->with('success', 'Supplier added successfully.');
            } else {
                return back()->with('info', 'Supplier creation failed');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('info', $e->getMessage());
        }
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $supplier = $this->supplierRepository->find($id);
        $products = Product::all();
        $countries = Country::orderBy('id', 'ASC')->get();
        return view('layouts.suppliers.edit', compact('supplier', 'products', 'countries'));
    }

    /**
     * Update supplier.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'phone')->ignore($id)],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'country_code' => 'required',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'company_email')->ignore($id)],
            'company_phone_number' => ['nullable', 'string', 'max:20', Rule::unique('suppliers', 'company_phone_number')->ignore($id)],
            'product_id' => 'required|array',
            'product_id.*' => 'required',
            'tax' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive',
        ], [
            'product_id.required' => 'Product category is required.',
            'product_id.array'    => 'Product category format is invalid.',
            'product_id.*.required' => 'Please select at least one product category.',
        ]);

        DB::beginTransaction();
        try {
            $update = $this->supplierRepository->update($id, $validated);
            if ($update) {
                SupplierProduct::where('supplier_id', $id)->delete();
                foreach ($request->product_id as $productId) {
                    SupplierProduct::create([
                        'supplier_id' => $id,
                        'product_id' => $productId
                    ]);
                }
                DB::commit();
                return redirect()->route('admin.supplier.index')->with('success', 'Supplier updated successfully.');
            } else {
                return back()->with(['info' => 'Failed to update supplier.'])->withInput();
            }
        } catch (\Exception $e) {
            return $e->getMessage() . '_' . $e->getLine();
            return back()->with(['info' => $e->getMessage()]);
        }
    }

    /**
     * Delete supplier.
     */
    public function destroy($id)
    {
        try {
            $this->supplierRepository->delete($id);
            return redirect()->route('admin.supplier.index')->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete supplier.']);
        }
    }

    /**
     * Show supplier as JSON for modal view.
     */
    public function show($id)
    {
        $supplier = $this->supplierRepository->findWithProductCategory($id);

        if (!$supplier || $supplier->status !== 'active') {
            return response()->json([
                'message' => 'Supplier not found or inactive.'
            ], 404);
        }

        return response()->json($supplier);
    }

    public function updateStatus(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->status = $request->status;
        $supplier->save();

        return response()->json(['success' => true, 'status' => $supplier->status]);
    }


    /**
     * Store a new supplier.
     */
    public function storeOnCreateReceipt(Request $request)
    {
        $rules = [
            'supplier_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone_number' => 'nullable|string|max:20',
            'product_id' => 'nullable|exists:products,id',
            'tax' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive',
        ];

        // Validate manually so we can catch errors for AJAX
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['status'] = $validated['status'] ?? 'Active';

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier added successfully.',
            'data' => $supplier,
        ]);
    }

    public function importSuppliers(Request $request)
    {
        $import = new SupplierImport();
        $import->import($request->file('file'));
        if ($import->failures()->isNotEmpty()) {
            $failureMessages = $import->failures()->map(function ($failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                return "Row $row - $attribute: $errors";
            })->toArray();
            $failureString = implode(', ', $failureMessages);
            return back()->with('info', "Import failed due to validation errors: $failureString");
        }
        return back()->with('success', 'Suppliers imported successfully!');
    }
}
