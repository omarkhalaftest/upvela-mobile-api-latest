<?php

namespace App\Http\Controllers\sasa;

use Illuminate\Http\Request;
use App\Mail\ProblemReport;
use App\Http\Requests\SASArequest;
use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Helper\BotTelgremController;



class sasaController extends Controller
{
    use ResponseJson;
    public function index(SASArequest $request)
    {
                    $telgrem=new BotTelgremController();

        if($request['type'] != "upvela")
        {
                try {

            // $email_sasa = "nesrenfayeed@smartsolution-ar.com";
            $username = $request->input('name');
            $email = $request->input('email');
            $problem = $request->input('nationality');
            $phone = $request->input('phone');
            
            $text = "Name: $username\nEmail: $email\nPhone: $phone\ncountry: $problem";
            $telgrem->Sasa($text);
             return redirect('https://sasarealestate.com/');
             } catch (\Exception $e) {
                    $telgrem->Sasa($e);
                         return redirect('https://sasarealestate.com/');
                }

           
              
            // // Send the email
            //  Mail::to($email_sasa)->send(new ProblemReport($username, $email, $problem,$phone));
            //  $email_sasa="ahmedsamir11711@gmail.com";
            //  Mail::to($email_sasa)->send(new ProblemReport($username, $email, $problem,$phone));
            //  return redirect('https://contactform.sasarealestate.com/');

        }else{
            try{
            
            $username = $request->input('name');
            $email = $request->input('email');
            $problem = $request->input('nationality');
            $phone = $request->input('phone');
            $type="Bot Upvela";
            
               $text = "Name: $username\nEmail: $email\nPhone: $phone\ncountry: $problem\ntype: $type";
            $telgrem->Sasa($text);
return redirect('https://add.upvela.net/');
           
           
           
        } catch (\Exception $e) {
            
            $telgrem->Sasa($e);
        return redirect('https://add.upvela.net/');
        // return response()->json(['error' => 'An error occurred while processing your request'], 500);
    }
              
            // // Send the email
            //  Mail::to($email_sasa)->send(new ProblemReport($username, $email, $problem,$phone));
            //  $email_sasa="ahmedsamir11711@gmail.com";
            //  Mail::to($email_sasa)->send(new ProblemReport($username, $email, $problem,$phone));
            //  return redirect('https://add.upvela.net/');

        }
           

    }
}

