<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Deposits\WithdrwController;
use App\Http\Controllers\Deposits\DepositsController;
use App\Http\Controllers\Deposits\DepositsUserController;
use App\Http\Controllers\Deposits\subscribPlanByDepositsController;
use App\Http\Controllers\TransactionUser\TransactionUserController;
use App\Http\Controllers\TransactionUser\TransactionUserFroMaxController;


Route::middleware('EnsureTokenIsValid')->group(function () {

Route::post('sendMony',[TransactionUserController::class,'oneToOne']);
Route::post('mySelf',[TransactionUserController::class,'mySelf']);
Route::post('sendMonybyFess',[TransactionUserController::class,'oneToOnebyFess']);
Route::post('mySelfbyFess',[TransactionUserController::class,'mySelfbyfess']);
Route::post('transfer_to_fess', [TransactionUserFroMaxController::class, 'transfertofess']); // for max




Route::get('historyTransaction',[TransactionUserController::class,'historyTransaction']);
Route::post('historyTransactionWeb',[TransactionUserController::class,'historyTransactionWeb']);
});

Route::get('getDeposits',[DepositsController::class,'getDeposits']);
Route::get('wallte',[DepositsController::class,'walteaddress']);

Route::post('Withdrw',[WithdrwController::class,'withdraw']);
Route::get('getUSDTBalance',[WithdrwController::class,'getUSDTBalance']);




// Deopsite for user
Route::POST('checkTextID',[DepositsUserController::class,'cheakTextID']);
Route::POST('historyDeposit',[DepositsUserController::class,'historyDeposit']);


// subscrib to plan by feez


Route::post('subplan',[subscribPlanByDepositsController::class,'subscrib']);