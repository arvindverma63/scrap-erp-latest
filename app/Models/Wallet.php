<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

    protected $fillable = [
        'requested_by',
        'cashier_id',
        'initial_balance',
        'balance',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Relationship: Wallet belongs to a user (requested_by)
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get formatted balance
     */
    public function formattedBalance(): string
    {
        return number_format($this->balance, 2);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
