<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class selladminnow extends Model
{
    use HasFactory;



    protected $fillable=['user_id','symbol','side','quantity','price','stop_price','status','orderID','commission','admin_id'];


}
