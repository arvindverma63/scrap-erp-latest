<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'supplier_type',
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
//        'product_id',
        'tax',
        'bank_name',
        'bank_branch',
        'account_number',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function products()
    {
        return $this->hasMany(SupplierProduct::class, 'supplier_id', 'id');
    }

}
