<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax;

use Carbon\Carbon;
use App\Models\User;
use App\Models\buffer;
use App\Models\buffer_user;
use App\Models\buffers_days;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Models\precentage_puffers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\profit_buffer_to_user;
use App\Http\Resources\Upvela_Max\Divie_Buffer\alluserfordayResource;
use App\Http\Controllers\V2\Dashbord\ProfitMax_Show\divirbuffer_for_userController;


class divite_user_bufferController extends Controller
{
    public function store_profit_to_user(Request $request)
    {
        set_time_limit(120);
             $buffers = buffer_user::where('active', 1)->get();
   
     

        $buffers->each(function ($user) {

            $days=$this->getdays($user->user_id);
            $daysRemaining=$days['daysRemaining'];
            $fromDay=$days['newDateString'];

            // Get money for one day for the buffer
            $bufferDays = buffers_days::where('buffer_id', $user->buffer_id)->where('active', 1)->latest()->first();
            $moneyPerDay = $user['money_for_day'] = $bufferDays->for_day;

            // Calculate total money for 15 days
            $totalMoney = $moneyPerDay * $daysRemaining;
            $user['mony_for_15day'] = $totalMoney;

//////////////////////////////////////////////////




            $user_id=$user->user_id;
            $buffer_id=$user->buffer_id;
            $from_day=$fromDay;
            $daysRemaining=$daysRemaining;
            $money_for_day=$totalMoney;



            // from day

             $this->storeProfitMax($user_id,$buffer_id,$from_day,$daysRemaining,$moneyPerDay,$money_for_day);



        });
        // $profit_buffer_to_user=profit_buffer_to_user::where('active',1)->get();
        
        
        $activeuser=new divirbuffer_for_userController();
        $request->request->add(['active' => 'active']); // Set the value of 'active' parameter
        return $activeuser->divite_for_user($request);

    //   return $activeuser->divite_for_user();
        // alluserfordayResource::collection($profit_buffer_to_user);

         
    }

    public function storeProfitMax($user_id,$buffer_id,$from_day,$daysRemaining,$moneyPerDay,$money_for_day)
    {
        if($daysRemaining <= 0)
        {


        }else{


            $profituser=profit_buffer_to_user::create([
                'user_id'=>$user_id,
                'buffer_id'=>$buffer_id,
                'from_day'=>$from_day,
                'daysRemaining'=>$daysRemaining,
                'money_for_day'=>$moneyPerDay,
                'mony_for_15day'=>$money_for_day,
                'active'=>1


              ]);
        }



    }

    public function getdays($user_id)
    {
        // check if found in profit_buffer_to_user

        $check_from_profit_user=profit_buffer_to_user::where('user_id',$user_id)->where('active',2)->latest()->first();
       if($check_from_profit_user)
       {

                $dateString = $check_from_profit_user->from_day; // Your date string
                $numberOfDaysToAdd = $check_from_profit_user->daysRemaining;

                // Convert the date string to a timestamp and add the number of seconds for the specified number of days
                $newTimestamp = strtotime($dateString . " +$numberOfDaysToAdd days");

                // Convert the new timestamp back to a date string
                $newDateString = date("Y-m-d", $newTimestamp);

                $currentTimestamp = time(); // Current timestamp
                // Convert the new date string to a timestamp
                $newTimestamp = strtotime($newDateString);
                // Calculate the difference in seconds
                $differenceInSeconds = $currentTimestamp - $newTimestamp;
                $daysRemaining = floor($differenceInSeconds / (60 * 60 * 24));


                // updated old active
            //    $updatedActive=$this->updatdActive($user_id);


                return ['daysRemaining' => $daysRemaining,'newDateString'=>$newDateString];



            }
        else{
            $buffers = buffer_user::where('active', 1)->where('user_id',$user_id)->latest()->first();
            $dateString=$buffers->start_subscrip;
            $numberOfDaysToAdd=1;
            $newTimestamp = strtotime($dateString . " +$numberOfDaysToAdd days");

            // Convert the new timestamp back to a date string
            $newDateString = date("Y-m-d", $newTimestamp); // Date after adding the specified number of days
            $currentTimestamp = time(); // Current timestamp
            // Convert the new date string to a timestamp
            $newTimestamp = strtotime($newDateString);
            // Calculate the difference in seconds
            $differenceInSeconds = $currentTimestamp - $newTimestamp;
            $daysRemaining = floor($differenceInSeconds / (60 * 60 * 24));

            return ['daysRemaining' => $daysRemaining,'newDateString'=>$newDateString];


       }




    }


    public function test()
    {


        $user_id=25;
        $days=$this->getdays($user_id);
       return $daysRemaining=$days['newDateString'];
    }

    public function updatdActive($user_id)
    {
        // profit_buffer_to_user::where('user_id', $user_id)
        // ->whereDate('created_at', '<', now()->toDateString()) // Rows created before today
        //  ->update(['active' => 0]);




    }

}
