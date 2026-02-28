<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightUnit extends Model
{
    use HasFactory;

    protected $table = 'weight_units';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'description',
    ];
}
