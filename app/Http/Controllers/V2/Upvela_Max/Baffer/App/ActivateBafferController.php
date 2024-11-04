<?php

namespace App\Http\Controllers\V2\Upvela_Max\Baffer\App;

use Carbon\Carbon;
use App\Models\User;
use App\Models\buffer;
use App\Models\buffer_user;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\NotficationController;
use App\Http\Requests\Upvela_Max\buffer\OneBufferRequest;
use App\Http\Requests\Upvela_Max\buffer\ActivateBufferRequest;
use App\Models\UsersGenerationRelation;
use App\Models\buffer_plan;
use Illuminate\Support\Str;


class ActivateBafferController extends Controller
{
    use ResponseJson;
    public function activate(ActivateBufferRequest $request)
    {
               $user=auth('api')->user();
            if(!$user)
            {
            return $this->token();
            }


        $buffer_id=$request->buffer_id;
         $get_blance_buffer=buffer::where('id',$buffer_id)->first();
         
        if($user->number_points >=$get_blance_buffer->amount)
        {

            // defination $request
            $request['user_id']=$user->id;
            $request['buffer_id']=$buffer_id;
            $request['amount']=$get_blance_buffer->amount;
            $request['plan_id']=$user->plan_id;
            $request['per_month'] = $request->per_month ?? 3;


                $insert=$this->insert_buffer_user($request);

 


             if ($insert['success'] == true) {

                $this->discount($get_blance_buffer->amount,$user->id);
                $this->UpdateFreeCheck($user->id);
                $this->notfication($user->fcm_token,$get_blance_buffer->name,$request['amount']);
                

                return $this->success("You have successfully subscribed");
             }else
             {
                return $this->error("You are already subscribed");
             }
            }else{
                return $this->error("Not Have Money");
            }

    }



    public function discount($amount,$user_id)
    {
            $user=User::where('id',$user_id)->first();

            $user->number_points -=$amount;
            $user->save();

    }


    public function insert_buffer_user(Request $request)
    {
               $date = Carbon::now();
        $per_month = $request['per_month'];
        $formattedDate = $date->format('Y-m-d H:i'); // Format the current date
        $futureDate = $date->addMonths($per_month); // Add per month months to the current date
        $formattedFutureDate = $futureDate->format('Y-m-d H:i');



            $user_buffer=buffer_user::create([
                'user_id'=>$request['user_id'],
                'buffer_id'=>$request['buffer_id'],
                'start_subscrip'=>$formattedDate,
                'end_subscrip'=>$formattedFutureDate,
                'amount'=>$request['amount'],
                'per_month' => $per_month,

                'active'=>1,
                'plan_id'=>$request['plan_id'],
                'HashID' => $this->HashID(),
            ]);

                return [
                    'success' => true,
                ];


 }
 
   public function HashID()
    {
        $transactionId = rand(1000000, 9999999); // Generates a random number between 1000000 and 9999999

        // Concatenate transaction ID, additional data, and a random string
        return $dataToHash = $transactionId  . Str::random(10);
    }

    public function notfication($fcm_token,$number_buffer,$mony_buufer)
    {
           $notfictions=new NotficationController();
        $body="تم الاشتراك بنجاج ف الخزان الخاص بك بمبلع $mony_buufer";
        $notfictions->notfication($fcm_token,$body);
        $massageForMnager="تم اشتراك شخص جديد ف خزان رقم $number_buffer بمبلغ قدره $mony_buufer";

        $notfictions->notficationManger($massageForMnager);

    }
     public function UpdateFreeCheck($user_id)
    {
        $user_gens = UsersGenerationRelation::where('user_id_child', $user_id)->get();
        if (!$user_gens) {
            return "false";
        } else {
            foreach ($user_gens as $user_gen) {
                $user_gen->free_check = 1;
                $user_gen->save();
            }
        }
    }
}
