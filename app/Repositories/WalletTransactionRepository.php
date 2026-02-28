<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletTransactionRepository
{
    public function all()
    {
        $user = auth()->user();
        if (!in_array($user->roles->first()->id, [1,5])) {
            return WalletTransaction::with('requester')->where('requested_by', $user->id)->where('amount', '>', 0)->latest()->paginate(30);
        } else {
            return WalletTransaction::with('requester')->where('amount', '>', 0)->latest()->paginate(30);
        }
    }

    public function pending()
    {
        $user = auth()->user();
        if ($user->roles->first()->id == 2) {
            return WalletTransaction::where('cashier_id', $user->id)->where('status', 'pending')
                ->where('type', 'credit')
                ->whereNotNull('requested_by')
                ->with('requester')
                ->latest()
                ->get();
        } else {
            return WalletTransaction::where('status', 'pending')
                ->where('type', 'credit')
                ->whereNotNull('requested_by')
                ->with('requester')
                ->latest()
                ->get();
        }

    }

    public function countPending()
    {
        return WalletTransaction::where('status', 'pending')->count();
    }

    public function lastTopUp()
    {
        return WalletTransaction::where('type', 'topup')
            ->latest()
            ->with('requester')
            ->first();
    }

    public function updateStatus($id, $status)
    {
        $txn = WalletTransaction::findOrFail($id);
        $txn->status = $status;
        $txn->save();
        return $txn;
    }
}
