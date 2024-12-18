<?php

namespace App\Http\Controllers\V2\Recomindation;

use Exception;
use App\Models\User;
use App\Models\binance;
use App\Models\selladminnow;
use App\Traits\ResponseJson;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ClientException;

class sellRecomindationsController extends Controller
{

    use ResponseJson;
    protected $client;



    public function __construct(Client $client)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);


    }
    public function sell(Request $request)
    {
        $recomindationId=$request->recomindationId;

          $binaances=binance::where([
            'recomondations_id'=>$recomindationId,
            'status'=>'FILLED',
        ])->get();

        foreach ($binaances as  $binaance) {
            $request['user_id']=$binaance->user_id;
            $request['symbol']=$binaance->symbol;
            $request['quantity']=$binaance->quantity;

            return $this->sellWithoutrecomindation($request);
        }


    }
    public function sellWithoutrecomindation(Request $request)
    {


    $userId=$request->user_id;

        $email = $request->email;
        $user = User::where('id', $userId)->first();

        $apikey = $user->binanceApiKey;
        $secretKey = $user->binanceSecretKey;
        $symbol = $request->symbol;
        $quantity = $request->quantity;
        $side = "sell";
        try {
            $exchangeInfo = Http::get('https://api.binance.com/api/v3/exchangeInfo')->json();

            // Find the filters for the specified symbol
            $symbolInfo = collect($exchangeInfo['symbols'])->first(function ($item) use ($symbol) {
                return $item['symbol'] === $symbol;
            });

            // Extract the LOT_SIZE filter
            $lotSizeFilter = collect($symbolInfo['filters'])->first(function ($filter) {
                return $filter['filterType'] === 'LOT_SIZE';
            });

            if ($lotSizeFilter) {
                $stepSize = $lotSizeFilter['stepSize'];
                $quantity = round($quantity, -log10($stepSize));
                // Ensure that $quantity adheres to the step size precision.
            }

            $quantity = $quantity;
            $timestamp = $this->timestampBinance(); // Assuming this method is available in the class
            $signature = $this->hashHmac($symbol, $side, $quantity, $timestamp, $secretKey); // Assuming this method is available

            $responseData = $this->sendMarketOrderRequest($symbol, $side, $quantity, $timestamp, $signature, $apikey);
            // $endTime = microtime(true); // Record the end time

            $responseData['user_id'] = $user->id;


            if (!$request['recomondations_id']) {
                return $test2 = $this->insertWithoutrecomindationId($responseData);
            } else {
                $responseData['recomondations_id'] = $request['recomondations_id'];
                return  $test = $this->insertTransaction($responseData);
            }

            return $this->success($responseData);
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }





    protected function sendMarketOrderRequest($symbol, $side, $quantity, $timestamp, $signature, $apiKey)
    {


        $response = $this->client->post('/api/v3/order', [
            'headers' => [
                'X-MBX-APIKEY' => $apiKey, //api key
            ],
            'form_params' => [
                'symbol' => $symbol,
                'side' => $side,
                'type' => 'MARKET', // Use MARKET order type for a market order
                'quantity' => $quantity,
                'timestamp' => $timestamp,
            ],
            'query' => [
                'signature' => $signature,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }




    protected function timestampBinance()
    {
        $response = $this->client->get('/api/v3/time');
        $serverTime = json_decode($response->getBody(), true);
        return $serverTime['serverTime'];
    }

    protected function hashHmac($symbol, $side, $quantity, $timestamp, $secretKey)
    {

        $query = http_build_query([
            'symbol' => $symbol,
            'side' => $side,
            'type' => 'MARKET',
            'quantity' => $quantity,
            'timestamp' => $timestamp,
        ]);
        return hash_hmac('sha256', $query, $secretKey); // Replace with your API secret
    }



    protected function handleBinanceError(ClientException $e, Request $request)
    {
        $responseBody = json_decode($e->getResponse()->getBody(), true);
        $errorCode = $responseBody['code'] ?? null;
        $errorMessage = $responseBody['msg'] ?? 'Unknown error';
        $request['status'] = 'Error';
        $request['massageError'] = $errorMessage;

        // Check the specific error code and provide a user-friendly message
        if ($errorCode === -2010) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient Balance',
                'message' => 'Your account has insufficient balance for the requested action.',
                'code' => $errorCode,
            ], $e->getCode());
        }

        // Handle other error cases here

        return response()->json([
            'success' => false,
            'error' => 'Binance API error',
            'message' => $errorMessage,
            'code' => $errorCode,
        ], $e->getCode());
    }

    public function insertTransaction($responseData)
    {



        $request = $responseData;
        $files = $responseData['fills'][0]; // Accessing the first fill



        $binanacebuy = binance::where('recomondations_id', $request['recomondations_id'])
            ->where('side', 'buy')
            ->where('user_id', $request['user_id'])
            ->latest()
            ->first();

        $binanacesell = $files['price'];
        $buy = $binanacebuy->price;

        $totalProfit = $binanacesell - $buy;
        if ($buy != 0) {
            $profit_per = ($totalProfit / $buy) * 100;
        } else {
            // يمكنك تعيين قيمة افتراضية أو التعامل بطريقة أخرى هنا
            $profit_per = 0; // على سبيل المثال، يتم تعيين الربح المئوي بصفر في حالة القسمة على صفر
        }

        if ($totalProfit > 0) {
            $profit = $totalProfit * (10 / 100);
        } else {
            $profit = 0;
        }

        $insert = binance::create([
            'user_id' => $request['user_id'],
            'symbol' => $request['symbol'],
            'side' => $request['side'],
            'type' => $request['type'],
            'quantity' => $files['qty'],
            'price' => $binanacesell,
            'status' => $request['status'],
            'orderID' => $request['orderId'], // Changed key to match response
            'massageError' => $request['clientOrderId'], // Changed key to match response
            'recomondations_id' => $request['recomondations_id'],
            'bot_num' => 1,
            'profit_usdt' => $totalProfit,
            'profit_per' => $profit_per,
            'fees' => $profit,
            'status_fees' => 1
        ]);

        // Check for successful insertion or handle errors here
        if ($insert) {
            // Insertion successful
            return $this->success("Successfully sell");  // Return the inserted record ID or appropriate response
        } else {
            // Insertion failed
            return $this->error(" NOT Successfully sell");
        }
    }

    // public function insertWithoutrecomindationId($responseData)
    // {
    //      $request = $responseData;
    //     $files = $responseData['fills'][0]; // Accessing the first fill

    //     $insert = selladminnow::create([
    //         'user_id' => $request['user_id'],
    //         'symbol' => $request['symbol'],
    //         'side' => $request['side'],
    //         'type' => $request['type'],
    //         'quantity' => $files['qty'],
    //         'price' => $files['price'],
    //         'status' => $request['status'],
    //         'orderID' => $request['orderId'], // Changed key to match response
    //         'commission' => $files['commission'], // Changed key to match response
    //         'stop_price'=>$request['type'],
    //         'admin_id'=>$this->admin->id,
    //     ]);

    //     // Check for successful insertion or handle errors here
    //     if ($insert) {
    //         // Insertion successful
    //         return $this->success("Successfully sell");  // Return the inserted record ID or appropriate response
    //     } else {
    //         // Insertion failed
    //         return $this->error(" NOT Successfully sell");
    //     }
    // }
}
