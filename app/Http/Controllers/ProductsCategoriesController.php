<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductsCategoriesController extends Controller
{
    // Display all product categories
    public function index()
    {
        $categories = ProductCategory::orderBy('created_at', 'desc')->get();
        return view('layouts.products-categories.index', compact('categories'));
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        ProductCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.product_categories.index')->with('success', 'Category added successfully.');
    }

    // Show the edit form
    public function edit(ProductCategory $product_category)
    {
        return view('layouts.products-categories.edit', compact('product_category'));
    }

    // Update the category
    public function update(Request $request, ProductCategory $product_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $product_category->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.product_categories.index')->with('success', 'Category updated successfully.');
    }

    // Delete the category
    public function destroy(ProductCategory $product_category)
    {
        $product_category->delete();

        return redirect()->route('admin.product_categories.index')->with('success', 'Category deleted successfully.');
    }
}
