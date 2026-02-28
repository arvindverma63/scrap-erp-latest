<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\Inventory;
use Carbon\Carbon;

class WalletRepository
{
    /**
     * Get the global wallet (create if not exists)
     */
    public function getOrCreate()
    {
//        return Wallet::firstOrCreate(['id' => 1], ['balance' => 0]);
        return Wallet::where('cashier_id', auth()->user()->id)
            ->where('date', date(Carbon::now()->toDateString()))->first();
    }

    /**
     * Get current wallet balance
     */
    public function getBalance()
    {
        return $this->getOrCreate()->balance;
    }

    /**
     * Update wallet balance manually
     */
    public function updateBalance($amount)
    {
        $wallet = $this->getOrCreate();
        $wallet->balance = $amount;
        $wallet->save();
        return $wallet;
    }

    /**
     * Recalculate wallet balance based on Inventory data
     */
    public function recalculateBalance()
    {
        $totalSale = Inventory::where('type', 'Sale')->sum('amount');
        $totalPurchase = Inventory::where('type', 'Purchase')->sum('amount');

        $wallet = $this->getOrCreate();
        $wallet->balance = $totalSale - $totalPurchase;
        $wallet->save();

        return $wallet;
    }

    /**
     * Adjust wallet balance (increment or decrement)
     */
    public function adjustBalance($amount)
    {
        $wallet = $this->getOrCreate();
        $wallet->balance += $amount;
        $wallet->save();
        return $wallet;
    }
}
