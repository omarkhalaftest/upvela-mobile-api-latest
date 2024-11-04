<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profit_buffer_to_user extends Model
{
    use HasFactory;

    protected $fillable=['user_id','buffer_id','from_day','daysRemaining','money_for_day','mony_for_15day','active'];
}
