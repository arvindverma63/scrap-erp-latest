<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderToProduct extends Model
{
    use HasFactory;

    protected $table = 'order_to_products';

    protected $fillable = [
        'purchase_order_id',  // ← link to purchase order
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

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function weightUnit()
    {
        return $this->belongsTo(WeightUnit::class, 'weight_unit_id');
    }
}
