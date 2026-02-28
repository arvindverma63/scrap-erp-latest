<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingToProduct extends Model
{
    use HasFactory;

    protected $table = 'sales_to_products';

    protected $fillable = [
        'selling_order_id',
        'product_id',
        'quantity',
        'weight_unit_id',
        'unit_price',
        'total_amount',
    ];

    // 🔹 Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sellingOrder()
    {
        return $this->belongsTo(SellingOrder::class, 'selling_order_id');
    }

    public function weightUnit()
    {
        return $this->belongsTo(WeightUnit::class, 'weight_unit_id');
    }
}
