<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'sale_price',
        'purchase_price',
        'total_quantity',
        'description',
        'weight_unit_id',
        'company_sale_price',
        'loyal_sale_price',
        'low_stock_limit',
        'high_stock_limit',
        'created_by', // user id who created the product
        
    ];



    /**
     * Relationship: Product created by a user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function weightUnit()
    {
        return $this->belongsTo(WeightUnit::class, 'weight_unit_id');
    }
}
