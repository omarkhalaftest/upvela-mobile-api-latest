<?php

namespace App\Http\Controllers\Helper;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BotTelgremController extends Controller
{
    // to get any
    public function getChatID()
    {
        $telegramToken = '6940128118:AAFxAsOkXV9WD1xy9Q4jr2fRblXOuygMzfU'; // this from bot father
        $client = new \GuzzleHttp\Client();
        $response = $client->get("https://api.telegram.org/bot{$telegramToken}/getUpdates");
        $updates = json_decode($response->getBody(), true);

        return $response; // i want from here id chat so add bot in telgrem group ant send any text from any user have permision add text



    }
    public function upvaleFreeGroupe($text)
    {
        $telegramToken = '6133147754:AAH8vqD5lRUcsxZqBFfAWI7hC-gBtgq6Duk';
        $chatId = '-1001951331969';
        $text = $text;

        $client = new Client();
        return $response = $client->get("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        ]);
    }
    
    public function Sasa($text)
    {
        
        $telegramToken = '6284358238:AAHz4D1EWDhVnWH956qGaE09DMm8bMbSYIU';
        $chatId = '-1002020821789';
        $text = $text;

        $client = new Client();
        return $response = $client->get("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        ]);
    }


    public function ramyboterror($text)
    {
        $telegramToken = '6940128118:AAFxAsOkXV9WD1xy9Q4jr2fRblXOuygMzfU';
        $chatId = '-4075564810';
        $text = $text;

        $client = new Client();
        return $response = $client->get("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        ]);
    }
}
