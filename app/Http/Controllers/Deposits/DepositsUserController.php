<?php

namespace App\Http\Controllers\Deposits;

use Illuminate\Http\Request;
use App\Models\DepositsBinance;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Deposits\DepositsController;
use App\Http\Controllers\Helper\NotficationController;

class DepositsUserController extends Controller
{
    public function cheakTextID(Request $request)
    {

         
            $user = auth('api')->user();

               $textid = $request['textid'];

            // Check if $textid includes "Internal transfer"
            if (strpos($textid, 'Off-chain transfer') !== false) {
                // Remove "Internal transfer" from $textid
                      $textid2 = str_replace('Off-chain transfer', '', $textid);

            // Trim any spaces from $textid2
             $textid2 = trim($textid2);
            } else {
                // If "Internal transfer" is not present, keep the original value of $textid
                $textid2 = $request['textid'];
            }
            
            // return $textid2;


           $existingDeposit = DepositsBinance::where('textId', $textid2)->where('status', '1')->first();
        if ($existingDeposit) {
            return response()->json([
                'success' => false,
                "message" => "The Text ID found or wrong",
            ]);
        } else {

            $binanceDeopsite = new DepositsController();
              $binanceDeopsite->getDeposits($user->id);

            $existingDeposit = DepositsBinance::where('textId', $textid2)->first();
            if (!$existingDeposit) {
                return response()->json([
                    'success' => false,
                    "message" => "The deposit has not been made to Binance, please check this",
                ]);
            } else {  //found it

                $existingDeposit->status = "1";
                $existingDeposit->user_id = $user->id;
                $existingDeposit->save();
// ;
                // Update the user's balance
                $totelMony=$existingDeposit->amount += 0.1;
                $user->number_points += $totelMony;
                $user->save();


                // for Notfication
                $notfication = new NotficationController();
                $body = "تم الايداع في محفظتك مبلغ $existingDeposit->amount وأصبح اجمالي الرصيد $$user->number_points";
                $notfication->notfication($user->fcm_token, $body);
                $bodyManger = "تم إيداع مبلغ $$existingDeposit->amount في محفظتك من قبل $user->name   ";
                $notfication->notficationManger($bodyManger);
                $notfication->Myahya($bodyManger);
            }
        }

        return response()->json([
            'success' => true,
            "amount" => $existingDeposit->amount,
            "message" =>
            "operation accomplished successfully"
        ]);

        return response()->json([
            'success' => true,
            "amount" => $existingDeposit->amount,
            "message" =>
            "operation accomplished successfully"
        ]);
    }


    public function historyDeposit(Request $request)
    {
        $user = auth('api')->user();

        return $existingDeposit = DepositsBinance::where('user_id', $user->id)->get();
    }
    
        public function historyDepositWeb(Request $request)
    {
        $user_id = $request->id;
        return  DepositsBinance::where('user_id', $user_id)->get();
    }
    
}
