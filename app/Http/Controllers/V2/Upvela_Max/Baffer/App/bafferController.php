<?php

namespace App\Http\Controllers\V2\Upvela_Max\Baffer\App;

use App\Models\buffer;
use App\Models\limit_buffer;

use App\Models\buffer_plan;
use App\Models\buffer_user;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Upvela_Max\buffer\OneBufferRequest;
use App\Http\Resources\Upvela_Max\buffer\AllBuffersResourece;
use App\Http\Resources\Upvela_Max\buffer\OneBuffersResourece;
use App\Models\plan;
use App\Models\Payment;


class bafferController extends Controller
{
    use ResponseJson;
    public function getAllBaffer(Request $request)
    {
         $user=auth('api')->user();
        if(!$user)
        {
         return $this->token();
        }
          $buffer_plan=buffer_plan::pluck('buffer_id');
        $buffer=buffer::whereIn('id',$buffer_plan)->get();
        $buffer->each(function($buffer) use ($user) {
            $count =$this->Subscribe($user->id,$buffer->id);
            $buffer->count_sub = $count;
        });


         return  AllBuffersResourece::collection($buffer);
    }
    // public function getOneBuffer(OneBufferRequest $request)
    // {
    //         $user=auth('api')->user();
    //       if(!$user)
    //       {
    //         return $this->token();
    //       }
    //       $buffer_id=$request['buffer_id'];
    //       $plan_user=$user->plan_id;


    //       $buffer=buffer::find($buffer_id);

    //       $buffer->load('alldesc');


    //      $buffer->count_sub=$this->Subscribe($user->id,$buffer_id);
    //      $buffer_plan=buffer_plan::where('buffer_id',$buffer_id)->first();
    //      $planName=plan::where('id',$buffer_plan->plan_id)->first();
    //      $buffer->plan_name=$planName->name;
    //      $buffer->price_plan=$planName->price;
    //       if($buffer_plan->plan_id > $user->plan_id )
    //      {
    //           $buffer->activate=0;
    //         $buffer->message="Upgrade plan";

    //     return new OneBuffersResourece($buffer);
    //      }else{

    //       $limit_count=$this->limit_buffer($buffer_id);
    //         ///////////////////////////if have permisions ////////////////////////
    //         if ($user->plan_id == 6) {
    //             $payment = Payment::where('user_id', $user->id)->latest()->first();
    //             if ($payment->plan_id == 6 && $payment->per_month == 12) {
    //                 $buffer_user = buffer_user::where('active', 1)->where('user_id', $user->id)->sum('amount');
    //                 if ($buffer_user >= 20000) {

    //                     $buffer->message = "Limit";
    //                     return new OneBuffersResourece($buffer);
    //                 } else {
    //                     $buffer->message = "Buy";
    //                     return new OneBuffersResourece($buffer);
    //                 }
    //             }
    //         }

    //         /////////////////////////// end permisions ////////////////////////

    //         if($buffer->count_sub >= $limit_count)
    //         {
    //             $buffer->message="Limit";
    //             return new OneBuffersResourece($buffer);
    //         }else{

    //             $buffer->message="Buy";

    //             return new OneBuffersResourece($buffer);
    //         }
    //      }
    


    // }
    
     public function getOneBuffer(OneBufferRequest $request)
    {
        $user = auth('api')->user();

        $buffer_id = $request['buffer_id'];
        $plan_user = $user->plan_id;


        $buffer = buffer::find($buffer_id);

        $buffer->load('alldesc');


        $buffer->count_sub = $this->Subscribe($user->id, $buffer_id);
        $buffer_plan = buffer_plan::where('buffer_id', $buffer_id)->first();
        // $planName = plan::where('id', $buffer_plan->plan_id)->first();
        $buffer->plan_name = $buffer_plan->plan->name;
        $buffer->price_plan = $buffer_plan->plan->price;
  
        $limit_count = $this->limit_buffer($buffer_id);
  
        ///////////////////////// end permisions ////////////////////////

        if ($buffer->count_sub >= $limit_count) {
            $buffer->message = "Limit";
            return new OneBuffersResourece($buffer);
        } else {

            $buffer->message = "Buy";

            return new OneBuffersResourece($buffer);
        }
    }

    public function Subscribe($user_id,$buffer_id)
    {
       $count= buffer_user::where('user_id', $user_id)
                                ->where('buffer_id', $buffer_id)
                                ->where('active', 1)
                                ->count();

                                return $count;

    }
        
    public function limit_buffer($buffer_id)
    {
          $limit_buffer=limit_buffer::where('buffer_id',$buffer_id)->first();
          return $limit_buffer->count;
    }
    
     public function getOneBufferNew(OneBufferRequest $request)
    {
        $user = auth('api')->user();

        $buffer_id = $request['buffer_id'];
        $plan_user = $user->plan_id;
        $buffer = buffer::find($buffer_id);
        $buffer->load('alldesc');
        $buffer->count_sub = $this->Subscribe($user->id, $buffer_id);
        $buffer_plan = buffer_plan::where('buffer_id', $buffer_id)->first();
        $buffer->plan_name = $buffer_plan->plan->name;
        $buffer->price_plan = $buffer_plan->plan->price;
        $buffer->message = "Buy";

        return new OneBuffersResourece($buffer);
    }

}
