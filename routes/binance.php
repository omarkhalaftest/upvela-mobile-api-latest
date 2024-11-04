<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Binance\buyController;
use App\Http\Controllers\Helper\BuyMarketController;
use App\Http\Controllers\Deposits\DepositsController;



Route::post('binance', [buyController::class, 'buy']);
Route::get('getAllOrders', [buyController::class, 'getAllOrder']);
Route::get('statusOrder', [buyController::class, 'getStatusOrder']);

Route::get('deposite',[DepositsController::class,'getDeposits']);
  

Route::post('buyMarket',[BuyMarketController::class,'buy']);

Route::post('getBlance',[BuyMarketController::class,'getBlance']);

