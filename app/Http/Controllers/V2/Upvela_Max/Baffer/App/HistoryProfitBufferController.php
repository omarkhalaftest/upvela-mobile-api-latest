<?php

namespace App\Http\Controllers\V2\Upvela_Max\Baffer\App;

use App\Models\buffer;
use App\Models\buffer_user;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\profit_buffer_to_user;
use App\Http\Requests\Upvela_Max\buffer\HistoryProfitBufferRequest;

class HistoryProfitBufferController extends Controller
{
    public function history(HistoryProfitBufferRequest $request)
    {
        $user = auth('api')->user();
        $HashID = $request['HashID'];
        $profitBuffer = profit_buffer_to_user::where('HashID', $HashID)->where('active',2)->get();
        $buffer = buffer_user::select('start_subscrip', 'end_subscrip')->where('HashID', $HashID)->first();

        return response()->json([
            'buffer'=>$buffer,
            'historyBuffer'=>$profitBuffer->toArray(),
            ]);
                 
            }
}
