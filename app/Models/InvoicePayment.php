<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $table = 'invoice_payments';

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'payment_method',
        'amount_paid',
        'reference_no',
        'notes',
        'created_by',
    ];

    // 🔹 Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
