<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobelPoll extends Model
{
    use HasFactory;

    protected $fillable = ['percentage' , 'percentage_rank'];
}
