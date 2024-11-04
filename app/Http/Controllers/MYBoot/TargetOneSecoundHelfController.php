<?php

namespace App\Http\Controllers\MYBoot;

use Illuminate\Http\Request;
use App\Models\recommendation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Helper\BotTelgremController;
use App\Http\Controllers\Helper\getpricecurencyController;
use App\Http\Controllers\limitBinance\buyController;


class TargetOneSecoundHelfController extends Controller
{
    public function index(Request $request)
    {
 
 



        $telgrem=new BotTelgremController(); // for telgrem

        //  $telgrem->upvaleFreeGroupe("test");




        $data = $request->input();
        $parsedResponse = json_decode(json_encode($data), true);

        $action = $parsedResponse[0]['action']; // buy or sell
        $ticker = $parsedResponse[1]['ticker']; //name of curency
        $currency = $ticker;
        // $bot_number=$parsedResponse[2]['bot_number'];
        // all data




        if ($action == "buy") {
             // call function to get price cuurency

            $getpricecurrency = new getpricecurencyController();
            $entryPrice = $getpricecurrency->getPriceCurrency($currency);



            $entry_price = $entryPrice;
            $fornumberpoint = $entryPrice + ((0.2 / 100) * $entryPrice);
            $fulltest = [$entryPrice, $fornumberpoint];

            $resultString = implode('-', $fulltest);

            $stop_loss_percentage = 40;
            $stop_loss = $entry_price - ($stop_loss_percentage * $entry_price / 100);
            $target1 = $entry_price + ((0.5 / 100) * $entry_price);
            

            $targets = [$target1];
            $plan = ['1'];
             $requestData = [
                'entry' => $fulltest,
                'ticker' => $currency,
                'title' => 'title',
                'stoplose' => $stop_loss,
                'targets' => $targets,
                'plan' => $plan,
                'planes_id' => 1,
                'admin_id' => 7,
                'user_id' => 7,
                'totalPlan' => $plan,
                'status' => 1, // this not sell only to buy
                'bot_num'=>8,
                'Activate_bot'=>'start',

            ];



        // $this->sendDataAfterBot($test, $data['bot_num']);

              // Create an instance of Illuminate\Http\Request
                $request = new Request($requestData);

                // Create an instance of RecommendationController
                $recommendationController = new RecommendationController();

                // Call the storeApiRequest method with the Request instance
                $recommendationController->storeApiRequest($request);

       

                   $bodyManager = "كده اشتري عمله من كابتن secound helf. " . $ticker;
                 return    $telgrem->upvaleFreeGroupe($bodyManager);


        }elseif ($action == "sell") {
            $lastRecommended = recommendation::where([
                  'currency' => $currency,
                'user_id' => 7,
            ])->latest('id')->first();

            if ($lastRecommended !== null) {
                // run function shdown
                $stopBotRecomindation = new RecommendationController();

                $requestData2 = new \App\Http\Requests\RecomindationIdRequest([
                    'recomondations_id' => $lastRecommended->id,
                    'shutdown' => 0,
                ]);
                $stopBotRecomindation->stopBotRecomindation($requestData2); // call the function
                $lastRecommended->status = 0;
                $lastRecommended->save();
                 $bodyManager = "كده باع عمله .$ticker من كابتن HELF";
return $telgrem->upvaleFreeGroupe($bodyManager);

            } else {

                $body = "الدتا راجعه فاضيه";
                // return $notfication->Ahmed($body);
            }
        }{

        }



    }
}
