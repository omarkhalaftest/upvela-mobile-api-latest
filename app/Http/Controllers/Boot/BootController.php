<?php

namespace App\Http\Controllers\Boot;

use Carbon\Carbon;
use App\Models\bot;
use App\Models\bots_usdt;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\expert;
use App\Models\plan_bot;
 


class BootController extends Controller
{
    use ResponseJson;


    public function AllBot()
    {
        
        
        $botOrders = Bot::select('id', 'bot_name as botName', 'created_at')
       ->whereNotIn('id', [5,6,7,9,10,11])
       ->get();

        $botOrders->each(function ($bot) {

            $bot->currency = explode('_', $bot->botName)[0];


            $botOrderDates = $bot->bot_order->pluck('created_at');

            $maxCreatedAt = $botOrderDates->max(); // Maximum created_at date
            $minCreatedAt = $botOrderDates->min(); // Minimum created_at date
            if ($maxCreatedAt && $minCreatedAt) {
                // Calculate the difference in days between max and min created_at
                $totleDay = $maxCreatedAt->diffInDays($minCreatedAt);

                $bot->totalDays = $totleDay . " " . "days";
                $totalProfit = $bot->bot_order->where('side', 'sell')->sum('profit');
                 $totlesuccess=expert::where('bot_num',$bot->id)->where('last_tp','>',0)->count(); // Approximate to two decimal places

                $bot->profitPercentage = number_format($totlesuccess, 2) . "" . "%"; // Approximate to two decimal places

            } else {
                $bot->totalDays = null; // Handle the case when there are no valid dates.
                $bot->percentage = null; // Handle the case when there's no valid data for percentage calculation.
                $bot->formattedPercentage = null; // Set formatted percentage to null in case of missing data.
            }
            unset($bot->bot_order);
        });

        return $botOrders;
    }

    public function oneBot(Request $request)
    {
         $user = auth('api')->user();
        
        
        
        $singleBot = Bot::find($request['bot_id']);

        $currency = $singleBot->bot_name; //currency
        $singleBot->currency = $currency;

        // for totle Days
        $botDays = $singleBot->bot_order->pluck('created_at');
        $maxCreatedAt = $botDays->max(); // Maximum created_at date
        $minCreatedAt = $botDays->min();
        $totleDay = $maxCreatedAt->diffInDays($minCreatedAt);
        $singleBot->startActive = $minCreatedAt->toDateString() . " " . "-" . $totleDay . "day";
        // end for total days

        // for totle precantage per day and peer month and peer years
        // check if active bot or not

        $checkActive = bots_usdt::where('user_id', $user->id)->where('bot_id', $request['bot_id'])->where('bot_status',1)->first();
        if (!empty($checkActive)) {
            $singleBot->activeBot = 1;
           
        } else {
            $singleBot->activeBot = 0;
       

        }
    $plan_bot = plan_bot::where('plan_id',$user->plan_id)->where('bot_num',$request['bot_id'])->count();
          if($plan_bot == 1)
          {
               $singleBot->available=1;
          }else{
                   $singleBot->available=0;
          }


        $botProfit = $singleBot->bot_order->sum('profit'); // all sum profit
        $totalBuy = $singleBot->bot_order->where('side', 'buy')->sum('price');
        $chart = $singleBot->bot_order->where('side', 'sell')->pluck('profit'); //chart
        





        if ($totalBuy > 0) {
            $profitPercentage = ($botProfit / $totalBuy) * 100;
        } else {
            $profitPercentage = 0; // Handle the case where totalBuy is zero to avoid division by zero error.
        }


        if ($botProfit !== null) {
            // Average per day
            //  $totleDay= expert::where('bot_num', $request['bot_id'])
            // ->where('last_tp', '>', 0)
            //  ->where('created_at', '>', Carbon::now()->startOfDay()->addHours(3))->count();
            $averagePerDay = expert::where('bot_num', $request['bot_id'])
            ->where('last_tp', '>', 0)
             ->where('created_at', '>', Carbon::now()->startOfDay()->addHours(3))->count();
            $averagePerMonth = expert::where('bot_num', $request['bot_id'])
            ->where('last_tp', '>', 0)
             ->where('created_at', '>', Carbon::now()->startOfMonth()->addHours(3))->count();
            $averagePerYears = expert::where('bot_num', $request['bot_id'])
            ->where('last_tp', '>', 0)
             ->where('created_at', '>', Carbon::now()->startOfYear()->addHours(3))->count();


            // for add in singlebot


            $singleBot->profitPercentage = number_format($botProfit, 2) . "" . "%"; // Approximate to two decimal places

            $singleBot->averagePerDay =  $averagePerDay. "%";
            $singleBot->averagePerMonth = $averagePerMonth  . "%";
            $singleBot->averagePerYears = $averagePerYears . "%";
        } else {
            $singleBot->profitPercentage = null; // Approximate to two decimal places

            $singleBot->averagePerDay = null;
            $singleBot->averagePerMonth = null;
            $singleBot->averagePerYears = null;
        }

        $singleBot->chart = $chart;

        unset($singleBot->bot_order);

        return $singleBot;
    }
}
