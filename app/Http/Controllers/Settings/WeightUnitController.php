<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\WeightUnit;

class WeightUnitController extends Controller
{
    /**
     * Display a listing of weight units.
     */
    public function index()
    {
        $units = WeightUnit::orderBy('created_at', 'desc')->paginate(30);
        return view('layouts.settings.weight_units.index', compact('units'));
    }

    /**
     * Store a newly created weight unit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:weight_units,name',
            'description' => 'required|string|max:50',

        ]);

        WeightUnit::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Weight Unit added successfully!');
    }

    /**
     * Show the form for editing the specified weight unit.
     */
    public function edit($id)
    {
        $unit = WeightUnit::findOrFail($id);
        return view('layouts.settings.weight_units.edit', compact('unit'));
    }

    /**
     * Update the specified weight unit.
     */
    public function update(Request $request, $id)
    {
        $unit = WeightUnit::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:weight_units,name,' . $unit->id,
            'description' => 'required|string|max:50',
        ]);

        $unit->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Weight Unit updated successfully!');
    }

    /**
     * Remove the specified weight unit.
     */
    public function destroy($id)
    {
        $inventries = Inventory::where('weight_unit_id', $id)->count();
        if ($inventries) {
            return back()->with('error', 'Weight Unit is used in ' . $inventries . ' Inventory');
        }
        $unit = WeightUnit::findOrFail($id);
        $unit->delete();

        return redirect()->back()->with('success', 'Weight Unit deleted successfully!');
    }
}
