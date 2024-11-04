<?php

namespace App\Http\Controllers\V2\Upvela_Max\Baffer\App;

use App\Models\buffer;
use App\Models\buffer_user;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Upvela_Max\buffer\allSubBuffersResourece;

class AllSubscribeBafferController extends Controller
{
    use ResponseJson;
    public function allSubscribe()
    {
        $user=auth('api')->user();
       if(!$user)
       {
        return $this->token();
       }
       $buffer_user=buffer_user::where('user_id',$user->id)->get();
       $buffer_user->each(function($items)use($user){

        $buffer=buffer::where('id',$items->buffer_id)->first();
        $items->buffer_name=$buffer->name;
        $items->buufer_img=$buffer->img;
        $items->amount=$buffer->amount;
       });

       return allSubBuffersResourece::collection($buffer_user);

    }
}
