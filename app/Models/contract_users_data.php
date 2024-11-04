<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contract_users_data extends Model
{
    use HasFactory;

    protected $guarded = []; // This means no attributes are guarded, all are mass assignable.
}
