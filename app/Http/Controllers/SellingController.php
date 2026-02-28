<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellingController extends Controller
{
    // App\Http\Controllers\SupplierController.php
    public function index()
    {
        return view('layouts.selling.index');
    }

}
