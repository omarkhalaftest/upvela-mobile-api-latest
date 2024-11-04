<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax;

use Carbon\Carbon;
use App\Models\buffer;
use App\Models\buffer_user;
use App\Models\buffers_days;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 use App\Models\precentage_puffers;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use App\Http\Requests\Upvela_Max\DiviteProfit_Max\diviteForPlanRequest;


class bufer_precantageController extends Controller
{
    use ResponseJson;
    public function storeDivate(Request $request)
    {
        
        
        $bufferData = [
            [
                'buffer_id' => $request['buffer_id1'],
                'precentage' => $request['pre_id1']
            ],
            [
                'buffer_id' => $request['buffer_id2'],
                'precentage' => $request['pre_id2']
            ],
            [
                'buffer_id' => $request['buffer_id3'],
                'precentage' => $request['pre_id3']
            ]
        ];

        $divites = [];

        foreach ($bufferData as $buffer) {
            $totleMony = $request['totle_mony'];
            $precentage = $buffer['precentage'];
            $plan_id = $buffer['buffer_id'];

            $precentage_decimal = $precentage / 100; //35% =0.35
            $totlemony = $totleMony * $precentage_decimal;

            $this->SETActive($plan_id);

            $divite = precentage_puffers::create([
                'totle_all_mony' => $totleMony,
                'plan_id' => $plan_id,
                'precentage' => $precentage,
                'totle_mony_afert_divite' => $totlemony,
                'is_active' => 1
            ]);

            $divites[] = $divite;
        }

       return $this->success($divites);




    }
    public function SETActive($plan_id)
    {
        // Define the data to update
        $dataToUpdate = ['is_active' => 0];

        // Update all rows in the precentage_puffers table to set is_active to 1
        precentage_puffers::query()->where('plan_id',$plan_id)->update($dataToUpdate);
    }

    // i got totle profit
    // and have totle buffers
    // to get hom mutch for every buffers


   public function index(diviteForPlanRequest $request)
   {
       $today = Carbon::now()->format('Y-m-d');


     
      $profit=$request['money'];
      $bufferone=buffer_user::where('buffer_id',1)->where('active',1)->whereDate('start_subscrip', '!=', $today)->sum('amount');
      $buffertwo=buffer_user::where('buffer_id',2)->where('active',1)->whereDate('start_subscrip', '!=', $today)->sum('amount');
      $bufferthree=buffer_user::where('buffer_id',3)->where('active',1)->whereDate('start_subscrip', '!=', $today)->sum('amount');
       $totleMonyforbuffer=buffer_user::where('active',1)->whereDate('start_subscrip', '!=', $today)->sum('amount');
      
      
      // to get precentage for buffers

       $precentagebuffer_one = number_format(round($bufferone / $totleMonyforbuffer * 100, 2), 2);
       $precentagebuffer_two = number_format(round($buffertwo / $totleMonyforbuffer * 100, 2), 2);
       $precentagebuffer_three = number_format(round($bufferthree / $totleMonyforbuffer * 100, 2), 2);

       



          // Call storeDivate function to divite on profit
     return   $this->storeDivate(request()->merge([
        'buffer_id1' => 1,
        'pre_id1' => $precentagebuffer_one,
        'buffer_id2' => 2,
        'pre_id2' => $precentagebuffer_two,
        'buffer_id3' => 3,
        'pre_id3' => $precentagebuffer_three,
        'totle_mony' => $profit // assuming this is what you want to pass as totle_mony
    ]));
         
   }



}
