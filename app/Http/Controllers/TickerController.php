<?php

namespace App\Http\Controllers;

use App\Models\Tiker;
use App\Models\buysellnow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TickerController extends Controller
{
    // Updated With old Date
    public function getAllTickers(Request $request)
    {
           $user=auth('api')->user();
         $alltiker=DB::table('mytickers')->get();
         $adminticker=buysellnow::where('user_id',$user->id)->get();
         return response()->json([
            'allticker'=>$alltiker,
            'adminticker'=>$adminticker,
         ]);
    }

    // Update on ticker
    public function updateTicker(Request $request)
    {
        $user = auth('api')->user();
        if ($user->state != 'super_admin') {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 401);
        }
        DB::table('mytickers')->where('ticker', $request->ticker_old)->update([
            'ticker' => $request->ticker_new, // 'ticker' => 'BTCUSDT
            'price' => $request->price ? $request->price : 0,
            'time' => $request->time ? $request->time : 0,
        ]);
        return response()->json([
            'message' => 'Ticker Updated Successfully'
        ]);
    }
    // Delete ticker
    public function deleteTicker(Request $request)
    {
        $user = auth('api')->user();
        if ($user->state != 'super_admin') {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 401);
        }
        DB::table('mytickers')->where('ticker', $request->ticker)->delete();
        return response()->json([
            'message' => 'Ticker Deleted Successfully'
        ]);
    }
    // Add ticker
    public function addTicker(Request $request)
    {
        $user = auth('api')->user();
        if ($user->state != 'super_admin') {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 401);
        }
        $ticker = Tiker::where('ticker',$request->ticker)->first();
        if ($ticker) {
            return response()->json([
                'message' => 'Ticker Already Exists'
            ], 401);
        }
        $ticker = new Tiker();
        $ticker->ticker = $request->ticker;
        $ticker->price = $request->price ? $request->price : 0;
        $ticker->time = $request->time ? $request->time : 0;
        $ticker->save();
        return response()->json([
            'message' => 'Ticker Added Successfully'
        ]);
    }
}
