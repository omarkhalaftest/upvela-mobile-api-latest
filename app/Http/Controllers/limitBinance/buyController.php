<?php

namespace App\Http\Controllers\limitBinance;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\limitBinance\sellLimitController;
use App\Http\Controllers\Helper\NotficationController;




class buyController extends Controller
{
     protected $client;
    protected $user;

    public function __construct()
    {

        $this->client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);
        // $this->user = User::where('id',8)->first();
    }

    public function buy(Request $request)
    {
            //  return 55;
        $not=new NotficationController();

        try {
            
            $symbol = $request['symbol']; // Name of Currency
            $lastFourCharacters = substr($symbol, -4);

        // Check if the last 4 characters are 'USDT'
        if ($lastFourCharacters !== 'USDT') {
            // Append 'USDT' to the symbol
            $symbol .= 'USDT';
        }

            $entryprice=$request['entry_price'];
            
                 
          $pricebuy_finle =  sprintf("%.3f", $entryprice);
          
            if (strpos($entryprice, '0.0') !== false) {
          return  $not->Ahmed("include");
                      }
            $side = 'BUY';    // Buy Or sell
            $type = 'MARKET'; // Set order type to MARKET
            $mybalance = 11; // get my blance 50
            // $not->Ahmed($symbol .$en$ftryprice );
            // Calculate the quantity based on the balance and the price
            $quantity = $mybalance / $entryprice;

            // Fetch exchange info from Binance
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

            // Continue with your order placement logic here
            $timestamp = $this->timestampBinance();
            $signature = $this->hashHmac($symbol, $side, $type, $quantity, $timestamp);

            $responseData = $this->sendOrderRequest($symbol, $side, $type, $quantity, $timestamp, $signature);



        // $responseData is already an array, no need to decode it again
             $data = $responseData;

            // Assign qty to a variable
            $qty = $data['fills'][0]['qty'];
            $price_buy = $data['fills'][0]['price'];
            
            // price for sell after add 1.005
            
            $price_sell=$price_buy * 1.005;
            
        $finle_price_sell = number_format(round($price_sell, 3), 3);
     
     
     
 
 
         

            $sell=new sellLimitController();
            $request['symbol_sell']=$symbol;
            $request['quantity_sell']=$qty;
            $request['price_buy']=$finle_price_sell;
            $not->Ahmed("buy => ".$symbol .' ' .$price_buy );

           $sell->sell($request);
            //  public function sell($symbol,$quantity,$price)






            return response()->json([
                'price_buy' => $price_buy,
                'price_sell'=>$finle_price_sell,
                'responseData' => $responseData,
            ]);
        } catch (ClientException $e) {
            
            $not->Ahmed($e->getMessage());
            
            return $this->handleBinanceError($e, $request);
        } catch (\Exception $e) {
              $not->Ahmed($e->getMessage());
            return $this->handleInternalError($e, $request);
        }
    }




    protected function timestampBinance()
    {
        $response = $this->client->get('/api/v3/time');
        $serverTime = json_decode($response->getBody(), true);
        return $serverTime['serverTime'];
    }

    protected function hashHmac($symbol, $side,$type,$quantity, $timestamp)
    {
        $query = http_build_query([
            'symbol' => $symbol,
            'side' => $side,
            'type' => $type,
             'quantity' => $quantity,
            'timestamp' => $timestamp,

        ]);
        return hash_hmac('sha256', $query, $this->user->binanceSecretKey);
    }

    protected function sendOrderRequest($symbol, $side,$type, $quantity, $timestamp, $signature)
    {
        $response = $this->client->post('/api/v3/order', [
            'headers' => [
                'X-MBX-APIKEY' => $this->user->binanceApiKey,
            ],
            'form_params' => [
                'symbol' => $symbol,
                'side' => $side,
                'type' => $type,
                 'quantity' => $quantity,

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
