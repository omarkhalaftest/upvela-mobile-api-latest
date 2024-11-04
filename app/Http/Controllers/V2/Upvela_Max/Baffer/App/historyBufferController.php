<?php

namespace App\Http\Controllers\V2\Upvela_Max\Baffer\App;

use App\Http\Controllers\Controller;
use App\Models\buffer;
use App\Models\profit_blanace_user;
use App\Models\buffer_user;
use App\Models\profit_blance_user_afflite;
use Illuminate\Http\Request;
use App\Traits\ResponseJson;
use App\Models\transfer_many;



class historyBufferController extends Controller
{    use ResponseJson;

    public function history()
    {
     $user=auth('api')->user();
     if(!$user)
     {
           return $this->token();
     }
       $buffer_user=buffer_user::where('user_id',$user->id)->latest()->first();
       if(!$buffer_user){
                 
                     return response()->json([
                    'success'=>true,
                     'data' => [],
                    'status'=>200,
                ]);
       }
     $buffer=buffer::where('id',$buffer_user->buffer_id)->first();
     $buffer_user->buffer_name=$buffer->name;
     $buffer_user->img=$buffer->img;
     $buffer_user->email=$user->email;
     $profit_blanace_user=profit_blanace_user::where('user_id',$user->id)->first();
     if($profit_blanace_user) {
    // Cast the balance to an integer without rounding
            $buffer_user->amount = intval($profit_blanace_user->blance);
        } else {
            // Default to 0 if no balance is found
            $buffer_user->amount = 0;
        }

 

                 $history = buffer_user::where('user_id', $user->id)->get();
            $withdraw = transfer_many::where([
                'user_id' => $user->id,
                'type' => "MAX",
            ])->get();
            
            // Change type of $withdraw items to "withdraw"
            $withdraw->each(function ($item) {
                $item->type = "withdraw";
                $item->amount=$item->money;
                $item->start_subscrip=$item->created_at->toDateString();
            });
            
            // Change type of $history items to "Receive"
            $history->each(function ($item) {
                $item->type = "Receive";
                
            });
            
            // Merge $withdraw into $history
            $history = $history->merge($withdraw);
            
            // Assign the merged collection to $buffer_user->history
            $buffer_user->history = $history;
            $buffer_user_afflite=profit_blance_user_afflite::where('user_id',$user->id)->latest()->first();
             if(!$buffer_user_afflite)
             {
                $buffer_user->balance_afflite=0;
             }else{
                 $buffer_user->balance_afflite=$buffer_user_afflite->balance;
             }
            
            
            
            return response()->json([
                'success' => true,
                'data' => $buffer_user,
                'status' => 200,
            ]);

                






    }
}
