<?php

namespace App\Http\Controllers\V2\Dashbord\ProfitMax_Show;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\precentage_puffers;

use App\Http\Resources\Upvela_Max\Divie_Buffer\precentageBufferResource;
use App\Models\buffer;
use App\Models\buffer_user;
use App\Traits\ResponseJson;

class DivitebuferactiveController extends Controller
{
    use ResponseJson;
    public function profitbufferActive(Request $request)
    {
        if($request['active']=="active")
        {
            $precentage_puffers=precentage_puffers::where('is_active',1)->get();
            $precentage_puffers->each(function($item){
            $buufer=buffer::find($item->plan_id);
            $item->buffer_name=$buufer->name;
            });
              return precentageBufferResource::collection($precentage_puffers);
        }else{
            $precentage_puffers=precentage_puffers::get();
            $precentage_puffers->each(function($item){
            $buufer=buffer::find($item->plan_id);
            $item->buffer_name=$buufer->name;
            });
              return precentageBufferResource::collection($precentage_puffers);
        }


    }

    public function totlemony_inBuufers()
    {
      $totlemoney=buffer_user::sum('amount');
     return $this->success($totlemoney);
    }

}
