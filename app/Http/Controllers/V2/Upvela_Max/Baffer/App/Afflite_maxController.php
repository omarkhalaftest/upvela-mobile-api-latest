<?php

namespace App\Http\Controllers\v2\Upvela_Max\Baffer\App;

 use App\Models\profit_blance_user_afflite;
 use App\Models\transfer_many;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Afflite_maxController extends Controller
{
    use ResponseJson;
    public function afflite_max(Request $request)
    {
    $user=auth('api')->user();
     if(!$user)
     {
           return $this->token();
     }
     $buffer_user=profit_blance_user_afflite::where('user_id',$user->id)->latest()->first();
     if(!$buffer_user)
     {
        $buffer_user->blance=0;
     }

     $history_withdraw=transfer_many::where([
        'user_id' => $user->id,
        'type' => "afflite_max",
    ])->get();


    $buffer_user->history=$history_withdraw;

    return response()->json([
        'success' => true,
        'data' => $buffer_user,
        'status' => 200,
    ]);


















    }
}
