<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    /**
     * Show settings form.
     */
    public function index()
    {
        // Fetch all settings as key=>value array
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('layouts.settings.general-settings', compact('settings'));
    }

    /**
     * Store/Update settings.
     */
    public function update(Request $request)
    {
        // ✅ Validate input including new fields
        $request->validate([
            'website_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website_email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'currency_symbol' => 'nullable|string|max:10',
            'admin_logo' => 'nullable|image|mimes:png,ico,jpg,jpeg|max:2048',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'company_address' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:255',
            'selling_currency_symbol' => 'required',
            'buying_currency_symbol' => 'required',
            'usd_to_jmd_rate' => 'required',
            'currency_count' => 'required|array'
        ]);

        // ✅ Save settings
        $request->currency_count = implode(',', $request->currency_count);
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                // Convert file to base64 with mime type
                $value = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        return redirect()->back()->with('success', 'Settings saved successfully!');
    }
}
