<?php

namespace App\Http\Controllers\V2\Recomindation;

use App\Models\expert;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StopRecomindationRequest;

class StopRecomindationController extends Controller
{
    use ResponseJson;
    public function StopRecomindationStillWorks(StopRecomindationRequest $request)
    {
        $recomindationId=$request['recomindationId'];
        $expert=expert::where('recomondations_id',$recomindationId)->first();
        if($expert)
        {
           $expert->status=-1;
           $expert->status_btc=1;
           $expert->save();
           return $this->success('The recommendation has been stopped');
        }else{
            return $this->error('The recommendation has been error');

        }


    }
}
