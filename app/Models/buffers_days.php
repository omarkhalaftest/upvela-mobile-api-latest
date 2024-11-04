<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buffers_days extends Model
{
    use HasFactory;

    protected $fillable=['for_day','money_for_buffers','precantage','count_user','buffer_id','active'];
}
