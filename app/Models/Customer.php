<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    // Mass assignable fields
    protected $fillable = [
        'customer_type',
        'name',
        'email',
        'country_code',
        'phone',
        'street_address',
        'city',
        'postal_code',
        'country',
        'company_name',
        'company_email',
        'company_phone_number',
        'tax',
        'bank_name',
        'bank_branch',
        'account_number',
        'status',
        'is_loyal'
    ];
}
