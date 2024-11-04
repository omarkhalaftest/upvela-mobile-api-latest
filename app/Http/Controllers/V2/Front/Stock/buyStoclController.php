<?php

namespace App\Http\Controllers\V2\Front\Stock;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\stock;
use App\Traits\ResponseJson;

class buyStoclController extends Controller
{
    use ResponseJson;
    public function buy(Request $request)
    {
        $stockId = 1;
        $quantity = 6;
        $user = auth('api')->user();

        try {
            // Check if the user has enough balance
            if (!$this->hasSufficientBalance($user->id, $stockId, $quantity)) {
                return $this->error("Insufficient balance");
            }
            // Check if there are enough stocks available
            if (!$this->hasEnoughStock($stockId, $quantity)) {
                return $this->error("Not enough stocks available");
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    protected function hasSufficientBalance($userId, $stockId, $quantity)
    {
        return  $user = User::findOrFail($userId);
        $stock = Stock::findOrFail($stockId);

        $totalPrice = $stock->price * $quantity;

        return $user->money >= $totalPrice;
    }


    protected function hasEnoughStock($stockId, $quantity)
    {
        $stock = Stock::findOrFail($stockId);

        return $stock->limit >= $quantity;
    }
}
