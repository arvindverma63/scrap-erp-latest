<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'description', // optional
        'status'
    ];

    /**
     * Relation to suppliers
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'product_type');
    }

    /**
     * Relation to products
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
