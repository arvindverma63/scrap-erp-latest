<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class CustomerRepository
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    /**
     * Get all customers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($request)
    {
        return $this->model->when(!empty($request->search), function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        })->orderBy('id', 'DESC')->paginate(30);
    }

    /**
     * Find a customer by ID.
     *
     * @param int $id
     * @return \App\Models\Customer
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new customer.
     *
     * @param array $data
     * @return \App\Models\Customer
     * @throws \Exception
     */
    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing customer.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Customer
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function update($id, array $data)
    {
        try {
            $customer = $this->model->findOrFail($id);
            $customer->update($data);
            return $customer;
        } catch (\Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a customer.
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function delete($id)
    {
        try {
            $customer = $this->model->findOrFail($id);
            return $customer->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());
            throw $e;
        }
    }
}
