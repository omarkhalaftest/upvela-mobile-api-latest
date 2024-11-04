<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax;

use App\Http\Controllers\Controller;
  use App\Models\buffer;
use Illuminate\Http\Request;
use App\Models\precentage_puffers;
use App\Models\buffer_user;
use App\Models\buffers_days;
use App\Http\Resources\Upvela_Max\Divie_Buffer\monyfordayResource;
use Carbon\Carbon;



class divite_bufferController extends Controller
{
    //1 - get totle_mony_afert_divite for one buffers
    //2 - get count from buffer active
    //3- divite totle money / count of users
    //4 - Multiply precentage for plan to reslute divite divite totle money / count of users


    public function index()
    {
        

          $get_money_for_buffers=precentage_puffers::where('is_active',1)->get();


        $get_money_for_buffers->each(function ($buffer) {
        $totle_money_for_one_buffer= $buffer['totle_mony_afert_divite'];
         // 2- count of users from one buffer
         // divite to totle mony
        $count_users_active=$this->count_user_active_to_buffer($buffer->plan_id)->count();
        $count_user_in_buffers=$totle_money_for_one_buffer / $count_users_active;
        $buffer['count_user']=$count_users_active;
        $buffer['after_divite_on_countuser']=$count_user_in_buffers;

               /////////////////////////END COUNT USERS ////////////////////////////////
        //3 -Multiply totle money * precentage for one buffer
        $precentage_for_bufer=$this->get_precentage_buffer($buffer->plan_id);
        $disocount_precentage=$count_user_in_buffers * $precentage_for_bufer;
        $buffer['precantage']=$precentage_for_bufer;
        $buffer['after_diviate_precantage']=$disocount_precentage;

              /////////////////////////END DIVITE PRECENATGE ////////////////////////////////

        $for_day=$disocount_precentage  / 13;
        $buffer['forday']=$for_day;
        
        ///////////////////////////////get name buffer ///////////////////////////////
        $buffername=buffer::find($buffer->plan_id);
        $buffer->buffer_name=$buffername->name;



        //
        $buffer_id=$buffer->plan_id;
        $count_user=$count_users_active;
        $precentage_for_buffer=$precentage_for_bufer;
        $totlemonyforprofit=$disocount_precentage;
        $for_day=$for_day;
        $this->buffer_days($buffer_id,$count_user,$precentage_for_buffer,$totlemonyforprofit,$for_day);



        });

        // return $get_money_for_buffers;
        
        return monyfordayResource::collection($get_money_for_buffers);

    }




    public function count_user_active_to_buffer($buffer_id)
    {
        $today = Carbon::now()->format('Y-m-d');
     return   $buffer_user=buffer_user::where('buffer_id',$buffer_id)->where('active',1)->whereDate('start_subscrip', '!=', $today)->get();

    }

    public function get_precentage_buffer($buffer_id)
    {
        $buffer_user=buffer::where('id',$buffer_id)->latest()->first();
        return $buffer_user->precantage /100;

    }

    public function buffer_days($buffer_id,$count_user,$precentage_for_buffer,$totlemonyforprofit,$for_day)
    {
        $updated=buffers_days::where('buffer_id',$buffer_id)->where('active',1)->latest()->first();
        if(!$updated){
            $buffer_per_day=buffers_days::create([
                'buffer_id'=>$buffer_id,
                'count_user'=>$count_user,
                'precantage'=>$precentage_for_buffer,
                'money_for_buffers'=>$totlemonyforprofit,
                'for_day'=>$for_day,
                'active'=>1,

            ]);
        }else{
            $updated->active=0;
            $updated->save();
            $buffer_per_day=buffers_days::create([
                'buffer_id'=>$buffer_id,
                'count_user'=>$count_user,
                'precantage'=>$precentage_for_buffer,
                'money_for_buffers'=>$totlemonyforprofit,
                'for_day'=>$for_day,
                'active'=>1,

            ]);
        }


    }



}
