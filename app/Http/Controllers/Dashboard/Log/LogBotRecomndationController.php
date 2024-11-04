<?php

namespace App\Http\Controllers\Dashboard\Log;

use App\Models\binance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogRecomnidation;
// use App\Http\Resources\LogBuyRecomnidation;
use App\Http\Resources\LogRecomindation\LogBuyRecomnidation;
use App\Http\Resources\LogRecomindation\LogSellRecomnidation;


class LogBotRecomndationController extends Controller
{
    public function getAllTransaticon(Request $request)
    {
        $id = $request->recomondations_id;
         $buyTransactions = binance::where('recomondations_id', $id)->where('side', 'buy')->get();
        $sellTransactions = binance::where('recomondations_id', $id)->where('side', 'sell')->get();

        $buyTransactions->each(function ($buy) {
            // Load the 'user' relationship for each $buy
          $test=$buy->load('user');

            // Access the 'name' attribute of the related user and assign it to the 'name' property of $buy
            $buy->name = $test->user->name;
            $buy->email = $test->user->email;
            $buy->id = $test->user->id;
            $buy->makeHidden(['user',]);

        });
        $sellTransactions->each(function ($buy) {
            // Load the 'user' relationship for each $buy
          $test=$buy->load('user');

          $buy->name = $test->user->name;
          $buy->email = $test->user->email;
          $buy->id = $test->user->id;
            $buy->makeHidden('user');

        });
// $allTransactions = $buyTransactions->concat($sellTransactions);
        $buy=LogBuyRecomnidation::collection($buyTransactions);
        $sell=LogSellRecomnidation::collection($sellTransactions);

        return response()->json([

            "buyTransactions" => $buy,
            "sellTransactions" => $sell,
        ]);
    }
}
