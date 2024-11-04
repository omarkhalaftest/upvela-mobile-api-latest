// <?php

// namespace App\Http\Controllers\MYBoot;

// use Illuminate\Http\Request;
// use App\Models\recommendation;
// use App\Http\Controllers\Controller;
// use App\Http\Controllers\RecommendationController;
// use App\Http\Controllers\Helper\NotficationController;

// class ScalpinghelfController extends Controller
// {
//     public function updatedAfflite(Request $request)
//     {
 


//         $notfication = new NotficationController();


//         $data = $request->input();
//         $parsedResponse = json_decode(json_encode($data), true);

//         // // Access the action and ticker values

//         $action = $parsedResponse[0]['action']; // buy or sell
//         $ticker = $parsedResponse[1]['ticker']; //name of curency
//         $entryPrice = $parsedResponse[2]['entryPrice']; // entry price




//         $currency = $ticker;
//         $entry_price = $entryPrice;
//         $fornumberpoint = $entryPrice + ((0.5 / 100) * $entryPrice);
//         $fulltest = [$entryPrice, $fornumberpoint];

//         $resultString = implode('-', $fulltest);

//         $stop_loss_percentage =1;
//         $stop_loss = $entry_price - ($stop_loss_percentage * $entry_price / 100);
//         $target1 = $entry_price + ((0.7 / 100) * $entry_price);
//         // $target2 = $entry_price + ((2 / 100) * $entry_price);

//         $targets = [$target1];
//         $plan = ['6'];

//         $requestData = [
//                     'entry_price' => $resultString,
//                     'currency' => $currency,
//                     'title' => 'title',
//                     'stop_price' => $stop_loss,
//                     'targets' => $targets,
//                     'plan' => $plan,
//                     'planes_id' => 1,
//                     'admin_id' => 948,
//                     'user_id' => 948,
//                     'totalPlan' => $plan,
//                     'status' => 1, // this not sell only to buy

//                 ];
//                 $request = new Request($requestData);
//                 $recomnidation = new RecommendationController();
//                 $recomnidation->store($request);

//                 $bodyManger = "كده اشتري";
//                 return   $notfication->Ahmed($action);
//             }


//         // recived all data

//         // if ($action == "sell") {


//         //     //   $body = " المفروض انه دخل يبيع";
//         //     //   $notfication->Ahmed($body);
//         //      $lastRecommended = recommendation::where([
//         //         'currency' => $currency,
//         //         'user_id' => 6,
//         //         'status' => 1,
//         //         ])->latest('id')->first();


//         //     if ($lastRecommended !== null) {
//         //         // run function shdown
//         //         $stopBotRecomindation = new RecommendationController();

//         //           $requestData2 = new Request([
//         //             'recomondations_id' => $lastRecommended->id,
//         //             'shutdown' => 0,
//         //         ]);
//         //         $stopBotRecomindation->stopBotRecomindation($requestData2); // call the function
//         //         $lastRecommended->status=0;
//         //         $lastRecommended->save();
//         //         $notfication->Ahmed('اتاكد انه باع');
//         //     } else {

//         //         $body = "الدتا راجعه فاضيه";
//         //         return $notfication->Ahmed($body);
//         //     }
//         // } else {
//         //     $requestData = [
//         //         'entry_price' => $resultString,
//         //         'currency' => $currency,
//         //         'title' => 'title',
//         //         'stop_price' => $stop_loss,
//         //         'targets' => $targets,
//         //         'plan' => $plan,
//         //         'planes_id' => 1,
//         //         'admin_id' => 6,
//         //         'user_id' => 6,
//         //         'totalPlan' => $plan,
//         //         'status' => 1, // this not sell only to buy

//         //     ];

//     //         $request = new Request($requestData);
//     //         $recomnidation = new RecommendationController();
//     //         $recomnidation->store($request);

//     //         $bodyManger = "كده اشتري";
//     //         return   $notfication->Ahmed($action);
//     //     }
//     // }
// }


