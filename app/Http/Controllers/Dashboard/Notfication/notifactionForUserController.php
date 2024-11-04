<?php

namespace App\Http\Controllers\Dashboard\Notfication;

use App\Models\User;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\NotficationController;

class notifactionForUserController extends Controller
{
    use ResponseJson;
    public function sendNotf(Request $request)
    {

        $userId=$request->user_id;
         $user=User::find($userId);
        $body=$request->body;

        $notfi=new NotficationController();
        $notfi->notfication($user->fcm_token,$body);

        return $this->success("Done") ;



    }

    public function store()
    {
        $users=User::get();
        foreach ($users as $user) {


        }
    }
}
