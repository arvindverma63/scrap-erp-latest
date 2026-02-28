<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderRepository
{
    protected $model;

    public function __construct(PurchaseOrder $model)
    {
        $this->model = $model;
    }

    /**
     * Get purchase orders with filters and pagination, optionally filtered by status.
     *
     * @param Request $request
     * @param string|null $status
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOrders(Request $request, $status = null)
    {
        $user = auth()->user();
        $query = $this->model->with(['supplier', 'cashier', 'latestInvoice']);
        // Filter by status/type if provided
        if ($status) {
            $query->where('status', $status); // or 'status' depending on your column name
        }

        if(!in_array($user->roles->first()->id, [1,5])){
            $query->where('created_by', $user->id);
        }
        // Return all results as a collection
        return $query->latest()->paginate(30);
    }


    /**
     * Find a purchase order by ID with full details for modal.
     *
     */
    public function findWithDetails($id)
    {
        return $this->model->with([
            'supplier',
            'cashier',
            'latestInvoice',
            'items.product',
            'items.weightUnit'
        ])->findOrFail($id);
    }
}
