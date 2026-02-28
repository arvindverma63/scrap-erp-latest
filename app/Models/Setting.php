<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'updated_by'
    ];

    protected $casts = [
        'currency_count' => 'array',
    ];

    /**
     * Automatically set updated_by when creating or updating.
     */
    protected static function booted()
    {
        // When creating
        static::creating(function ($setting) {
            if (Auth::check()) {
                $setting->updated_by = Auth::id();
            }
        });

        // When updating
        static::updating(function ($setting) {
            if (Auth::check()) {
                $setting->updated_by = Auth::id();
            }
        });
    }
}
