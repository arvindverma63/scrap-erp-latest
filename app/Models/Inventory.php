<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'order_id',
        'product_id',
        'weight_unit_id',
        'quantity',
        'amount',
        'inventory_type',
        'created_by',
        'user_id',       // Supplier ID if Purchase, Customer ID if Sale
    ];

    // 🔹 Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function weightUnit()
    {
        return $this->belongsTo(WeightUnit::class);
    }

    public function creator() // The logged-in user who created it
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    // Example helpers
    public function isPurchase(): bool
    {
        return $this->inventory_type === 'Purchase';
    }

    public function isSale(): bool
    {
        return $this->inventory_type === 'Sale';
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'user_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
