<?php

namespace App\Http\Controllers;

use DateTime;

use DateTimeZone;
use App\Models\plan;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\ActivePending;
use Illuminate\Support\Facades\Date;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\UserPlanResource;
use Illuminate\Support\Facades\Http;
 
use App\Models\marktingFess;
 
use App\Http\Controllers\Helper\NotficationController;
use App\Http\Controllers\TransactionUser\TransactionUserController;

class PayController extends Controller
{
    public function pending()
    {

        $payment = Payment::where('status', 'pending')->with(['plan', 'user'])->orderBy('id', 'desc')->get();
        return PaymentResource::collection($payment);

        return UserPlanResource::collection(User::where('Status_Plan', 'pending')->with(['plan', 'imgPay' => function ($query) {
            $query->orderBy('id', 'desc')->first();
        }])->get());
    }


    public function returnFree(Request $request)
    {
        $user = auth('api')->user();
        $user->plan_id = 1;
        $user->save();
    }




    public function ActivePending($transactionId, $planId)
    {



        // $transactionId = $request->transaction_id;
        $startPlan = gmdate('Y-m-d');
        $endPlan = Carbon::now()->addDays(30)->format('Y-m-d');

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return false;
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Transaction ID not found',
            // ]);
        }

        $payment->status = 'success';
        $payment->save();

        $user = User::find($payment->user_id);

        if (!$user) {
            return false;
            // return response()->json([
            //     'success' => false,
            //     'message' => 'User not found',
            // ]);
        }

        $user->update([
            'plan_id' => $planId,
            'start_plan' => $startPlan,
            'end_plan' => $endPlan,
            'Status_Plan' => 'paid',
        ]);

        // $this->afterPay($user->id);
        return true;
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Request is successful',
        // ]);
    }
    // function afterPay($id)
    // {
    //      $user_comming = User::find($id);
    //      $plan_idSaved=$user_comming->plan_id;
    //     $user_comming_affiliate = $user_comming->comming_afflite;
    //     if ($user_comming->plan->title != 'free') {
    //         $user_plan_price = $user_comming->plan->price;
    //               $user_master3 = User::where('affiliate_code', $user_comming_affiliate)->first();
    //         if (!empty($user_master3)) {
    //             $user_comming_affiliate3 = $user_master3->comming_afflite;
    //             $perc_paln = +$user_master3->plan->percentage1;
    //             $this->affililateProccess($user_master3, $user_plan_price, $perc_paln,$id,$status="PLAN",$user_plan_price,$Generations=1,$plan_idSaved);
    //                 $user_master2 = User::where('affiliate_code', $user_comming_affiliate3)->first();
    //             if (!empty($user_master2)) {
    //                 $user_comming_affiliate2 = $user_master2->comming_afflite;
    //                 $check_his_plan = +$user_master2->plan->percentage2;
    //                 if ($check_his_plan != null) {
    //                       $this->affililateProccess($user_master2, $user_plan_price, $check_his_plan,$id,$status="PLAN",$user_plan_price,$Generations=2,$plan_idSaved);
    //                 } else {
    //                       $this->affililateProccess($user_master2, $user_plan_price, $check_his_plan = 0,$id,$status="PLAN",$user_plan_price,$Generations=2,$plan_idSaved);
    //                 }

    //                      $user_master1 = User::where('affiliate_code', $user_comming_affiliate2)->first();
    //                 if (!empty($user_master1)) {
    //                     $check_his_plan = +$user_master1->plan->percentage3;
    //                     if ($check_his_plan != null) {
    //                             $this->affililateProccess($user_master1, $user_plan_price, $check_his_plan,$id,$status="PLAN",$user_plan_price,$Generations=3,$plan_idSaved);
    //                     } else {
    //                             $this->affililateProccess($user_master1, $user_plan_price, $check_his_plan,$id,$status="PLAN",$user_plan_price,$Generations=3,$plan_idSaved);
    //                     }
    //                 } else {
    //                     return "Not Assign to Father yet";
    //                 }
    //             } else {
    //                 return "Not Assign to Father yet";
    //             }
    //         } else {
    //             return "Not Assign to Father yet";
    //         }
    //     } else {
    //         return "Not Assign to Paln yet";
    //     }
    // }

    // function affililateProccess($user, $price, $perc, $id, $status, $profit, $Generations,$plan_id)
    // {



    //     $user_old_money = $user->money;
    //     $user_new_money = ($perc / 100) * $price;
    //     $user_money = $user_old_money + $user_new_money;
    //     $user->money = $user_money;
    //     $user->save();


    //      $this->storeInmarktingFess($id, $user->id, $user_new_money, $status, $profit, $Generations,$plan_id);
    // }
    //  public function storeInmarktingFess($user_id, $markting_id, $user_new_money, $status, $profit, $Generations,$plan_id)
    // {
    //     $plan=plan::select('name')->find($plan_id);

    //     $insert = marktingFess::create([
    //         'user_id' => $user_id,
    //         'markting_id' => $markting_id,
    //         'amount' => $user_new_money,
    //         'status' => $status,
    //         'profit_users' => $profit,
    //         'Generations' => $Generations,
    //         'plan_id'=>$plan->name,

    //     ]);
    // }
}
