<?php

namespace App\Http\Controllers\Binance;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Traits\ResponseJson;
class getAllBuyForUserController extends Controller
{
     use ResponseJson;


    protected function timestampBinance()
    {
        $response = Http::get('https://api.binance.com/api/v3/time');
        $serverTime = json_decode($response->getBody(), true);
        return $serverTime['serverTime'];
    }






    public function getallcurrency(Request $request)
    {
         
         $email=$request->email;
         $user=User::where('email',$email)->first();
         
          if (!$user) {
        return $this->error('User not found'); // Or any other handling for this case
    }
         
         $apikey=$user->binanceApiKey;
         $keysecrt=$user->binanceSecretKey;

        try {
         $apiUrl = 'https://api.binance.com/api/v3/account';

         // Timestamp for the request
           $timestamp = $this->timestampBinance();;

         // Create a query string with the required parameters
         $queryString = http_build_query([
             'timestamp' => $timestamp,
         ]);

         // Create a signature for the request
         $signature = hash_hmac('sha256', $queryString, $keysecrt);

         // Make the GET request with authentication headers
          $response = Http::withHeaders([
             'X-MBX-APIKEY' => $apikey,
         ])->get($apiUrl . '?' . $queryString . '&signature=' . $signature);


           $accountData = $response->json();

           
                
                
                
                
                

                // Check if 'balances' key exists and if it's not empty
                if (isset($accountData['balances']) && !empty($accountData['balances'])) {
                    // 'balances' key exists and is not empty
                    // Perform actions when balances exist
                
                    // Example: Return balances data
                    return $this->success($accountData['balances']);
                } else {
                    // 'balances' key either doesn't exist or is empty
                    // Handle the scenario where there are no balances
                
                    return $this->error('No balances found');
                }
          
             } catch (ClientException $e) {
                         
            
                     return  $this->error('have error in api key');
            
            }
            
            
                }
                
            }
