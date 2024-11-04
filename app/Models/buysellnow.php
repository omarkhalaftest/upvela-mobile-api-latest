<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buysellnow extends Model
{
    use HasFactory;

    protected $fillable=['ticker','user_id','recomindation_id','type'];

}
