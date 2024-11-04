<?php

namespace App\Http\Controllers\MYBoot;

use Illuminate\Http\Request;
use App\Models\recommendation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Helper\NotficationController;
use App\Http\Controllers\Helper\getpricecurencyController;

class EmabuysellnowController extends Controller
{
    public function updatedAfflite(Request $request)
    {
     return 555;


        $notfication = new NotficationController();
        $notfication->Ahmed('goooooooooo');

        $data = $request->input();
        $parsedResponse = json_decode(json_encode($data), true);

        // // Access the action and ticker values

        $action = $parsedResponse[0]['action']; // buy or sell
        $ticker = $parsedResponse[1]['ticker']; //name of curency
        $entryPrice = $parsedResponse[2]['entryPrice']; // entry price


        $getpricecurrency=new getpricecurencyController();
        $entryPrice=$getpricecurrency->getPriceCurrency($ticker);

        $currency = $ticker;
        $entry_price = $entryPrice;
        $fornumberpoint = $entryPrice + ((1.5 / 100) * $entryPrice);
        $fulltest = [$entryPrice, $fornumberpoint];

        $resultString = implode('-', $fulltest);

        $stop_loss_percentage = 15;
        $stop_loss = $entry_price - ($stop_loss_percentage * $entry_price / 100);
        $target1 = $fornumberpoint + ((10 / 100) * $fornumberpoint); // for max entry price
        $target2 = $entry_price + ((20 / 100) * $entry_price);
        // $target3 = $entry_price + ((5 / 100) * $entry_price);

        $targets = [$target1, $target2];
        $plan = ['7'];
        // recived all data

        if ($action == "sell") {



              $lastRecommended = recommendation::where([
                'currency' => $currency,
                'user_id' => 30,
                'status' => 1,
                ])->latest('id')->first();


            if ($lastRecommended !== null) {
                // run function shdown
                $stopBotRecomindation = new RecommendationController();

                  $requestData2 = new \App\Http\Requests\RecomindationIdRequest([
                    'recomondations_id' => $lastRecommended->id,
                    'shutdown' => 0,
                ]);
                $stopBotRecomindation->stopBotRecomindation($requestData2); // call the function
                $lastRecommended->status=0;
                $lastRecommended->save();
                $notfication->Ahmed('اتاكد انه باع');
            } else {

                $body = "الدتا راجعه فاضيه";
                return $notfication->Ahmed($body);
            }
        } else {
            $requestData = [
                'entry_price' => $resultString,
                'currency' => $currency,
                'title' => 'title',
                'stop_price' => $stop_loss,
                'targets' => $targets,
                'plan' => $plan,
                'planes_id' => 1,
                'admin_id' => 30,
                'user_id' => 30,
                'totalPlan' => $plan,
                'status' => 1, // this not sell only to buy

            ];

            $request = new Request($requestData);
            $recomnidation = new RecommendationController();
            $recomnidation->store($request);

            $bodyManger = "كده اشتري";
            return   $notfication->Ahmed($action);
        }
    }
}
