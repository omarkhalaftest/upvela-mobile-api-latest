<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buffer extends Model
{
    use HasFactory;

    protected $fillable=['amount','img','name','precantage'];


    public function alldesc()
    {
        return $this->hasMany(buffer_desc::class);
    }
    
   
}
