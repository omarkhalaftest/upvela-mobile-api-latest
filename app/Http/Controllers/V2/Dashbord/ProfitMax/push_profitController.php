<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax;

use App\Http\Controllers\Controller;
use App\Models\profit_buffer_to_user;
use App\Models\profit_blanace_user;
use Illuminate\Http\Request;
use App\Traits\ResponseJson;


class push_profitController extends Controller
{
    use ResponseJson;

    public function push()
    {
                set_time_limit(120);

        // Fetch all unique user_ids
         $userIds = profit_buffer_to_user::where('active', 1)->distinct()->pluck('user_id');

        // Iterate over each user_id
        $userIds->each(function ($userId) {
             
            $totalProfit = profit_buffer_to_user::where('active', 1)
                ->where('user_id', $userId)
                ->sum('mony_for_15day');

            $usermodel=User::where('id',$userId)->first();
            Http::timeout(120)->post('https://upvela.gfoura.smartidea.tech/api/subscription_max', [
                'user_id' => $usermodel->id,
                'coming_affiliate' => $usermodel->comming_afflite,
                'bot_id'=>1,
                'bot_money'=>$totalProfit
                ]);
            $this->profit($userId, $totalProfit);

        });
        $this->updatedActive();




            // Optionally, return something indicating the process is completed
    return $this->success('Total profits stored for all users');

}

public function profit($userId, $balance)
{
    $checkfound = profit_blanace_user::where('user_id', $userId)->first();

    if ($checkfound) {
        // If the record exists, update the balance
         $checkfound->blance += $balance;
        $checkfound->save();
    } else {
        // If the record doesn't exist, create a new one
        profit_blanace_user::create([
            'user_id' => $userId,
            'blance' => $balance,
        ]);
    }
}

   public function updatedActive()
    {
            $userIds = profit_buffer_to_user::where('active', 1)->update(['active' => 2]);

   }

    }

