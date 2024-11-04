<?php

namespace App\Http\Controllers\Affliate;

use App\Models\plan;
use App\Models\User;
use App\Models\Rank;
use Illuminate\Http\Request;
use App\Models\UserRanksRelation;
use App\Http\Controllers\Controller;
use App\Models\UsersGenerationRelation;

class AffliateAppController extends Controller
{
    public function afflite(Request $request)
    {
        $userToken=auth('api')->user();
         $user=$request['user_id'];
        $gen=$request['generation_number'];

        if($user)
        {
           if($request['user_id'] == $userToken->id)
           {  $gen=1;
              return $this->genration($gen,$userToken);
           }else{
             return $this->diffrentuser($user,$userToken);
           }
        }else{

            return $this->genration($gen,$userToken);

        }

    }


      public function genration($gen,$userToken)
 {
             $generationNumber = $gen;
        $getAllChildGenerationIds = UsersGenerationRelation::where('user_id_father', $userToken->id)
            ->where('generation_id', $generationNumber)
            ->pluck('user_id_child')
            ->toArray();

  $users = User::select('id', 'name', 'plan_id')->with([
            'plan:id,name', // تحديد الحقول المطلوبة فقط من علاقة plan
            'historyAllProfit'
        ])->whereIn('id', $getAllChildGenerationIds)->get();
        
        
        $plan = Plan::select('percentage1', 'percentage2', 'percentage3')->find(6);

        // تحميل بيانات UserGenerationRelation لجميع المستخدمين دفعة واحدة
        $userGenerationRelations = UsersGenerationRelation::whereIn('user_id_child', $getAllChildGenerationIds)
            ->where('user_id_father', $userToken->id)
            ->get()
            ->keyBy('user_id_child');

        $users->each(function ($user) use ($plan, $userToken, $generationNumber, $userGenerationRelations) {
            $totlefess = $user->historyAllProfit->where('Generations', $generationNumber)->sum('profit_users');
            $user->totleMony = number_format($user->historyAllProfit->sum('amount'), 2);

            // الحصول على UserGenerationRelation مسبقاً من المجموعة المحملة مسبقاً
            $userGenration = $userGenerationRelations->get($user->id);
            $user_free_check = $userGenration->free_check ?? 0;

            if ($user_free_check == 0) {
                $user->Active = 0; // هذا المستخدم غير نشط
            } else {
                $user->Active = 1;
            }
        });


        // $userRank = UserRanksRelation::where('user_id', $user->id)->first();
        // $user->rank = $userRank->rank_id;

        // Return the modified collection
        return response()->json([
            "success" => true,
            "data" => $users
        ]);
}



public function diffrentuser($user_id,$userToken)
{

        $user = User::where('id', $user_id)->first(); // click in  user
        $affiliate_code = $user->affiliate_code;
    
               $users = User::with([
            'plan:id,name', // تحديد الحقول المطلوبة فقط من علاقة plan
            'historyAllProfit' => function ($query) use ($user) {
            $query->where('markting_id', $user->id);
            }
        ])->where('comming_afflite', $affiliate_code)->get();
        // for
    
       $plan = Plan::select('percentage1','percentage2', 'percentage3')->find(6);
      $userGenration = UsersGenerationRelation::where('user_id_child', $user->id)->where('user_id_father', $userToken->id)->first();
    
       // for loop users
            $users->each(function ($user) use ($plan, $userToken, $userGenration) {
    
    
       $totlefess= $user->historyAllProfit->where('Generations', 1)->sum('profit_users');
       $user->totleMony=number_format($user->historyAllProfit->sum('amount'), 4);
    
       // check this user active or not
    
        $user_free_check=$userGenration->free_check;
    
       if(!$userGenration && $userGenration->free_check == 1)
       {
           $user->Active=1; // this user is not active
       }else{
           $user->Active=0;
       }
       // end user rnak active
         
    
       $g1 = +$plan->percentage1 / 100 * $totlefess;
       $g2 = +$plan->percentage2 / 100 * $totlefess;
       $g3 = +$plan->percentage3 / 100 * $totlefess;
    
       $user->G1 = number_format($g1,4);
       $user->G2 =number_format($g2,4);
       $user->G3 = number_format($g3,4);
       });
    
       // for get rank
         
    
       // Return the modified collection
       return response()->json([
           "success" => true,
           "status"=>200,
           "data" => $users,
           "statusCode"=>200,
       ]);
}





 public function Myranke_with_genration(Request $request)
 {
    $user = auth('api')->user();

 
$user_rank = UserRanksRelation::where('user_id', $user->id)->first();

$mergedData = [
    'rank' => Rank::all(), 
    'user_rank' => $user_rank,
];

return response()->json(
    [
        'success' => true,
        'message' => $mergedData,
        'status' => 200,
    ]
);

 }














        }







