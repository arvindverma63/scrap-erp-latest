<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Imports\SupplierImport;
use App\Models\Country;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuyerController extends Controller
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $customers = $this->customerRepository->all($request);
        $countries = Country::orderBy('id', 'ASC')->get();
        return view('layouts.buyers.index', compact('customers', 'countries'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $countries = Country::OrderBy('id', 'ASC')->get();
        return view('layouts.buyers.create', compact('countries'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers', 'phone')],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'country_code' => 'required',
            'company_name' => 'nullable|string|max:255',
            'company_email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'company_email')],
            'company_phone_number' => ['nullable', 'string', 'max:20', Rule::unique('customers', 'company_phone_number')],
            'tax' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'is_loyal' => 'nullable|in:Yes,No',
        ]);

        try {
            $this->customerRepository->create($validated);
            return redirect()->route('admin.buyers.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = $this->customerRepository->find($id);
        return view('layouts.buyers.view', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        $customer = $this->customerRepository->find($id);
        $countries = Country::OrderBy('id', 'ASC')->get();
        return view('layouts.buyers.edit', compact('customer', 'countries'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers', 'phone')->ignore($id)],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'company_email')->ignore($id)],
            'company_phone_number' => ['nullable', 'string', 'max:20', Rule::unique('customers', 'company_phone_number')->ignore($id)],
            'country_code' => 'required',
            'tax' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'is_loyal' => 'nullable|in:Yes,No',
        ]);

        try {
            $this->customerRepository->update($id, $validated);
            return redirect()->route('admin.buyers.index')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update customer.'])->withInput();
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        try {
            $this->customerRepository->delete($id);
            return redirect()->route('admin.buyers.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete customer.']);
        }
    }

    public function getCustomer($id)
    {
        $customer = $this->customerRepository->find($id);
        return response()->json($customer);
    }

    public function updateStatus(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = $request->status;
        $customer->save();

        return response()->json(['success' => true]);
    }


    public function onInvoiceCreate(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone_number' => 'nullable|string|max:20',
            'tax' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'is_loyal' => 'nullable|in:Yes,No',
        ]);

        try {
            $customer = $this->customerRepository->create($validated);
            return response()->json([
                'success' => true,
                'message' => 'customer added successfully.',
                'data' => $customer,
            ]);;
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create customer.'])->withInput();
        }
    }

    public function importCsv(Request $request)
    {
        $import = new CustomerImport();
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
        return back()->with('success', 'Customer imported successfully!');
    }
}
