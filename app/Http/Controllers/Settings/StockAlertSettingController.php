<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class StockAlertSettingController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('layouts.settings.stock-alert', compact('products'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.low_stock_limit' => 'required|decimal:0,2|min:0',
            'products.*.high_stock_limit' => [
                'required',
                'decimal:0,2',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $productNumber = ((int) $index) + 1;

                    $lowStock = request()->input("products.$index.low_stock_limit");

                    if ($value < $lowStock) {
                        $fail("The high stock limit must be greater than or equal to the low stock limit");
                    }
                }
            ],
        ]);


        // Bulk update in one go
        foreach ($request->products as $id => $data) {
            Product::where('id', $id)->update([
                'low_stock_limit' => $data['low_stock_limit'],
                'high_stock_limit' => $data['high_stock_limit'],
            ]);
        }

        return redirect()->back()->with('success', 'All stock alert limits updated successfully!');
    }
}
