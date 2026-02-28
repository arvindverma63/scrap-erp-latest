<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchasePayment; // or SalesPayment
use App\Models\SalesPayment;
use App\Models\User;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchasePayment::with(['invoice.purchaseOrder.supplier', 'invoice.purchaseOrder.cashier'])->when(!empty($request->cashier_id), function($q) use ($request){
            $q->whereHas('invoice.purchaseOrder.cashier',function($q) use ($request){
                            $q->where('id', $request->cashier_id);
                    });
        })->when(!in_array(auth()->user()->roles->first()->id,[1,5]), function($q){
            $q->whereHas('invoice.purchaseOrder.cashier', function($q) {
                $q->where('id', auth()->user()->id);
            });
        });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($main) use ($search) {
                $main->whereHas('invoice', function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('purchaseOrder', function ($q2) use ($search) {
                            $q2->whereHas('supplier', function ($q3) use ($search) {
                                $q3->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                        });
                })
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereDate('payment_date', $search);
            });
        }
         $payments = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

         $cashiers = User::Role(['cashier'])->get();

        return view('layouts.Payments.index', compact('payments', 'cashiers'));
    }


    public function salesIndex(Request $request)
    {
        $query = SalesPayment::with(['invoice.sellingOrder.customer','invoice.sellingOrder.cashier'])->when(!empty($request->cashier_id), function($q) use ($request){
            $q->whereHas('invoice.sellingOrder.cashier', function($q) use ($request) {
                $q->where('id', $request->cashier_id);
            });
        })->when(!in_array(auth()->user()->roles->first()->id,[1,5]), function($q){
            $q->whereHas('invoice.sellingOrder.cashier', function($q) {
                $q->where('id', auth()->user()->id);
            });
        });
        // Optional filter by invoice number
        if ($request->filled('invoice_no')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->invoice_no}%");
            });
        }

        $payments = $query->orderBy('id', 'DESC')->paginate(30)->withQueryString();

        $cashiers = User::role(['cashier'])->get();

        return view('layouts.Payments.sales-payment-index', compact('payments', 'cashiers'));
    }
}
