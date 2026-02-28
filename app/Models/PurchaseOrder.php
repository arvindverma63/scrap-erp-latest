<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'supplier_id',
        'created_by',
        'less_scale_fee',
        'total_amount',
        'paid_amount',
        'payment_method',
        'status',
        'haulage_fee',
        'handling_fee'
    ];

    // 🔹 Relationships

    // Supplier who provided this order
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Cashier / user who created this order
    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Invoices for this purchase order (for partial payments)
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }


    // 🔹 Helper: latest invoice
    public function latestInvoice()
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }

    // 🔹 Helper: total paid from invoices
    public function totalPaid()
    {
        return $this->invoices()->sum('grand_total');
    }

    // 🔹 Helper: remaining balance
    public function balance()
    {
        return $this->total_amount - $this->totalPaid();
    }

    // PurchaseOrder.php
    // All products/items attached to this purchase order
    public function orderItems()
    {
        return $this->hasMany(OrderToProduct::class, 'purchase_order_id');
    }
}