<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $table = 'purchase_payments';

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'payment_method',
        'reference_no',
        'amount',
        'notes',
        'created_by',
    ];

    /**
     * Relation: Payment belongs to an Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Relation: Payment recorded by a User (if you have users table)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
