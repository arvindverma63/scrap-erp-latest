<?php

namespace App\Repositories;

use App\Models\SellingOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SellingOrderRepository
{
    protected $model;
    protected $inventoryRepository;

    public function __construct(SellingOrder $model, InventoryRepository $inventoryRepository)
    {
        $this->model = $model;
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Get sales orders with filters and pagination, optionally filtered by status.
     *
     * @param Request $request
     * @param string|null $status
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOrders(Request $request, $status = null)
    {
        $query = $this->model->with(['cashier', 'customer', 'latestInvoice']);

        // Filter by status/type if provided
        if ($status) {
            $query->where('status', $status); // or 'status' depending on your column name
        }

        // Optional: add other filters from request
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($request->date_from)),
                date('Y-m-d 23:59:59', strtotime($request->date_to)),
            ]);
        }

         $user = auth()->user();
        if(!in_array($user->roles->first()->id, [5])){
            $query->where('created_by', $user->id);
        }

    

        return $query->latest()->paginate(30);
    }


    /**
     * Find a sales order by ID with its items.
     *
     * @param int $id
     * @return \App\Models\SellingOrder
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithItems($id)
    {
        return $this->model->with('items')->findOrFail($id);
    }

    /**
     * Find a sales order by ID with full details for modal.
     *
     * @param int $id
     * @return \App\Models\SellingOrder
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithDetails($id)
    {
        return $this->model->with([
            'cashier',
            'customer',
            'latestInvoice',
            'items.product',
            'items.weightUnit'
        ])->findOrFail($id);
    }

    /**
     * Update sales order status and create inventory entries.
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function updateStatusAndCreateInventory($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $salesOrder = $this->findWithItems($id);
                $salesOrder->update(['status' => 'Completed']);

                foreach ($salesOrder->items as $item) {
                    $this->inventoryRepository->create([
                        'product_id' => $item->product_id,
                        'weight_unit_id' => $item->weight_unit_id,
                        'quantity' => $item->quantity,
                        'average_cost' => $item->total_amount,
                        'inventory_type' => 'Sales',
                        'created_by' => Auth::id(),
                        'user_id' => $salesOrder->customer_id,
                        'status' => 'Available',
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error updating sales order status and creating inventory: ' . $e->getMessage());
            throw $e;
        }
    }
}
