<?php

namespace App\Http\Controllers\BootCaptian;

use App\Models\User;
use App\Models\Admin;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TestAdminBot;

class BotRealtedWithExportBptController extends Controller
{
    use ResponseJson;
        public function getCaptian(Request $request)
        {
            //       'id'=>$this->id,
            // 'bot_id'=>$this->bot_id,
            // 'botName'=>$this->name,
            // 'img'=>$this->image_profile,
            
            
            
            $adminone = User::select('id','id as bot_id', 'name as botName')->where('id', '1485')->get();
            $admintwo = User::select('id','id as bot_id', 'name as botName')->where('id', '1493')->get();

            $adminone->each(function ($adminone) {

           $adminone->bot_id=1485;

            });

            $admintwo->each(function ($admintwo) {

                $admintwo->bot_id=1493;

                 });


            $mergedUsers = $adminone->merge($admintwo);

            $text="Lorem ipsum dolor sit amet consectetur adipisicing elit. Praesentium pariatur id assumenda ad porro nam, eligendi unde corrupti tempora aliquam, cum excepturi ullam quae quis. Neque odio corrupti dolores minima.";
            
             return $mergedUsers;

   return TestAdminBot::collection($mergedUsers);

            





        }














    public function index(Request $request)
    {
        $user=auth('api')->user();
        if(!$user)
        {
          return $this->error('Not Found The Token');
        }

        $activeExportBot=$user->is_bot;
        if($activeExportBot == 0)
        {
          $user->is_bot=1;
          $user->save();
        }
        return $user;
    }
}
