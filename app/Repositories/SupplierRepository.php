<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierRepository
{
    protected $model;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    /**
     * Get all suppliers with optional filtering.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(Request $request)
    {
        $query = $this->model->with('product');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('material')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->material . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(30);
    }

    /**
     * Find a supplier by ID.
     *
     * @param int $id
     * @return \App\Models\Supplier
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        return $this->model::with(['products'])->findOrFail($id);
    }

    /**
     * Create a new supplier.
     *
     * @param array $data
     * @return \App\Models\Supplier
     * @throws \Exception
     */
    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            Log::error('Error creating supplier: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing supplier.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Supplier
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function update($id, array $data)
    {
        try {
            $supplier = $this->model->findOrFail($id);
            $supplier->update($data);
            return $supplier;
        } catch (\Exception $e) {
            Log::error('Error updating supplier: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a supplier.
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function delete($id)
    {
        try {
            $supplier = $this->model->findOrFail($id);
            return $supplier->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting supplier: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Find a supplier with product category for JSON response.
     *
     * @param int $id
     * @return \App\Models\Supplier
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithProductCategory($id)
    {
        return $this->model->findOrFail($id);
    }
}
