<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buffer_user extends Model
{
    use HasFactory;

    protected $fillable=['user_id','buffer_id','start_subscrip','end_subscrip','amount','active','plan_id','HashID','per_month'];
 
     public function user()
    {

        return $this->belongsTo(User::class);
    }
}
