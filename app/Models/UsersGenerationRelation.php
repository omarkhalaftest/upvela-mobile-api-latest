<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersGenerationRelation extends Model
{
    use HasFactory;

    protected $table = 'user_generation_relations';

    protected $fillable = ['user_id_child', 'user_id_father', 'free_check', 'generation_id', 'child_plan_price', 'father_money','block_user'];


    public function generation()
    {
        return $this->belongsTo(Generation::class, 'generation_id');
    }

    public function getFather()
    {
        return $this->belongsTo(User::class, 'user_id_father');
    }

    public function getChild()
    {
        return $this->belongsTo(User::class, 'user_id_child');
    }
}
