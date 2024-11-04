<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
class getpricecurencyController extends Controller
{
    public function getPriceCurrency($symbol)
    {
        $client = new Client();

        $symbol=strtoupper($symbol)."USDT";

        // Replace with the cryptocurrency pair you want to check
        $apiUrl = 'https://api.binance.com/api/v3/ticker/price';

        try {
            $response = $client->request('GET', $apiUrl, [
                'query' => [
                    'symbol' => $symbol,
                ],
            ]);


            $data = json_decode($response->getBody()->getContents(), true);
            $price = rtrim(sprintf('%.8F', $data['price']), '0');


            return $price;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch data: ' . $e->getMessage()], 500);
        }
    }


    public function index(Request $request)
    {
            return $this->getPriceCurrency($request->ticker);
    }

}
