<?php

namespace App\Http\Controllers\Helper\OTP;

use App\Models\User;
use App\Mail\OTPregister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\UserNameResource;
use App\Models\DepositsBinance;
use App\Traits\ResponseJson;
use App\Http\Requests\Helper\passwordRequest;
use App\Http\Requests\Helper\otpRequest;
use App\Http\Requests\Helper\emailRequest;
use App\Http\Requests\Helper\changepassworddRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Helper\OTP\providerOTPController;







class OtpRegisterController extends Controller
{
     use    ResponseJson;
    public function otpRegister($email)
    {
        $user=User::where('email',$email)->first();
        $otpDataaa=rand(1000,9999);
        $user->otp=$otpDataaa;
        $user->save();

    $sendotp = new providerOTPController();
    $sendotp->sendOtp($user->email, $user->name, $otpDataaa);
        //  Mail::to($email)->send(new OTPregister($otpDataaa,$user->name)); // Replace with your own mail class

        //  return $this->success("A code has been sent to your email");
    }

    public function checkOtoRegister(otpRequest $request)
    {
         

       $otp=$request['otp'];

       $userotp=User::where('email',$request->email)->first();
       if($userotp)
       {
           if($otp == $userotp->otp)
           {
            $userotp->email_verified_at = \Carbon\Carbon::now();
            $userotp->otp=null;
            $userotp->save();
            return $this->success("Otp successful! Please login");
           }else{
            return $this->error("The Otp is incorrect");
           }
       }else{
        return $this->error("NOT HAVE TOKEN");

       }
    }
    
     public function index()
    {
        ini_set('max_execution_time', 300);

        $users = User::where('plan_id', 1)->get();

        $users->each(function($user) {
            $deposite = DepositsBinance::where("user_id", $user->id)->where('amount', '>=', 50)->first();
            if(!$deposite) {
                $user->active = 0;
            } else {
                $user->active = 1;
            }
        });

        return UserNameResource::collection($users);

    }

    public function genrateOtp()
    {
          return $otpDataaa=rand(1000,9999);

    }
    
    // for change password
      public function changepassword(changepassworddRequest $request)
     {
         return $this->error($this->randmError());
          $user=User::where('email',$request->email)->first();
       
        
        if($user->otp == $request['otp'])
        {
             
            $user->otp=null;
            $user->password=Hash::make($request['password']);
            $user->save();
            return $this->success("change password successful! Please login");
        }else{
                        return $this->error("NOT Chanage password");

        }


     }
     // for check Otp ResatPassword
       public function checkOtpResatPassword(Request $request)
     {
        
         return $this->error($this->randmError());
        $otp=$request['otp'];

       $userotp=User::where('email',$request->email)->first();
       if($userotp)
       {
           if($otp == $userotp->otp)
           {
            $userotp->email_verified_at = \Carbon\Carbon::now();
            $userotp->save();
            return $this->success("Otp successful! Please change password");
           }else{
            return $this->error("The Otp is incorrect");
           }
       }else{
        return $this->error("NOT HAVE TOKEN");

       }
     }
     
     // for rest password
      public function resetemail(emailRequest $request)
     {
         
        $email=$request['email'];
        $this->otpRegister($email);
        return $this->success("A code has been sent to your email");

     }
     public function randmError()
     {
          $sentences = [
        "Invalid OTP, please try again.",
        "Your OTP has expired, request a new one.",
        "OTP verification not successful.",
        "Incorrect OTP entered, please check and retry.",
        "OTP not found, please check the request."
    ];

    // Get a random index and return the sentence
    return $sentences[array_rand($sentences)];
     }



}
