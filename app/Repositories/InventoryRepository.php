<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InventoryRepository
{
    protected $model;

    public function __construct(Inventory $model)
    {
        $this->model = $model;
    }

    /**
     * Get all inventory items with filters and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(Request $request)
    {
        $query = $this->model->with(['product', 'weightUnit']);

        if ($request->filled('name')) {
            $query->wherehas('product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%');
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('inventory_type')) {
            $query->where('inventory_type', $request->inventory_type);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        return $query->latest()->paginate(30)->appends($request->query());
    }

    /**
     * Find an inventory item by ID.
     *
     * @param int $id
     * @return \App\Models\Inventory
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new inventory item.
     *
     * @param array $data
     * @return \App\Models\Inventory
     * @throws \Exception
     */
    public function create(array $data)
    {
        try {
            return $this->model->create([
                'product_id' => $data['product_id'],
                'weight_unit_id' => $data['weight_unit_id'],
                'quantity' => $data['quantity'],
                'average_cost' => $data['average_cost'],
                'inventory_type' => $data['inventory_type'],
                'created_by' => Auth::id(),
                'user_id' => $data['user_id'] ?? null,
                'status' => 'Active',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating inventory: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing inventory item.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Inventory
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function update($id, array $data)
    {
        try {
            $inventory = $this->model->findOrFail($id);
            $inventory->update([
                'product_id' => $data['product_id'],
                'weight_unit_id' => $data['weight_unit_id'],
                'quantity' => $data['quantity'],
                'average_cost' => $data['average_cost'],
                'inventory_type' => $data['inventory_type'],
                'user_id' => $data['user_id'] ?? null,
            ]);
            return $inventory;
        } catch (\Exception $e) {
            Log::error('Error updating inventory: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an inventory item.
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function delete($id)
    {
        try {
            $inventory = $this->model->findOrFail($id);
            return $inventory->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting inventory: ' . $e->getMessage());
            throw $e;
        }
    }
}
