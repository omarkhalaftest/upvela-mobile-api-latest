<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class test extends Model
{
    use HasFactory;

    protected $table="test_t";

    protected $fillable=['name','price','qu','status','orderID','MyBlance','MonyForOrder','statusopretion','time'];
}
