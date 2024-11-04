<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class plan_bot extends Model
{
    use HasFactory;

    protected $table='plan_bot';

    protected $fillable=[
        'plan_id',
        'bot_num',
 
    ];
}
