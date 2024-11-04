<?php

namespace App\Http\Controllers\Front;

use App\Mail\OTPWithdraw;
use Illuminate\Http\Request;
use App\Models\transfer_many;
use App\Models\profit_blanace_user;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Storetransfer_manyRequest;
use App\Traits\ResponseJson;
use App\Http\Controllers\Helper\NotficationController;
use App\Http\Controllers\Helper\OTP\OTPWithrawController;
use Illuminate\Support\Str;
use App\Models\transactionUser;
use App\Models\profit_blance_user_afflite;
use App\Http\Requests\transfer\amountRequest;









class BufferController extends Controller
{
    use ResponseJson;

    public function withdrawFromMax(Storetransfer_manyRequest $request)
    {
      $user = auth('api')->user();
        if(!$user)
        {
            return $this->token();
        }
        
      
        $money = $request['money'];
        $Visa_number = $request['Visa_number'];
        $transactionId =$this->TransactionId();
         $user_ip = $request->ip();



          // check max or afflite
        if(!$request['type'] == "afflite_max")
        {
            $type="Max";
           return $this->max($user, $money, $Visa_number, $transactionId, $user_ip,$type);
        }
        else
        {
            $type="afflite_max";
           return $this->max_afflite($user, $money, $Visa_number, $transactionId, $user_ip,$type);
        }
    }

    public function max($user, $money, $Visa_number, $transactionId, $user_ip,$type)
    {

         $balanceMax = profit_blanace_user::where('user_id', $user->id)->latest()->first();
        if (!$balanceMax  && $money <= 0) {
            $data = "No balance available or not subscribed";
            return $this->error($data);
        }

        if ($money > $balanceMax->blance) {

            return $this->error("You don't have enough balance");
        }

        // Subtract money from balance
        $balanceMax->blance -= $money;
        $balanceMax->save();

        // Store withdrawal transaction
        $this->storeTransferMany($money, $Visa_number, $transactionId, $user->id, $user_ip,$type);
        $this->notficationAndOtp($user,$money,$transactionId);

        return $this->success("Withdrawal processed successfully");
    }

    public function max_afflite($user, $money, $Visa_number, $transactionId, $user_ip,$type)
    {

         $balanceMax = profit_blance_user_afflite::where('user_id', $user->id)->latest()->first();
        if (!$balanceMax && $money <= 0) {
            $data = "No balance available or not subscribed";
            return $this->error($data);
        }

        if ($money > $balanceMax->balance) {

            return $this->error("You don't have enough balance");
        }

        // Subtract money from balance
        $balanceMax->balance -= $money;
        $balanceMax->save();

        // Store withdrawal transaction
        $this->storeTransferMany($money, $Visa_number, $transactionId, $user->id, $user_ip,$type);
        $this->notficationAndOtp($user,$money,$transactionId);

        return $this->success("Withdrawal processed successfully");
    }

    // ////////////////helper /////////////////////////////
    public function storeTransferMany($money, $Visa_number, $transactionId, $user_id, $user_ip,$type)
    {
        $transferMany = transfer_many::create([
            'money' => $money,
            'Visa_number' => $Visa_number,
            'status' => 'pending',
            'transaction_id' => $transactionId,
            'user_id' => $user_id,
            'ip_user' => $user_ip,
            'otp' => 000,
            'check_otp' => 0,
            'type' => $type,
        ]);
    }


    public function TransactionId()
    {
        $timestamp = time(); // Get the current timestamp
        $uniqueId = uniqid(); // Generate a unique identifier
        $randomNumber = mt_rand(1000, 9999); // Generate a random number

        $transactionId = $timestamp . $uniqueId . $randomNumber;

        return $transactionId;
    }

    public function notficationAndOtp($user,$money,$transactionId)
    {
        $otpWithdraw=new OTPWithrawController();
        $notfication = new NotficationController();
        $bodyManger = "تم طلب سحب " . $money . " من " . $user->name;
        $notfication->notficationManger($bodyManger);
        // $body="تم طلب السحب بنجاح وجاري الموافقه";
        $body="تمت الموافقه وجاري السحب بنجاح";
        $notfication->notfication($user->fcm_token,$body);


        return   $otpWithdraw->sendotpWithdraw($user->email,$transactionId);
    }


public function transfertofess(amountRequest $request)
{
    // Get the authenticated user
    $user = auth('api')->user();
    $amount = (int)$request->input('amount'); // Make sure it's an integer



     $blance_max=profit_blanace_user::where('user_id',$user->id)->latest()->first();
    if (!$blance_max || $user->plan_id == 1) {
      $data = "No balance or not subscribed";
      return $this->errorDtat($data);
    }
      if ($request['amount'] > $blance_max->blance) {
          return $this->errorDtat("You dont have all that money");

      }else{
        $check = $blance_max->blance -= $request['amount'];
        $blance_max->blance = $check;
        $blance_max->save();
        if($request['type'] == "fees")
        {
            $user->money += $amount;
            $user->save();
            $this->Store($user->id, $user->id, $user->name = "Me", $amount);
            $massageSend = "تم تحويل المبلغ الي محفظتك بنجاح";
            // $result = $this->notificationController->notfication($user->fcm_token, $massageSend);

            return $this->successDtat("true");
        }else{
            $user->number_points += $amount;
            $user->save();
            $this->Store($user->id, $user->id, $user->name = "Me", $amount);
            $massageSend = "تم تحويل المبلغ الي محفظتك بنجاح";
            // $result = $this->notificationController->notfication($user->fcm_token, $massageSend);

            return $this->successDtat("true");
        }


      }

      }


        public function Store($userId, $reciveId, $name, $amount)
        {
        
            $randomString = Str::random(20);
            $randomNumber = mt_rand(1000, 9999);
            $uniqueCode = $randomString . $randomNumber;
        
            $transactionUser = transactionUser::create([
                'user_id' => $userId,
                'recive_id' => $reciveId,
                'amount' => $amount,
                'transaction_id' => $uniqueCode,
        
            ]);
        }
}


