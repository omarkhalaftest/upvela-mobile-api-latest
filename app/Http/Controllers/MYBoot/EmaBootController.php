<?php

namespace App\Http\Controllers\MYBoot;

use Illuminate\Http\Request;
use App\Models\recommendation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Helper\NotificationController;
use App\Http\Controllers\Helper\getpricecurencyController;
 
class EmaBootController extends Controller
{
    public function updatedAfflite(Request $request)
    {
        // Uncomment the following line to fix the $notfication variable
        // $notfication = new NotificationController();
     
// return 555 ;

        $data = $request->input();
        $parsedResponse = json_decode(json_encode($data), true);

        $action = $parsedResponse[0]['action'];
        $ticker = $parsedResponse[1]['ticker'];
        $entryPrice = $parsedResponse[2]['entryPrice'];
        $currency = $ticker;
        $entry_price = $entryPrice;
         
        if ($action == "buy") {
            $getpricecurrency = new getpricecurencyController();
            $entryPrice = $getpricecurrency->getPriceCurrency($ticker);
            $fornumberpoint = $entryPrice + ((0.2 / 100) * $entryPrice);
            $fulltest = [$entryPrice, $fornumberpoint];
            
            $stop_loss_percentage = 40;
            $stop_loss = $entry_price - ($stop_loss_percentage * $entry_price / 100);
            $target1 = $fornumberpoint + ((1 / 100) * $fornumberpoint);
            $target2 = $entry_price + ((3 / 100) * $entry_price);
            
            $targets = [$target1, $target2];
            $plan = ['6'];

            $requestData = [
                'entry' => $fulltest,
                'ticker' => $currency,
                'title' => 'title',
                'stoplose' => $stop_loss,
                'targets' => $targets,
                'plan' => $plan,
                'planes_id' => 1,
                'admin_id' => 6,
                'user_id' => 6,
                'totalPlan' => $plan,
                'status' => 1,
                'bot_num' => 4,
                'Activate_bot' => 'start',
            ];

            $request = new Request($requestData);
            $recommendationController = new RecommendationController();
            $recommendationController->storeApiRequest($request);

            return $bodyManger = "كده اشتري";
        } elseif ($action == "sell") {
            // Uncomment the following line to fix the $notfication variable
            // $notfication = new NotificationController();

            $lastRecommended = recommendation::where([
                'currency' => $currency,
                'user_id' => 6,
            ])->latest('id')->first();

            if ($lastRecommended !== null) {
                $stopBotRecomindation = new RecommendationController();
                $requestData2 = new \App\Http\Requests\RecomindationIdRequest([
                    'recomondations_id' => $lastRecommended->id,
                    'shutdown' => 0,
                ]);
                $stopBotRecomindation->stopBotRecomindation($requestData2);
                $lastRecommended->status = 0;
                $lastRecommended->save();
                // Uncomment the following line to fix the $notfication variable
                // $notfication->Ahmed('اتاكد انه باع');
            } else {
                $body = "الدتا راجعه فاضيه";
                // Uncomment the following line to fix the $notfication variable
                // return $notfication->Ahmed($body);
            }
        }
    }
}
