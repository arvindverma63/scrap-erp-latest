<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialsController extends Controller
{
    // App\Http\Controllers\SupplierController.php
    public function index()
    {
        return view('layouts.materials.index');
    }


}
