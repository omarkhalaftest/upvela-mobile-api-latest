<?php

namespace App\Http\Controllers\MarktingFees;

use App\Models\bot;
use App\Models\User;
use App\Models\marktingFess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarktingFessController extends Controller
{
    // from here
    public function forFees($id, $profit,$bot_num)
    {
        $user_comming = User::find($id);
        $user_comming_affiliate = $user_comming->comming_afflite;
        if ($user_comming->plan->name != 'free') {
            return $this->father3($user_comming_affiliate, $id, $profit,$bot_num);
        }
    }
    public function father3($comming, $id, $profit,$bot_num)
    {
        $user_master3 = User::where('affiliate_code', $comming)->first();
       

        if (!empty($user_master3)) {
             $userAfflite3 = $user_master3->comming_afflite; // for under father

            $user_plan_price = $profit;  // for profit
            $perc_paln = +$user_master3->plan->percentage1; // for percentage
            $status = "Boot";
            $Generations = 1;

            $this->affililateProccess($user_master3, $user_plan_price, $perc_paln, $id, $status, $profit, $Generations,$bot_num); //
            return $this->father2($userAfflite3, $id, $profit,$bot_num);
        }
    }
    public function father2($comming, $id, $profit,$bot_num)
    {
          $user_master2 = User::where('affiliate_code', $comming)->first();
         
        
       

        if (!empty($user_master2)) {
             $userAfflite2 = $user_master2->comming_afflite; // for under father
            $userAfflite2 = $user_master2->comming_afflite; // for under father
            $user_plan_price = $profit;  // for profit
            $perc_paln = +$user_master2->plan->percentage2; // for plan perecantage 2

            $status = "Boot";
            $Generations = 2;

            $this->affililateProccess($user_master2, $user_plan_price, $perc_paln, $id, $status, $profit, $Generations,$bot_num); //
            return $this->father1($userAfflite2, $id, $profit,$bot_num);
        }
    }
    public function father1($comming, $id, $profit,$bot_num)
    {

        $user_master1 = User::where('affiliate_code', $comming)->first();
        if (!empty($user_master1)) {
            $user_comming_affiliate2 = $user_master1->comming_afflite; // for under father
            $user_plan_price = $profit;  // for profit
            $perc_paln = +$user_master1->plan->percentage3; // for plan perecantage 2

            $status = "Boot";
            $Generations = 3;

            return $this->affililateProccess($user_master1, $user_plan_price, $perc_paln, $id, $status, $profit, $Generations,$bot_num); //

        }
    }

    public function storeInmarktingFess($user_id, $markting_id, $user_new_money, $status, $profit, $Generations,$bot_num)
    {
        $bot=bot::select('bot_name')->first($bot_num);

        $insert = marktingFess::create([
            'user_id' => $user_id,
            'markting_id' => $markting_id,
            'amount' => $user_new_money,
            'status' => $status,
            'profit_users' => $profit,
            'Generations' => $Generations,
            'bot_id'=>$bot->bot_name,
        ]);
    }

    function affililateProccess($user, $price, $perc, $id, $status, $profit, $Generations,$bot_num)
    {



        $user_old_money = $user->money;
        $user_new_money = ($perc / 100) * $price;
        $user_money = $user_old_money + $user_new_money;
        $user->money = $user_money;
        $user->save();
        


        return $this->storeInmarktingFess($id, $user->id, $user_new_money, $status, $profit, $Generations,$bot_num);
    }
}
