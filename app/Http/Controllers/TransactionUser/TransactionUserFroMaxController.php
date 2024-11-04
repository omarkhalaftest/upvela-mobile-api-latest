<?php

namespace App\Http\Controllers\TransactionUser;

use Illuminate\Support\Str;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Models\transactionUser;
use App\Models\profit_blanace_user;
use App\Http\Controllers\Controller;
use App\Http\Requests\transfer\amountRequest;
use App\Http\Controllers\Helper\NotficationController;

class TransactionUserFroMaxController extends Controller
{
    use ResponseJson;
    protected $notificationController;

    public function __construct(NotficationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }


    public function transferToFess(amountRequest $request)
    {
        $user = auth('api')->user();
        $amount = $request->input('amount');
        $balanceMax = profit_blanace_user::where('user_id', $user->id)->latest()->first();

        if (!$balanceMax && $amount > $balanceMax->blance) {
            return $this->errorDtat("You don't have enough money");
        }
        
          if ($amount > $balanceMax->blance) {
            return $this->errorDtat("You don't have enough money");
        }

        $this->subtractMoneyForMax($balanceMax, $amount);

        if ($request->input('type') === "fees") {
           $this->addPointsToUser($user, $amount);
            $type = "profit";
        } else {
             $this->addMoneyToUser($user, $amount);
            $type = "fess";
        }


        $this->storeTransaction($user->id, $user->id, $amount, $type);

        $messageSend = "تم تحويل المبلغ الى محفظتك بنجاح";
        $result = $this->notificationController->notfication($user->fcm_token, $messageSend);

        return $this->successDtat("The transfer was successful");
    }

    protected function subtractMoneyForMax($balanceMax, $amount)
    {
        $balanceMax->blance -= $amount;
        $balanceMax->save();
    }

    protected function addMoneyToUser($user, $amount)
    {
        $user->money += $amount;
        $user->save();
    }

    protected function addPointsToUser($user, $amount)
    {
        $user->number_points += $amount;
        $user->save();
    }


    public function storeTransaction($userId, $reciveId, $amount, $name)
    {
        $randomString = Str::random(20);
        $randomNumber = mt_rand(1000, 9999);
        $uniqueCode = $randomString . $randomNumber;

        $transactionUser = transactionUser::create([
            'user_id' => $userId,
            'recive_id' => $reciveId,
            'amount' => $amount,
            'name' => $name,
            'transaction_id' => $uniqueCode,

        ]);
    }
}
