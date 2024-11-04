<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buffer_plan extends Model
{
    use HasFactory;

    protected $fillable=['buffer_id','plan_id'];
  public function plan()
    {
        return $this->belongsTo(plan::class, 'plan_id');
    }
}
