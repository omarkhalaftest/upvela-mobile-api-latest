<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'number_of_stock', 'sold', 'percentage', 'permonth', 'limit', 'active'];
}
