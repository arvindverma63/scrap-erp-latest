<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceSettingController extends Controller
{
    /**
     * Display the invoice settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch existing settings or set default values
        $settings = [
            'prefix' => Setting::where('key', 'invoice_prefix')->first()->value ?? 'INV-',
            'receipt_prefix' => Setting::where('key', 'receipt_prefix')->first()->value ?? null,
            'receipt_signature' => Setting::where('key', 'receipt_signature')->first()->value ?? null,
        ];

        return view('layouts.settings.invoice-settings', compact('settings'));
    }

    /**
     * Update the invoice settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            'prefix' => 'required|string|max:10',
            'receipt_prefix' => 'required|string|max:10',
            'receipt_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Update prefixes
        Setting::updateOrCreate(
            ['key' => 'invoice_prefix'],
            ['value' => $request->prefix]
        );

        Setting::updateOrCreate(
            ['key' => 'receipt_prefix'],
            ['value' => $request->receipt_prefix]
        );

        // Handle receipt signature upload
        if ($request->hasFile('receipt_signature')) {
            $file = $request->file('receipt_signature');

            // Convert image to Base64
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();
            $base64Image = "data:$mimeType;base64,$imageData";

            // Save in settings
            Setting::updateOrCreate(
                ['key' => 'receipt_signature'],
                ['value' => $base64Image]
            );
        }


        return redirect()->route('admin.settings.invoice')->with('success', 'Invoice settings updated successfully.');
    }



    public function uploadSignature(Request $request)
    {
        $request->validate([
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('signature')) {
            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'receipt_signature')->first();
            if ($oldLogo && $oldLogo->value) {
                Storage::disk('public')->delete($oldLogo->value);
            }

            // Store new logo
            $path = $request->file('logo')->store('receipt_signature', 'public');

            Setting::updateOrCreate(
                ['key' => 'receipt_signature'],
                ['value' => $path]
            );
        }
    }
}
