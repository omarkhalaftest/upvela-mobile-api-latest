<?php

namespace App\Http\Controllers\Boot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\ActiveBotRequest;
use App\Http\Controllers\Helper\BotTelgremController;


class ActiveUserBotController extends Controller
{
    public function ActiveBot(ActiveBotRequest $request)
    {

        $user = auth('api')->user();


        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Invalid token",
            ]);
        } else {
            $user->is_bot = 1;
            $user->num_orders = $request['numOrders'];
            $user->orders_usdt = $request['ordersUsdt'];
            $user->tickers = $request['tickers'];
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => $user,
        ]);
    }

    public function stopBot(Request $request)
    {

        $user = auth('api')->user();
        
        $user->is_bot=0;
        $user->save();

        $data = [
            'shutdown' => 1,
            "userid" => $user->id,

        ];
        
            return response()->json([
                        'success' => true,
                        'message' => $user,
                    ]);
        
        

        $response = Http::post('http://51.161.128.30:5015/shutdown', $data);
       

       $responseBody = $response->body();
                   $responseData = json_decode($responseBody);
                if ($responseData && isset($responseData->success) && $responseData->success === true) {
                    // The "success" field is present and is true
    ;
                   
                    return response()->json([
                        'success' => true,
                        'message' => $user,
                    ]);
                } else {
                            // The "false" field is present and is true
                      $telgrem=new BotTelgremController();
                      $text=" shutdown لدينا مشكله ف عمليه ";
                      $telgrem->ramyboterror($text);
                    return response()->json([
                        'success' => false,
                        'message' => $user,
                    ]);
                }
        // return 555;
    }
}
