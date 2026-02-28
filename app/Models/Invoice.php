<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'purchase_order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'sub_total',
        'tax_amount',
        'discount',
        'grand_total',
        'partially_paid',
        'notes',
        'balance_amount',
        'digital_signature',
        'status',
        'created_by',
    ];

    // 🔹 Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function purchasePayments()
    {
        return $this->hasMany(PurchasePayment::class, 'invoice_id');
    }
}
