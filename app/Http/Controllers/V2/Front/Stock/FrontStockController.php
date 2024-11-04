<?php

namespace App\Http\Controllers\V2\Front\Stock;

use App\Http\Controllers\Controller;
use App\Models\stock;
use App\Models\contract_users_data;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


class FrontStockController extends Controller
{
    use ResponseJson;
    public function stock()
    {
        $stock = stock::where('active', 1)->latest()->first();
        return $this->success($stock);
    }

     public function checkVerified(Request $request)
    {
        // return "555";
        $user = auth('api')->user();
        $check = contract_users_data::where('user_id', $user->id)->first();
        return $this->success($check ? true : false);
    }
}
