<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCount extends Model
{
    use HasFactory;

    protected $table = 'cash_count';

    // Mass assignable fields
    protected $fillable = [
        'wallet_id',
        'cashier_id',
        'currency',
        'count',
        'added_by',
        'date',
        'remarks',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'id', 'wallet_id');
    }

    public function addedby()
    {
        return $this->hasOne(User::class, 'id', 'added_by');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
