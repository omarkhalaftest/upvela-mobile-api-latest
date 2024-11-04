<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class limit_buffer extends Model
{
    use HasFactory;

    protected $fillable=['count','buffer_id'];
}
