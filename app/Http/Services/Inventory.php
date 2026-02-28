<?php

class Inventory
{
    public function updateInventory(Request $request) {
        $response = Inventory::create([
            'product_id' => $request->product_id,
            'weight_unit_id' => $request->weight_unit_id,
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'inventory_type'=> $request->inventory_type,
            'created_by'=>Auth::user()->id,
            'user_id' => $request->user_id,
        ]);

        return $response->json();
    }
}
