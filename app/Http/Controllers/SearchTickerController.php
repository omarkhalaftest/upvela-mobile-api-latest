<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\recommendation;
use App\Http\Resources\RecommendationResource;

class SearchTickerController extends Controller
{
    public function getCurency(Request $request)
    {

        $ticker=$request['ticker'];
        $page = $request->input('page', 1); // Get the requested page from the request parameters

        $recommendations=recommendation::where('currency',$ticker)->paginate(20, ['*'], 'page', $page);


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
}
