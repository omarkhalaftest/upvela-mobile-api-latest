<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bots extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'bot_name',
    ];

    protected $table = 'bots';
    
      public function bot_order()
    {
        return $this->hasMany(BotOrder::class, 'bot_id');
    }

    // علاقة مع جدول experts
    public function experts()
    {
        return $this->hasMany(Expert::class, 'bot_num', 'id');
    }
}
