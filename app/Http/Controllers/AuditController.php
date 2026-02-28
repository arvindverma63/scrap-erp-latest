<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

// ✅ Import your Log model
use App\Models\PurchaseOrder;
use App\Models\SellingOrder;
use App\Models\User;

// ✅ (Optional) If you reference users
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

// ✅ correct import
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class AuditController extends Controller
{

    public function index(Request $request)
    {
        $query = Log::with('user')->latest();

        // Optional filters
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->action_type) {
            $query->where('status', $request->action_type);
        }

        if ($request->date) {
            $query->whereDate('login_at', $request->date);
        }

        $logs = $query->paginate(30);

        return view('layouts.audits.index', compact('logs'));
    }


    public function ledger(Request $request)
    {
        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'type' => 'nullable|in:Purchase,Sales',
            'keyword' => 'nullable|string|max:100',
        ],[
            'from_date.date' => 'The from date must be a valid date.',
            'to_date.date' => 'The to date must be a valid date.',
            'to_date.after_or_equal' => 'The to date must be the same as or after the from date.',
            'type.in' => 'The selected type is invalid. It must be Purchase or Sales.',
            'keyword.string' => 'The keyword must be a text value.',
            'keyword.max' => 'The keyword may not be greater than 100 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if($request->has('from_date') && $request->has('to_date')){
            if($request->has('to_date') > $request->has('from_date') ){
                return back()->with('error', 'Please provide correct date  order, To date is not greater then From date');
            }
        }

        $filters = $request->only(['from_date', 'to_date', 'type', 'keyword']);

        if($request->type == 'Purchase'){
            $purchase = PurchaseOrder::with(['supplier', 'cashier.roles'])
            ->select('id', 'supplier_id', 'total_amount', 'created_at', 'created_by')
            ->addSelect(\DB::raw("'Purchase' as type"));

        if ($request->filled('from_date')) {
            $purchase = $purchase->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
           $purchase = $purchase->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            // Purchase side
           $purchase =  $purchase->where(function ($query) use ($keyword) {
                $query->whereHas('supplier', fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                    ->orWhere('total_amount', 'like', "%{$keyword}%")
                    ->orWhereHas('cashier', function ($cashier) use ($keyword) {
                        $cashier->where('name', 'like', "%{$keyword}%")
                            ->orWhereHas('roles', fn($r) => $r->where('name', 'like', "%{$keyword}%"));
                    });
            });
        }
        $ledger = $purchase->orderBy('id', 'DESC')->paginate(30)->withQueryString();
        return view('layouts.ledger.index', compact('ledger', 'filters')); 
        }else if($request->type == 'Sales'){

            $sales = SellingOrder::with(['customer', 'cashier.roles'])
            ->select('id', 'customer_id', 'total_amount', 'created_at', 'created_by')
            ->addSelect(\DB::raw("'Sales' as type"));

        if ($request->filled('from_date')) {
           $sales =  $sales->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
          $sales = $sales->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            // Sales side
            $sales = $sales->where(function ($query) use ($keyword) {
                $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                    ->orWhere('total_amount', 'like', "%{$keyword}%")
                    ->orWhereHas('cashier', function ($cashier) use ($keyword) {
                        $cashier->where('name', 'like', "%{$keyword}%")
                            ->orWhereHas('roles', fn($r) => $r->where('name', 'like', "%{$keyword}%"));
                    });
            });
        }
         $ledger = $sales->orderBy('id', 'DESC')->paginate(30)->withQueryString();
         return view('layouts.ledger.index', compact('ledger', 'filters'));
        }

        // if ($request->filled('type')) {
        //     if ($request->type === 'Purchase') {
        //         $merged = $purchase->get();
        //     } elseif ($request->type === 'Sales') {
        //         $merged = $sales->get();
        //     } else {
        //         $merged = $purchase->get()->merge($sales->get());
        //     }
        // } else {
        //     $merged = $purchase->get()->merge($sales->get());
        // }

        
        // $merged = $merged->sortByDesc('created_at')->values();
        // $page = $request->get('page', 1);
        // $perPage = 30;

        // $ledger = new LengthAwarePaginator(
        //     $merged->forPage($page, $perPage),
        //     $merged->count(),
        //     $perPage,
        //     $page,
        //     ['path' => $request->url(), 'query' => $request->query()]
        // );
    }
}
