<?php

namespace App\Http\Controllers\TransactionUser;
use App\Models\bot;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\transactionUser;
use PhpParser\Node\Stmt\Return_;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Binance\buyController;
use App\Http\Controllers\Helper\NotficationController;
use App\Http\Requests\transfer\amountRequestForTrade;
use App\Http\Requests\transfer\amountRequest;



class TransactionUserController extends Controller
{

    protected $notificationController;

    public function __construct(NotficationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }


    public function oneToOne(amountRequestForTrade $request)
    {
return response()->json([
            'success' => false,
            'message' => "not available now"
        ]);


        // Get the authenticated user
         $user = auth('api')->user();
        $affiliateCode = $request->input('affiliateCode'); // Corrected variable name
        $amount = (int)$request->input('amount'); // Make sure it's an integer
        $receiver = User::where('affiliate_code', $affiliateCode)->first();

        
        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => "Affiliate Code Not Found"
            ]);
        }

        // Check if the authenticated user has enough money
        if ($amount > $user->money) {
            return response()->json([
                'success' => false,
                'message' => "Not enough money"
            ]);
        }

        // Perform the transaction
        $user->money -= $amount;
        $user->save();


        $receiver->number_points += $amount;

        $receiver->save();


        $this->Store($user->id, $receiver->id, $receiver->name, $amount);


        // Call the notfication method
        $massageSend = "تم تحويل المبلغ بنجاح الي $receiver->name";
        $result = $this->notificationController->notfication($user->fcm_token, $massageSend);

        $massageRecived = "تم تحويل مبلغ وقدره $$amount من $user->name ";
        $results = $this->notificationController->notfication($receiver->fcm_token, $massageRecived);


        return response()->json([
            'success' => true,
            'message' => "Transaction successful"
        ]);
    }
    
    public function oneToOnebyFess(amountRequestForTrade $request)
    {
 return response()->json([
            'success' => false,
            'message' => "not available now"
        ]);

 
        // Get the authenticated user
         $user = auth('api')->user();
        $affiliateCode = $request->input('affiliateCode'); // Corrected variable name
        $amount = (int)$request->input('amount'); // Make sure it's an integer
        $receiver = User::where('affiliate_code', $affiliateCode)->first();

    
        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => "Affiliate Code Not Found"
            ]);
        }
        
      

        // Check if the authenticated user has enough money
        if ($amount > $user->number_points) {
            return response()->json([
                'success' => false,
                'message' => "Not enough money"
            ]);
        }
        
                  if ($user->id === $receiver->id) {
           return response()->json([
            'success' => false,
            'message' => "You cannot send points to yourself"
        ]);
        }

        // Perform the transaction
        $user->number_points -= $amount;
        $user->save();


        $receiver->number_points += $amount;
        $receiver->save();


        $this->Store($user->id, $receiver->id, $receiver->name, $amount);


        // Call the notfication method
        $massageSend = "تم تحويل المبلغ بنجاح الي $receiver->name";
        $result = $this->notificationController->notfication($user->fcm_token, $massageSend);

        $massageRecived = "تم تحويل مبلغ وقدره $$amount من $user->name ";
        $results = $this->notificationController->notfication($receiver->fcm_token, $massageRecived);


        return response()->json([
            'success' => true,
            'message' => "Transaction successful"
        ]);
    }


    public function mySelf(amountRequest $request)
    {
        return response()->json([
            'success' => false,
            'message' => "not available now"
        ]);
        // Get the authenticated user
        $user = auth('api')->user();
        $amount = $request->input('amount'); // Make sure it's an integer
        if ($amount > $user->money) {
            return response()->json([
                'success' => false,
                'message' => "Not enough money"
            ]);
        }

        $user->money -= $amount;
        $user->number_points += $amount;
        $user->save();




        $this->Store($user->id, $user->id, $user->name = "Me", $amount);
        $massageSend = "تم تحويل المبلغ الي محفظتك بنجاح";
        $result = $this->notificationController->notfication($user->fcm_token, $massageSend);
        return response()->json([
            'success' => true,
            'message' => "Transaction successful"
        ]);
    }
    
     public function mySelfbyfess(amountRequest $request)
    {
        
        return response()->json([
            'success' => false,
            'message' => "not available now"
        ]);
    
        // Get the authenticated user
        $user = auth('api')->user();
        $amount = $request->input('amount'); // Make sure it's an integer
        if ($amount > $user->number_points) {
            return response()->json([
                'success' => false,
                'message' => "Not enough money"
            ]);
        }

        $user->number_points -= $amount;
        $user->money += $amount;
        $user->save();




        $this->Store($user->id, $user->id, $user->name = "Me", $amount);
        $massageSend = "تم تحويل المبلغ الي محفظتك بنجاح";
        $result = $this->notificationController->notfication($user->fcm_token, $massageSend);
        return response()->json([
            'success' => true,
            'message' => "Transaction successful"
        ]);
    }


    public function historyTransaction(Request $request)
    {
        $user = auth('api')->user();

        $sentTransactions = transactionUser::where('user_id', $user->id)->get();
        $receivedTransactions = transactionUser::where('recive_id', $user->id)->get();

        // send
        $sentTransactions->each(function ($transaction) {
            $transaction->transaction_type = 'sent';

            if ($transaction->user_id == $transaction->recive_id) {
                $transaction->send_name = "Me";
            } else {
                $getname = User::find($transaction->recive_id);
                if ($getname !== null) {
                    $transaction->send_name = $getname->name;
                } else {
                    $transaction->send_name = 'Deleted Acount';
                }
            }

            // Add 3 hours to the created_at attribute
            $transaction->created_at = $transaction->created_at->addHours(3);
        });


        // received
        $receivedTransactions->each(function ($transaction) {
            $transaction->transaction_type = 'received';

            if ($transaction->user_id == $transaction->recive_id) {
                $transaction->receiver_name = 'Me';
            } else {
                $getname = User::find($transaction->user_id);
                if ($getname !== null) {
                    $transaction->receiver_name = $getname->name;
                } else {
                    $transaction->receiver_name = 'Deleted Acount';
                }
            }

            // Add 3 hours to the created_at attribute
            $transaction->created_at = $transaction->created_at->addHours(3);
        });

        $mergedTransactions = $sentTransactions->concat($receivedTransactions);
        $mergedTransactions = $mergedTransactions->sortByDesc('created_at')->values();

        // for fess Bot
        $is_Deposits = $user->DepositsBinance->each(function ($define) {
            $define->type = "is_Deposits";
            // Add 3 hours to the created_at attribute
            $define->created_at = $define->created_at->addHours(3);
        });

        $is_fess = $user->fessBot->each(function ($define) {
            $define->type = "is_fess";

            if ($define->number_bot == null) {
                $define->side = "plan";

            } else {
                $define->side = "bot";
                $getname=bot::select('bot_name')->where('id',$define->number_bot)->first();
                $define->botName= $getname->bot_name;
            }
            // Add 3 hours to the created_at attribute
            $define->created_at = $define->created_at->addHours(3);
        });

        $fess = $is_Deposits->concat($is_fess);
        $sendfess = $fess->sortByDesc('created_at')->values();

        $client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);

        $buyController = new buyController($client);

        // Call the getstatsOrde method
        $buyController->getAllOrder($request);

        $user->load(['BuySellBinance', 'DepositsBinance', 'historypayment', 'fessBot']);
        $user->BuySellBinance->each(function ($item) {
            $item->created_at = $item->created_at->addHours(3);
        });
        $user->historypayment->each(function ($item) {
            $item->created_at = $item->created_at->addHours(3);
        });
        $responseData = [
            'transactions' => $mergedTransactions,
            'historypayment' => $user->historypayment,
            'BuySellBinance' => $user->BuySellBinance,
            'DepositsBinance' => $sendfess,
        ];

        return $responseData;
    }


    public function Store($userId, $reciveId, $name, $amount)
    {

        $randomString = Str::random(20);
        $randomNumber = mt_rand(1000, 9999);
        $uniqueCode = $randomString . $randomNumber;

        $transactionUser = transactionUser::create([
            'user_id' => $userId,
            'recive_id' => $reciveId,
            'amount' => $amount,
            'transaction_id' => $uniqueCode,

        ]);
    }
    
    
        public function historyTransactionWeb(Request $request)
    {
        $user_id = $request->id; 
   $user = User::find($user_id);
        $sentTransactions = transactionUser::where('user_id', $user_id)->get();

        $receivedTransactions = transactionUser::where('recive_id', $user_id)->get();


        $sentTransactions->each(function ($transaction) {
            $transaction->transaction_type = 'sent';

            if ($transaction->user_id == $transaction->recive_id) {
                $transaction->send_name = "Me";
            } else {
                $transaction->send_name = User::select('name')->find($transaction->recive_id);
            }
        });

        $receivedTransactions->each(function ($transaction) {
            $transaction->transaction_type = 'received';

            if ($transaction->user_id == $transaction->recive_id) {
                $transaction->receiver_name = 'Me';
            } else {
                $transaction->receiver_name = User::select('name')->find($transaction->user_id);
            }
        });
        $mergedTransactions = $sentTransactions->concat($receivedTransactions);
        $mergedTransactions = $mergedTransactions->sortByDesc('created_at')->values();

        // for fess Bot it
        $is_Deposits = $user->DepositsBinance->each(function ($define) {
            $define->type = "is_Deposits";
        });
        $is_fess = $user->fessBot->each(function ($define) {
            $define->type = "is_fess";
            if ($define->number_bot == null) {
                $define->side = "plan";
            } else {
                $define->side = "bot";
            }
        });

        $fess = $is_Deposits->concat($is_fess);
        $sendfess = $fess->sortByDesc('created_at')->values();



        $client = new Client([
            'base_uri' => 'https://api.binance.com',
        ]);

        $buyController = new buyController($client);

        // Call the getstatsOrde method
        $buyController->getAllOrder($request);




          $user->load(['BuySellBinance', 'DepositsBinance', 'historypayment', 'fessBot','binanceloges','historyAllProfit','MarktingAllProfit']);


    return    $responseData = [
            'transactions' => $mergedTransactions,
            'historypayment' => $user->historypayment,
            'BuySellBinance' => $user->BuySellBinance,
            'DepositsBinance' => $sendfess,
            'binanceloges'=>$user->binanceloges,
            'historyAllProfit'=>$user->historyAllProfit,
            'MarktingAllProfit'=>$user->MarktingAllProfit,
        ];

    }
}
