<?php

namespace App\Http\Controllers\Helper\Deposite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\buffer_user;




class DepositeController extends Controller
{
    public function getDeposits()
    {
      

         $userIdsFinshQuarter1 = buffer_user::where('finsh_quarter', 1)->pluck('user_id');

 return $filteredRecords = buffer_user::whereNotIn('user_id', $userIdsFinshQuarter1)->get();


        // $renewedUsers = [];
        
        // foreach ($users as $user) {
        //     // Check if the user has renewed their subscription in the same table
        //     $renewed = buffer_user::where('user_id', $user->user_id)
        //         ->where('finsh_quarter', 1)
        //         ->exists();
        
        //     if ($renewed) {
        //         $renewedUsers[] = $user;
        //     }
        // }
        
        // return $renewedUsers;


         $api_key = env('API_KEY_DEPOSITE');
    $api_secret = env('KEY_SECRT_DEPOSITE');

    // Get current date and start/end timestamps for today
    $startTime = Carbon::today()->timestamp * 1000; // Binance API requires timestamps in milliseconds
    $endTime = Carbon::tomorrow()->subSecond()->timestamp * 1000; // endTime is just before the start of tomorrow

    // Prepare parameters for the signature
    $timestamp = $this->timestampBinance();
    $params = [
        'timestamp' => $timestamp,
        // 'startTime' => $startTime,
        // 'endTime' => $endTime,
    ];
    $query = http_build_query($params);

    // Create the signature
    $signature = hash_hmac('sha256', $query, $api_secret);

    // Make the API request
    $client = new Client();
    $response = $client->get('https://api.binance.com/sapi/v1/capital/deposit/hisrec', [
        'headers' => [
            'X-MBX-APIKEY' => $api_key,
        ],
        'query' => $query . "&signature={$signature}", // Add signature to the query
    ]);

    // Decode the response and return the deposits
    $deposits = json_decode($response->getBody()->getContents());
 

        foreach ($deposits as $deposit) {
            $textid = trim(str_replace('Internal transfer', '', $deposit->txId));
            $mount = $deposit->amount;
            $network = $deposit->network;
            $this->insertDeposit($mount, $textid, $network, $user_id = 1); // Pass parameters here
        }
    }
    
    


    public  function createBinanceSignature($data, $apiSecret)
    {
        return hash_hmac('sha256', $data, $apiSecret);
    }





    protected function timestampBinance()
    {
        $client = new Client();
        $response = $client->get('https://api.binance.com/api/v3/time');
        $serverTime = json_decode($response->getBody(), true);
        return $serverTime['serverTime'];
    }
     public function insertDeposit($mount, $textid, $network, $user_id)
    {

        $existingDeposit = DepositsBinance::where('textId', $textid)->first();

        // If the record with the same textId exists, do not insert a new one
        if ($existingDeposit) {
            $body = "هناك خطا يرجي التاكد او هذه العنوان موجود سابقا ";
            $notfy = new NotficationController();
            $user = 'gg';
            $notfy->notfication($user, $body);

            return 'Duplicate';
        }


        $test = DepositsBinance::create([
            'amount' => $mount,
            'textId' => $textid,
            'network' => $network,
            'user_id' => $user_id,




        ]);

        return 'ok';
    }

    public function walteaddress(Request $request)
    {
        return response()->json([
            "success" => true,
            "wallet" => "TLNaJdkATC5NnmHfnLfskXMG85NtihQT29"
        ]);
    }
}
