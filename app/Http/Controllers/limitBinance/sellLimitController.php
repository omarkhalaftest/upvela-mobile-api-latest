<?php

namespace App\Http\Controllers\limitBinance;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\binance;
use App\Models\TargetsRecmo;
use Illuminate\Http\Request;
use App\Models\recommendation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\profit_buffer_to_user;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\Helper\NotficationController;

class sellLimitController extends Controller
{
   protected $client;
    protected $user;

    public function __construct()
    {

        $this->client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);
        $this->user =User::where('id',8)->first();
    }

    public function send($recommendation_id)
    {
         
        

        $data=[
            'recommendation_id'=>$recommendation_id,
        ];
          $get=Http::post("http://51.161.128.30:5001/test_limit",$data);
     }

    public function sell(Request $request)
{

 

     $id=$request['id'];
    $not=new NotficationController();
    $recomondations_id=$request['recomondations_id']=$id;
       $binance=binance::where([
        'recomondations_id'=>$recomondations_id,
        'side'=>"buy",
        'status'=>"FILLED",
        'user_id'=>8
    ])->latest()->first();

    try {
        $symbol = $binance['symbol']; // Name of Currency
        $side = "sell";
        $quantityt = $binance['quantity'];
        $stopPrice="";
        $price=$binance['price'];


           $price = $this->filterPrice($price);

               $recomdation=TargetsRecmo::where('recomondations_id',$recomondations_id)->latest()->first();
                $target_recomindation=$recomdation->target;
        if($price > $target_recomindation)
        {
                $not->Ahmed($target_recomindation .$price );
                               $not->Ahmed("large");


            return "larger";
        }else{
          
              $timestamp = $this->timestampBinance();
                 $signature = $this->hashHmac($symbol, $side, $quantityt, $price, $stopPrice, $timestamp);
                   $responseData = $this->sendOrderRequest($symbol, $side, $quantityt, $price, $stopPrice, $timestamp, $signature);

                               $not->Ahmed("Done");


            return response()->json(200, 200);
        }
        } catch (ClientException $e) {
            $not->Ahmed($e->getMessage());
            // Handle Binance API errors
            return $this->handleBinanceError($e, $request);
        } catch (\Exception $e) {
            // Handle internal errors
            // $not->Ahmed($e->getMessage());
            return $this->handleInternalError($e, $request);
        }
        }








    public function price($symbol,$price)
    {

            // Fetch the price filter details for the trading pair from Binance API
            $response = Http::get('https://api.binance.com/api/v3/exchangeInfo');

            if ($response->successful()) {
                $exchangeInfo = $response->json();
                // Find the price filter details for the given symbol
                $filters = collect($exchangeInfo['symbols'])->firstWhere('symbol', $symbol)['filters'];

                // Extract the price filter details
                $priceFilter = collect($filters)->firstWhere('filterType', 'PRICE_FILTER');

                if ($priceFilter) {
                    $minPrice = $priceFilter['minPrice'];
                    $maxPrice = $priceFilter['maxPrice'];

                    // Adjust the price if it's outside the allowed range
                    if ($price < $minPrice) {
                        return $minPrice;
                    } elseif ($price > $maxPrice) {
                        return $maxPrice;
                    }
                }
            }

            // If price filter details are not available or the response was not successful, return the original price
            return $price;

    }


    protected function filterPrice($price)
{
    $priceString = strval($price);
    $decimalPosition = strpos($priceString, '.');
    $digitsAfterDecimal = strlen($priceString) - $decimalPosition - 1;
    $price = number_format($price * 1.004, $digitsAfterDecimal, '.', '');
    return $price;
}



//     // If price filter details are not available or the response was not successful, return the original price
//     return $originalPrice;
// }


    protected function timestampBinance()
    {
        $response = $this->client->get('/api/v3/time');
        $serverTime = json_decode($response->getBody(), true);
        return $serverTime['serverTime'];
    }

    protected function hashHmac($symbol, $side, $quantity, $price, $stopPrice, $timestamp)
    {

        // return 'qu'.$quantity . ' ' . 'price' .$price;
        $query = http_build_query([
            'symbol' => $symbol,
            'side' => $side,
            'type' => 'LIMIT',
            'timeInForce' => 'GTC',
            'quantity' => $quantity,
            'price' => $price,
            'timestamp' => $timestamp,

        ]);
        return hash_hmac('sha256', $query, $this->user->binanceSecretKey);
    }

    protected function sendOrderRequest($symbol, $side, $quantity, $price, $stopPrice, $timestamp, $signature)
    {
        $response = $this->client->post('/api/v3/order', [
            'headers' => [
                'X-MBX-APIKEY' => $this->user->binanceApiKey,
            ],
            'form_params' => [
                'symbol' => $symbol,
                'side' => $side,
                'type' => 'LIMIT',
                'timeInForce' => 'GTC',
                'quantity' => $quantity,
                'price' => $price,
                'timestamp' => $timestamp,

            ],
            'query' => [
                'signature' => $signature,
            ],
        ]);


        return json_decode($response->getBody(), true);
    }

    protected function handleBinanceError(ClientException $e, Request $request)
    {
        $responseBody = json_decode($e->getResponse()->getBody(), true);
        $errorCode = $responseBody['code'] ?? null;
        $errorMessage = $responseBody['msg'] ?? 'Unknown error';
        $request['status'] = 'Error';
        $request['massageError'] = $errorMessage;

        return response()->json([
            'success' => false,

            'error' => 'Binance API error',
            'message' => $errorMessage,
            'code' => $errorCode,
        ], $e->getCode());
    }

    protected function handleInternalError(\Exception $e, Request $request)
    {
        $request['status'] = 'Error';
        $request['massageError'] = 'Internal error';
         return response()->json([
            'error' => 'Internal error',
            'message' => $e->getMessage(),
        ], 500); // Internal Server Error
    }




  


 


  
    }

