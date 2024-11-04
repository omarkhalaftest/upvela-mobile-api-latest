<?php

namespace App\Http\Controllers\V2\Recomindation;

use App\Models\expert;
use App\Models\binance;
use App\Models\bots_usdt;
use Illuminate\Http\Request;
use App\Models\recommendation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\RecommendationResource;

class SearchReacomindationController extends Controller
{
    public function searchrecomindation(Request $request)
    {


      $status=$request['status'];
       $expert = expert::where('status', $status)->pluck('recomondations_id')->toArray();

         $page = $request->input('page', 1);
         $recommendations = Recommendation::orderBy('created_at', 'desc')
         ->whereIn('id', $expert)
         ->where('archive', 0)
         ->with(['user', 'target', 'Recommindation_Plan.plan', 'ViewsRecomenditionnumber', 'tragetsRecmo'])
         ->paginate(20, ['*'], 'page', $page);

     $recommendations->each(function ($buy) {
         $buy->buy = Binance::where('recomondations_id', $buy->id)
             ->where('side', 'buy')
             ->where('status', 'FILLED')
             ->count();

         $buy->sell = Binance::where('recomondations_id', $buy->id)
             ->where('side', 'sell')
             ->where('status', 'FILLED')
             ->count();

         $buy->totalSell = array_sum(binance::where('recomondations_id', $buy->id)->where('side', 'sell')->pluck('profit_usdt')->toArray());

     });

     return response()->json([
         'data' => RecommendationResource::collection($recommendations),
         'meta' => [
             'current_page' => $recommendations->currentPage(),
             'last_page' => $recommendations->lastPage(),
             'total' => $recommendations->total(),
             'next_page' => $recommendations->nextPageUrl(),
         ],
     ]);

    }


    public function getallusersubscrib()
    {
        $repeatedData = DB::table('bots_usdt')
        ->select('user_id', 'bot_id', DB::raw('count(*) as count'))
        ->where('bot_status', 1)
        ->groupBy('user_id', 'bot_id')
        ->having('count', '>', 1)
        ->orderBy('user_id')
        ->get();
return $repeatedData;

    }
}
