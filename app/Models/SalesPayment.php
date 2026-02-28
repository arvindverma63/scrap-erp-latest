<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    use HasFactory;

    protected $table = 'sales_payments';

    protected $fillable = [
        'sales_invoice_id',
        'payment_date',
        'payment_method',
        'reference_no',
        'amount',
        'notes',
        'created_by',
    ];

    /**
     * Relationship: A payment belongs to a sales invoice.
     */
    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    /**
     * Relationship: The user who recorded the payment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
