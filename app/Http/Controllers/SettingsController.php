<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // App\Http\Controllers\SupplierController.php
    public function index()
    {
        return view('layouts.settings.general-settings');
    }


    public function invoice_settings(){
        return view('layouts.settings.invoice-settings');
    }

    public function notification_settings(){
        return view('layouts.settings.notification-settings');
    }

    public function user_settings(){
        return view('layouts.settings.user-settings');
    }

}
