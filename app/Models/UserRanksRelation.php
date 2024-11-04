<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRanksRelation extends Model
{
    use HasFactory;

    protected $table = 'user_ranks';

    protected $fillable = ['user_id', 'rank_id', 'block_generation', 'child_number', 'direct_child_number', 'child_free'];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRank()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }
}
