<?php

namespace App\Http\Controllers\limitBinance;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Helper\NotficationController;

class finleController extends Controller
{
    protected $client;
    protected $user;

    public function __construct(Client $client)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);

    }

    public function buy(Request $request)
    {
        $startTime = microtime(true); // Record the start time

        $data = $request->input();
        $parsedResponse = json_decode(json_encode($data), true);

        // // Access the action and ticker values

        $action = $parsedResponse[0]['action']; // buy or sell
        $ticker = $parsedResponse[1]['ticker']; //name of curency
        $entryPrice = $parsedResponse[2]['entryPrice']; // entry price

        try {
            $symbol = $ticker."USDT"; // Name of Currency
            $side = $action; // Buy Or sell
            $price = $entryPrice; // Entry price



            if ($side == "sell") {

                $quantity=$this->getBlance($ticker);
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
            } else {
                   $test = test::latest('id')->first(); // Retrieve input data

                 $mybalance = $test->MyBlance;

                // Calculate the quantity based on the balance and the price
                $quantity = $mybalance / $price;

                // Fetch exchange info from Binance
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
            }

            // Continue with your order placement logic here
            $timestamp = $this->timestampBinance(); // Assuming this method is available in the class
            $signature = $this->hashHmac($symbol, $side, $quantity, $timestamp); // Assuming this method is available

            $responseData = $this->sendMarketOrderRequest($symbol, $side, $quantity, $timestamp, $signature);
            $endTime = microtime(true); // Record the end time
            $executionTime = $endTime - $startTime;

            // Update the 'orderID' and 'status' properties
            $request->merge([
                'orderID' => $responseData['orderId'],
                'status' => $responseData['status'],

            ]);

            // Insert the transaction
             $test = $this->insertTransaction($responseData,$executionTime); // Assuming this method is available

            // $notification = new NotficationController();


          $notfication = new NotficationController();
         $notfication->Ahmed($action . "  " .$ticker);
            return response()->json([
                'success' => true,
                'responseData' => $responseData,
            ]);

        } catch (\Exception $e) {
                       $notfication = new NotficationController();

            $notfication->Ahmed("Exception");
            // Handle Binance API-related errors
            return response()->json([
                'success' => false,
                'error' => 'Binance API error',
                'message' => $e->getMessage(),
            ], 400); // Use an appropriate HTTP status code
        } catch (\Exception $e) {
                    $notfication = new NotficationController();

            $notfication->Ahmed("Exception");
            // Handle other exceptions or internal errors
            return response()->json([
                'success' => false,
                'error' => 'Internal error',
                'message' => $e->getMessage(),
            ], 500); // Use an appropriate HTTP status code
        }

    }
    protected function timestampBinance()
    {
        $response = $this->client->get('/api/v3/time');
        $serverTime = json_decode($response->getBody(), true);
        return $serverTime['serverTime'];
    }

    protected function hashHmac($symbol, $side, $quantity, $timestamp)
    {
        $query = http_build_query([
            'symbol' => $symbol,
            'side' => $side,
            'type' => 'MARKET',
            'quantity' => $quantity,
            'timestamp' => $timestamp,
        ]);
        return hash_hmac('sha256', $query, 'yUlfwNoedfsb2FdXs2iR0Ws8Xt3buSIjIAe2q0i5xktwsNQUf4CfCLQ3aDx9orDH'); // Replace with your API secret
    }

    protected function sendMarketOrderRequest($symbol, $side, $quantity, $timestamp, $signature)
    {
        $response = $this->client->post('/api/v3/order', [
            'headers' => [
                'X-MBX-APIKEY' => "5CM1UX19uiuhVxod8DxVaTHoYR9jBfVXeLc5LUwOrvnrOpKCgHG3glAGmSyk1PhQ",//api key
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

}
