<?php

namespace App\Http\Controllers\V2\Upvela_Max\Baffer\App;

use App\Models\plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Upvela_Max\buffer\PlanBuffersResourece;
use App\Models\buffer;
use App\Models\buffer_plan;
use App\Http\Requests\Upvela_Max\buffer\AllBufferInPlansRequest;
use App\Http\Resources\Upvela_Max\buffer\AllBuffersResourece;


class PlanBafferController extends Controller
{
    public function plans()
    {
         $plans=plan::whereNotIn('id', [1, 7])->get();
         $plans->each(function($items){

            $count_bufer=buffer_plan::where('plan_id',$items->id)->count();
            $items->buffer=$count_bufer;
         });

         return  PlanBuffersResourece::collection($plans);

    }
    
      public function plan_buffer(AllBufferInPlansRequest $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return $this->token();
        }
        $buffer_plan = buffer_plan::where('plan_id', $request['plan_id'])->pluck('buffer_id');
        $buffer = buffer::whereIn('id', $buffer_plan)->get();
        $buffer->each(function ($buffer) use ($user) {
            $buffer->count_sub = 0;
        });

        return  AllBuffersResourece::collection($buffer);
    }
}
