<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Get all products with weight unit relationship.
     */
    public function all($request)
    {
        Log::info('Fetching all products with weight unit', [
            'user_id' => Auth::id(),
        ]);

        return $this->model->with('weightUnit')->paginate(30);
    }

    /**
     * Find a product by ID.
     */
    public function find($id)
    {
        Log::info('Fetching product by ID', [
            'user_id' => Auth::id(),
            'product_id' => $id,
        ]);

        return $this->model->findOrFail($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data)
    {
        Log::info('Attempting to create product', [
            'user_id' => Auth::id(),
            'data' => $data,
        ]);

        try {
            $product = $this->model->create([
                'name' => $data['name'],
                'sale_price' => $data['sale_price'],
                'purchase_price' => $data['purchase_price'],
                'weight_unit_id' => $data['weight_unit_id'] ?? null,
                'company_sale_price' => $data['company_sale_price'] ?? null,
                'loyal_sale_price' => $data['loyal_sale_price'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by' => Auth::id(),
            ]);

            Log::info('Product created successfully', [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Error creating product', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing product.
     */
    public function update($id, array $data)
    {
        Log::info('Attempting to update product', [
            'user_id' => Auth::id(),
            'product_id' => $id,
            'data' => $data,
        ]);

        try {
            $product = $this->model->findOrFail($id);
            $product->update([
                'name' => $data['name'],
                'sale_price' => $data['sale_price'],
                'purchase_price' => $data['purchase_price'],
                'weight_unit_id' => $data['weight_unit_id'] ?? null,
                'company_sale_price' => $data['company_sale_price'] ?? null,
                'loyal_sale_price' => $data['loyal_sale_price'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            Log::info('Product updated successfully', [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'user_id' => Auth::id(),
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a product.
     */
    public function delete($id)
    {
        Log::info('Attempting to delete product', [
            'user_id' => Auth::id(),
            'product_id' => $id,
        ]);

        try {
            $product = $this->model->findOrFail($id);
            $product->delete();

            Log::info('Product deleted successfully', [
                'user_id' => Auth::id(),
                'product_id' => $id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'user_id' => Auth::id(),
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get product with weight unit (for JSON/API use).
     */
    public function getProduct($id)
    {
        Log::info('Fetching product details for API', [
            'user_id' => Auth::id(),
            'product_id' => $id,
        ]);

        try {
            $product = $this->model->with('weightUnit')->find($id);

            if (!$product) {
                Log::warning('Product not found', ['product_id' => $id]);
                return [
                    'success' => false,
                    'message' => 'Product not found.',
                    'status' => 404,
                ];
            }

            if (!$product->weightUnit) {
                Log::warning('Weight unit not found for product', ['product_id' => $id]);
                return [
                    'success' => false,
                    'message' => 'Weight unit not found for this product.',
                    'status' => 404,
                ];
            }

            Log::info('Product data fetched successfully', [
                'product_id' => $id,
            ]);

            return [
                'success' => true,
                'sale_price' => $product->sale_price,
                'company_sale_price' => $product->company_sale_price,
                'loyal_sale_price' => $product->loyal_sale_price,
                'purchase_price' => $product->purchase_price,
                'weight_unit_id' => $product->weightUnit->id,
                'weight_unit_name' => $product->weightUnit->name,
                'status' => 200,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching product', [
                'user_id' => Auth::id(),
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'status' => 500,
            ];
        }
    }
}
