<?php

namespace App\Http\Controllers\Helper\OTP;

use App\Models\User;
use App\Mail\OTPWithdraw;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Models\transfer_many;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Withdarw\otprequest;


class OTPWithrawController extends Controller
{
    use ResponseJson;




public function sendotpWithdraw($email,$transactionId)
{

    $user = User::where('email', $email)->first();
    $userotp = transfer_many::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('transaction_id', $transactionId)
        ->latest()
        ->first();

    if ($userotp) {
        $otpData = rand(1000, 9999);
        $userotp->otp = $otpData;
        $userotp->save();
    }else{
        return $this->error('YOU HAVE error');
    }


  
         $name=$user->name;
        Mail::to($email)->send(new OTPWithdraw($otpData,$name));

         return $this->successDtat("A code has been sent to your email");

}





    public function chechOtoWithdraw(otprequest $request)
    {

   
        $otp=$request['otp'];
       $user=auth('api')->user();
            if(!$user)
            {
            return $this->token();
            }

         $userotp=transfer_many::where('user_id',$user->id)->where('otp',$otp)->first();
       if($userotp)
       {
           if($otp == $userotp->otp)
           {
            $userotp->check_otp = 1;
            $userotp->otp=null;
            $userotp->save();
            return $this->success("Otp successful!");
           }else{
            return $this->error("The Otp is incorrect");
           }
       }else{
        return $this->error("The Otp is incorrect");

       }
}

        public function screen_withdraw()
        { 
            $user=auth('api')->user();
            if(!$user)
            {
                return $this->token();
            }
             $userotp = transfer_many::where('user_id', $user->id)
            ->where('status', 'pending')->latest()->first();
            if(!$userotp)
            {
                $check=1;
            }else{
                $check=1;
            }
            
          return $this->successDtat($check);
        }
        
 public function resetfrowithdraw()
    {
        $user=auth('api')->user();
        if(!$user)
        {
           return $this->token();
        }

        $userotp = transfer_many::where('user_id', $user->id)
        ->where('status', 'pending')
        ->latest()
        ->first();

    if ($userotp) {

        $otpDataaa = rand(1000, 9999);
        $userotp->otp = $otpDataaa;
        $userotp->save();
        Mail::to($user->email)->send(new OTPWithdraw($otpDataaa,$user->name));
                 return $this->successDtat("A code has been sent to your email");


    }else{
        return $this->error('YOU HAVE error');
    }

    }
}
