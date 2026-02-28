<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $table = 'sales_invoices'; // ✅ still ok

    protected $fillable = [
        'sales_order_id', // ✅ changed from sales_order_id
        'invoice_number',
        'invoice_date',
        'due_date',
        'sub_total',
        'tax_amount',
        'discount',
        'grand_total',
        'notes',
        'status',
        'partially_paid',
        'created_by',
        'digital_signature'
    ];

    // 🔹 Relationships
    public function sellingOrder()
    {
        return $this->belongsTo(SellingOrder::class, 'sales_order_id'); // ✅ changed
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function salesPayments()
    {
        return $this->hasMany(\App\Models\SalesPayment::class, 'sales_invoice_id');
    }
}
