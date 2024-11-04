<?php

namespace App\Http\Controllers\MYBoot;

use App\Models\buysellnow;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\buySell\adminBuySell;
use App\Http\Requests\RecomindationIdRequest;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Helper\BotTelgremController;
use App\Http\Controllers\Helper\getpricecurencyController;
use App\Http\Controllers\DependencyRecomindation\shudownRecomindationController;
use App\Models\plan_recommendation;
class AdminstrategicController extends Controller
{
    use ResponseJson;
    public function buy(adminBuySell $request)
    {
     return 555;

        $user = auth('api')->user();
        if (!$user || $user->state == "user" || $user->state == "support") {
            return response()->json([
                'massage' => "not have permison"
            ]);
        }
        $ticker = $request->ticker;
        $type = $request->type;
        $user_id = $user->id;
        $target1fromadmin = $request->target1;
        $target2fromadmin = $request->target2;
        $stoplossfromadmin = $request->stoplose;

        // cheack if type == buy
        if ($type == "buy") {
            $pricetikernow = new getpricecurencyController();   // get controller  price currency
            $minEntryPrice = $pricetikernow->getPriceCurrency($ticker);        // get price currency


            $maxentryprice = $minEntryPrice + ((0.5 / 100) * $minEntryPrice); // max entry price

            $fullEntryPrice = [$minEntryPrice, round($maxentryprice, 4)]; //eound 4 from max entry price
            $finle_entry_price = implode('-', $fullEntryPrice); // impoled - entry price

            // for target
            $targetOne = $minEntryPrice + (($target1fromadmin / 100) * $minEntryPrice);
            $targetTwo = $minEntryPrice + (($target2fromadmin / 100) * $minEntryPrice);
            $finle_targets = [$targetOne, $targetTwo];
            //   for stop lose
            $finle_stop_loss = $minEntryPrice - ($stoplossfromadmin * $minEntryPrice / 100);
            // FOR PLAN
            
            // /////////////////////////////////////////////////////////////////////////////////////////
          $plan = $request->input('totalPlan');
            
            // //////////////////////////////////////////////////////////////
            
            

            if ($user->id == 84) {
                $admin_id = $request->admin_id; // for admin becouse if abdo chosse for company or for all user
            } else {
                $admin_id = 1146;
            }
            $requestData = [
                'entry_price' => $finle_entry_price,
                'currency' => $ticker,
                'title' => $ticker,
                'stop_price' => $finle_stop_loss,
                'targets' => $finle_targets,
                'plan' => $plan,
                'planes_id' => 1,
                'admin_id' => $user->id,
                'user_id' => $user->id,
                'totalPlan' => $plan,
                'status' => 1, // this not sell only to buy

            ];



            $request = new Request($requestData);
            $recomnidation = new RecommendationController();
            $reco = $recomnidation->store($request);
            $content = $reco->content(); // Get the JSON content from the response
            $data = json_decode($content, true); // Decode the JSON content into an associative array

            // Access the 'id' from the decoded JSON data
            $id = $data['massge']['id']; // Assuming 'massge' contains the 'id' field






            // // Access the 'id' from the 'massge' array
            // $symbol = strtoupper($ticker); // Convert symbol to uppercase

            // if (substr($symbol, -4) !== 'USDT') {
            //     // If 'USDT' is not found at the end of $symbol, add 'USDT' to it
            //     $symbol .= 'USDT';
            // } else {
            //     $symbol = $symbol;
            // }


            // $buysellnow = buysellnow::create([
            //     'ticker' => $symbol,
            //     'user_id' => $user_id,
            //     'recomindation_id' => $id,
            //     'type' => "buy",

            // ]);


            return $this->success('operation created successfully');






            // make secound or max entry price




        }
    }

    public function sell(RecomindationIdRequest $request)

    {
        $data = [
            'shutdown' => $request['shutdown'],
            "recomondations_id" => $request['recomondations_id'],
        ];


        $response = Http::post('http://51.161.128.30:5015/shutdown_recomondations_id', $data);
        $responseBody = $response->body();
        $responseData = json_decode($responseBody);

        if ($responseData && isset($responseData->success) && $responseData->success === true) {

            // add in buy sell now
            $this->updatedStatse($request['recomondations_id']);
            return $this->success("operation created successfully");
        } else {
            // The "false" field is present and is true
            $telgrem = new BotTelgremController();
            $recomondations_id = $request['recomondations_id'];
            $text = "لدينا مشكلة في غلق التوصيات رقم $recomondations_id";
            $telgrem->ramyboterror($text);
            return $this->error("Operation Not Accomplished");
        }
    }


    public function updatedStatse($recomindation_id)
    {
        $updated = buysellnow::where('recomindation_id', $recomindation_id)->first();
        $updated->type = "sell";
        $updated->save();

        return 'ok';
    }
}
