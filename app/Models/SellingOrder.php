<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingOrder extends Model
{
    use HasFactory;

    protected $table = 'sales_orders';

    protected $fillable = [
        'customer_id',     // ✅ changed from supplier_id to customer_id
        'created_by',
        'less_scale_fee',
        'total_amount',
        'paid_amount',
        'payment_method',
        'status',
    ];

    // 🔹 Relationships

    // customer for this order
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Cashier / user who created this order
    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Invoices for this selling order
    public function invoices()
    {
        return $this->hasMany(SalesInvoice::class, 'sales_order_id'); // ✅ changed
    }

    public function invoice()
    {
        return $this->hasOne(SalesInvoice::class, 'sales_order_id'); // ✅ changed
    }

    // 🔹 Helper: latest invoice
    public function latestInvoice()
    {
        return $this->hasOne(SalesInvoice::class, 'sales_order_id')->latestOfMany();
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

    // 🔹 All products/items attached to this selling order
    public function items()
    {
        return $this->hasMany(SellingToProduct::class, 'selling_order_id'); // ✅ changed
    }
}
