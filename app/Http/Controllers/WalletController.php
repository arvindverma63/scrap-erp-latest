<?php

namespace App\Http\Controllers;

use App\Models\CashCount;
use App\Models\Notification;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\WalletTransactionRepository;
use App\Repositories\WalletRepository;
use App\Repositories\NotificationRepository;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    protected $walletRepo;
    protected $txnRepo;
    protected $notificationRepo;

    public function __construct(
        WalletRepository            $walletRepo,
        WalletTransactionRepository $txnRepo,
        NotificationRepository      $notificationRepo
    )
    {
        $this->walletRepo = $walletRepo;
        $this->txnRepo = $txnRepo;
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Display wallet dashboard (pending + history)
     */
    public function walletsPage()
    {
        $pending = $this->txnRepo->pending();
        $history = $this->txnRepo->all();

        return view('layouts.wallet.index', compact('pending', 'history'));
    }

    /**
     * Approve a pending top-up
     */
    public function approveTopup($id)
    {
        DB::beginTransaction();
        try {
            $transaction = WalletTransaction::where('id', $id)->whereDate('created_at', today())->first();
            if (!$transaction) {
                return back()->with('error', 'Request date is crossed, action denied');
            }
            $txn = $this->txnRepo->updateStatus($id, 'approved');
            // Update wallet balance
            $walletExist = Wallet::where('date', date(Carbon::now()->toDateString()))
                ->where('cashier_id', $transaction->cashier_id)->first();
            if (!$walletExist) {
                $wallet = Wallet::create([
                    'requested_by' => null,
                    'cashier_id' => $transaction->cashier_id,
                    'initial_balance' => $txn->amount,
                    'balance' => $txn->amount,
                    'date' => date(Carbon::now()->toDateString()),
                ]);
            } else {
                if (!empty($walletExist) && $walletExist->id) {
                    $wallet = Wallet::where('id', $walletExist->id)->first();
                    if ($txn->type === 'credit') {
                        $wallet->balance += $txn->amount;
                    } else {
                        $wallet->balance -= $txn->amount;
                    }
                    $wallet->save();
                } else {
                    return back()->with('error', 'Wallet not found');
                }
            }

            // WalletTransaction::where('id', $id)->update([
            //     'wallet_id' => $transaction->requested_by
            // ]);
            $this->notificationRepo->create([
                'title' => 'Top-up Approved',
                'message' => 'Your top-up request of ' . number_format($txn->amount, 2) . ' JMD has been approved.',
                'type' => 'wallet',
                'user_id' => $txn->requested_by,
                'is_read' => false,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Top-up request approved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage() . '_' . $e->getLine());
        }
    }

    /**
     * Reject a pending top-up
     */
    public function rejectTopup($id)
    {
        $transaction = WalletTransaction::where('id', $id)->whereDate('created_at', today())->first();
        if (!$transaction) {
            return back()->with('error', 'Request date is crossed, action denied');
        }
        $txn = $this->txnRepo->updateStatus($id, 'rejected');

        // ✅ Add notification for user
        $this->notificationRepo->create([
            'title' => 'Top-up Rejected',
            'message' => 'Top-up request of ' . number_format($txn->amount, 2) . ' JMD has been rejected.',
            'type' => 'wallet',
            'user_id' => $txn->requested_by,
            'is_read' => false,
        ]);

        return redirect()->back()->with('error', 'Top-up request rejected.');
    }

    /**
     * Request a top-up
     */
    public function requestTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'cashier_id' => ['nullable', Rule::exists('users', 'id')]
        ]);

        $user = auth()->user();
        $cashierId = $request->cashier_id ?? $user->id;
        $todayWalletExist = Wallet::where('cashier_id', $cashierId)
            ->where('date', date(Carbon::now()->toDateString()))->first();
        if (!$todayWalletExist && (auth()->user()->roles->first()->name == 'super-admin' ||
                auth()->user()->roles->first()->name == 'admin')) {
            $request->balance = $request->amount;
            return $this->createDeposit($request);
        } else {
//            $wallet = $this->walletRepo->getOrCreate($user->id);
            // ✅ Check roles using Spatie
            $isAdmin = $user->hasAnyRole(['admin', 'super-admin']);
            // ✅ Create transaction (auto-approved if admin/super-admin)
            $txn = WalletTransaction::create([
                'wallet_id' => $todayWalletExist->id ?? null,
                'requested_by' => $cashierId,
                'cashier_id' => $cashierId,
                'amount' => $request->amount,
                'type' => 'credit',
                'status' => $isAdmin ? 'approved' : 'pending',
                'description' => 'Top-up request by ' . $user->name,
                'remarks' => !$isAdmin ? ($request->remarks ?? 'null') : null
            ]);


            // ✅ Instantly credit wallet if admin/super-admin
            if ($isAdmin) {
                $todayWalletExist->initial_balance += $txn->amount;
                $todayWalletExist->balance += $txn->amount;
                $todayWalletExist->save();
            }

            $users = User::whereIn('id', [3, $cashierId])->pluck('id');
            // ✅ Notification logic
            foreach ($users as $user1) {
                $this->notificationRepo->create([
                    'user_id' => $user1,
                    'title' => $isAdmin ? 'Top-up Approved' : 'New Top-up Request',
                    'message' => $isAdmin
                        ? 'Your top-up of ' . number_format($request->amount, 2) . ' JMD has been added by Admin.'
                        : 'User ' . $user->name . ' requested a top-up of ' . number_format($request->amount, 2) . ' JMD.',
                    'type' => 'wallet',
                    'is_read' => false,
                ]);
            }
        }
        return redirect()->back()->with(
            'success',
            $isAdmin
                ? 'Top-up added and approved automatically.'
                : 'Top-up request submitted successfully and awaiting approval.'
        );
    }

    public function deposit(Request $request)
    {
        try {
            $user = auth()->user();
            if (!in_array($user->roles->first()->id, [1, 5])) {
                $wallets = Wallet::with(['cashier'])->where('cashier_id', $user->id)->when(
                    !empty($request->from_date) && !empty($request->to_date),
                    function ($q) use ($request) {
                        $q->whereBetween('created_at', [
                            $request->from_date . ' 00:00:00',
                            $request->to_date . ' 23:59:59'
                        ]);
                    }
                )->orderBy('id', 'DESC')->paginate(30);
            } else {
                $wallets = Wallet::with(['cashier'])->when(
                    !empty($request->from_date) && !empty($request->to_date),
                    function ($q) use ($request) {
                        $q->whereBetween('created_at', [
                            $request->from_date . ' 00:00:00',
                            $request->to_date . ' 23:59:59'
                        ]);
                    }
                )->when(!empty($request->cashier_id), function ($q) use ($request) {
                    $q->where('cashier_id', $request->cashier_id);
                })->orderBy('id', 'DESC')->paginate(30);
            }
            $cashiers = User::role('cashier')->select(['id', 'name'])->get();
            return view('layouts.wallet.deposit', compact('wallets', 'cashiers'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createDeposit(Request $request)
    {
        DB::beginTransaction();
        try {
            $wallet = Wallet::where('cashier_id', $request->cashier_id)->where('date', date('Y-m-d'))->first();
            if (!$wallet) {
                $walletDeposit = Wallet::create([
                    'requested_by' => null,
                    'cashier_id' => $request->cashier_id,
                    'initial_balance' => $request->balance,
                    'balance' => $request->balance,
                    'date' => date(Carbon::now()->toDateString()),
                ]);
                if ($walletDeposit) {
                    $walletTransaction = WalletTransaction::create([
                        'wallet_id' => $walletDeposit->id,
                        'requested_by' => auth()->user()->id,
                        'cashier_id' => $request->cashier_id,
                        'amount' => $request->balance,
                        'type' => 'Credit',
                        'status' => 'approved',
                        'description' => 'Wallet Credit by ' . auth()->user()->roles->first()->name,
                        'remarks' => '',
                    ]);
                    if ($walletTransaction) {
                        $users = User::whereIn('id', [3, $request->cashier_id])->pluck('id')->toArray();
                        foreach ($users as $userId) {
                            Notification::create([
                                'user_id' => $userId,
                                'title' => 'Wallet Recharged Successfully',
                                'message' => $userId != 5 ?
                                    'Cashier ' . User::find($userId)->name . ' wallet has been successfully recharged with an amount of ' . number_format($request->balance, 2) . '.'
                                    : 'Your wallet has been successfully recharged with an amount of ' . number_format($request->balance, 2) . '.',
                                'type' => 'wallet',
                            ]);
                        }
                        DB::commit();
                        return redirect()->route('admin.wallets.deposit')->with('success', 'Wallet deposit successfully');
                    } else {
                        return back()->with('error', 'Today wallet credit failed');
                    }
                } else {
                    return back()->with('error', 'Today wallet credit failed');
                }
            } else {
                return back()->with('error', 'Today wallet already created');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.wallets.deposit')->with('error', $e->getMessage());
        }
    }

    public function cashCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ], [
            'from_date.date' => 'The from date must be a valid date.',
            'to_date.date' => 'The to date must be a valid date.',
            'to_date.after_or_equal' => 'The to date must be the same as or after the from date.',
        ]);

        try {
            $user = auth()->user();
            if ($user->roles->first()->id == 2) {
                $cashCounts = CashCount::selectRaw("DATE(cash_count.created_at) as date")
                    ->selectRaw("MAX(cash_count.id) as id")                     // latest id of that day
                    ->selectRaw("MAX(cash_count.created_at) as created_at")
                    ->selectRaw("MAX(cash_count.remarks) as remarks")
                    ->selectRaw("SUM(cash_count.count) as total_count") // latest timestamp of that day
                    ->selectRaw("SUM(cash_count.currency * count) as total_value")
                    ->leftJoin('wallets', 'wallets.id', '=', 'cash_count.wallet_id')
                    ->leftJoin('users', 'users.id', '=', 'cash_count.cashier_id')
                    ->selectRaw("MAX(wallets.initial_balance) as wallet_initial_balance")
                    ->selectRaw("MAX(wallets.balance) as wallet_balance")
                    ->selectRaw("MAX(users.name) as cashier_name")
                    ->groupByRaw("DATE(cash_count.created_at)")
                    ->orderBy('cash_count.date', 'DESC');
                if ($request->from_date && $request->to_date) {
                    $cashCounts->whereBetween('cash_count.created_at', [
                        $request->from_date . " 00:00:00",
                        $request->to_date . " 23:59:59"
                    ]);
                } elseif ($request->from_date) {
                    $cashCounts->whereDate('cash_count.created_at', '>=', $request->from_date);
                } elseif ($request->to_date) {
                    $cashCounts->whereDate('cash_count.created_at', '<=', $request->to_date);
                }
                $cashCounts->where('cash_count.cashier_id', $user->id);
                $cashCounts = $cashCounts->paginate(30);
                $todayCashCountExist = CashCount::where('cashier_id', $user->id)->where('date', date(Carbon::now()->toDateString()))->count();
                return view('layouts.wallet.cash-count', compact('cashCounts', 'todayCashCountExist'));
            } else {
                $cashCounts = CashCount::selectRaw("DATE(cash_count.created_at) as date")
                    ->selectRaw("MAX(cash_count.id) as id")                     // latest id of that day
                    ->selectRaw("MAX(cash_count.created_at) as created_at")
                    ->selectRaw("MAX(cash_count.remarks) as remarks")
                    ->selectRaw("SUM(cash_count.count) as total_count") // latest timestamp of that day
                    ->selectRaw("SUM(cash_count.currency * count) as total_value")
                    ->leftJoin('wallets', 'wallets.id', '=', 'cash_count.wallet_id')
                    ->leftJoin('users', 'users.id', '=', 'cash_count.cashier_id')
                    ->selectRaw("MAX(wallets.initial_balance) as wallet_initial_balance")
                    ->selectRaw("MAX(wallets.balance) as wallet_balance")
                    ->selectRaw("MAX(users.name) as cashier_name")
                    ->groupByRaw("DATE(cash_count.created_at)")
                    ->orderBy('cash_count.date', 'DESC');
                if ($request->from_date && $request->to_date) {
                    $cashCounts->whereBetween('cash_count.created_at', [
                        $request->from_date . " 00:00:00",
                        $request->to_date . " 23:59:59"
                    ]);
                } elseif ($request->from_date) {
                    $cashCounts->whereDate('cash_count.created_at', '>=', $request->from_date);
                } elseif ($request->to_date) {
                    $cashCounts->whereDate('cash_count.created_at', '<=', $request->to_date);
                }
                $cashCounts = $cashCounts->paginate(30);
                $todayCashCountExist = CashCount::where('date', date(Carbon::now()->toDateString()))->count();
                return view('layouts.wallet.cash-count', compact('cashCounts', 'todayCashCountExist'));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return back()->with('error', $e->getMessage());
        }
    }

    public function createCashCount(Request $request)
    {
        try {
            $wallet = Wallet::where('cashier_id', auth()->user()->id)->where('date', date(Carbon::now()->toDateString()))->first();
            if ($wallet) {
                $totalAmount = 0;
                foreach ($request->currency as $key => $curr) {
                    $totalAmount += $curr * $request->count[$key];
                }

                if ($wallet->balance != $totalAmount) {
                    if ($request->has('remarks') && trim($request->remarks) == null) {
                        return back()->with('error', 'Remarks is required');
                    }
                }
                foreach ($request->currency as $key => $curr) {
                    CashCount::create([
                        'wallet_id' => $wallet->id,
                        'cashier_id' => auth()->user()->id,
                        'currency' => $curr,
                        'count' => $request->count[$key],
                        'added_by' => auth()->user()->id,
                        'remarks' => $request->remarks,
                        'date' => date(Carbon::now()->toDateString())
                    ]);
                }
                if ((int)$totalAmount != (int)$wallet->balance) {
                    $value = "";
                    if ((int)$totalAmount > (int)$wallet->balance) {
                        $value = "exceeds";
                    } elseif ((int)$totalAmount < (int)$wallet->balance) {
                        $value = "lower";
                    }
                    Notification::create([
                        'user_id' => auth()->user()->id,
                        'title' => "Today's cash count total does not match",
                        'message' => "Cash count has been submitted, but the 
                            total cash count amount of " . number_format($totalAmount, 2) . " JMD " . $value . " the 
                            remaining wallet balance of " . number_format($wallet->balance, 2) . " JMD.",
                        'type' => 'wallet',
                    ]);
                    return redirect()->route('admin.wallets.cash-count')->with('success', 'Cash count submitted successfully, but Wallet amount is not matched');
                } else {
                    return redirect()->route('admin.wallets.cash-count')->with('success', 'Cash count submitted successfully');
                }
            } else {
                return back()->with('error', 'Today Wallet is not top-up, please request for top-up');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadCashCount(Request $request)
    {

        try {
            $cashCount = CashCount::where('id', $request->id)->first();
            $cashCounts = CashCount::where('created_at', $cashCount->created_at)
                ->orderBy('id', 'ASC')->get();
            $wallet = Wallet::with(['cashier'])->where('cashier_id', $cashCount->cashier_id)->whereDate('created_at', $cashCount->created_at->format('Y-m-d'))->first();

            $pdf = PDF::loadView(
                'invoices.cash-count',
                compact('cashCounts', 'wallet')
            )
                ->setPaper([0, 0, 230, 500], 'portrait')
                // 80mm paper width
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0);
            $filename = 'cash-count-Receipt-' . $cashCount->id . '.pdf';
            // If ?action=view => show in browser (print)
            if (request()->query('action') === 'view') {
                return $pdf->stream($filename);
            }
            // Default => download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateDeposit(Request $request)
    {
        //        try {
        //            DB::beginTransaction();
        //            $wallet = Wallet::find($request->id);
        //            $wallet->initial_balance = $wallet->initial_balance + $request->balance;
        //            $wallet->balance = $wallet->balance + $wallet->balance;
        //        } catch (\Exception $e) {
        //            DB::rollBack();
        //            return back()->with('error', $e->getMessage());
        //        }
    }
}
