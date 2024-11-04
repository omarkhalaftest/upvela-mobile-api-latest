<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MYBoot\EmaBootController;
use App\Http\Controllers\MYBoot\ScalpinghelfController;
use App\Http\Controllers\MYBoot\EmabuysellnowController;
use App\Http\Controllers\MYBoot\TargetHalfController;
use App\Http\Controllers\MYBoot\TargetHalfHalfController;
use App\Http\Controllers\BootCaptian\BotRealtedWithExportBptController;
use App\Http\Controllers\MYBoot\TargetOneSecoundHelfController;
use App\Http\Controllers\limitBinance\sellLimitController;






 
Route::post('ahmed',[EmaBootController::class,'updatedAfflite']);
Route::post('TargerOne',[TargetHalfController::class,'index']);
Route::post('TargerHelf',[TargetHalfHalfController::class,'index']);

Route::post('TargetFreeHelf',[TargetOneSecoundHelfController::class,'index']); // for helf



 Route::post('realted',[BotRealtedWithExportBptController::class,'index']);
 Route::get('getCaptain',[BotRealtedWithExportBptController::class,'getCaptian']);



Route::post('EmaBuySell',[EmabuysellnowController::class,'updatedAfflite']);


 
Route::post('scalping1/2',[ScalpinghelfController::class,'updatedAfflite']);





 Route::post("limit_test_send",[sellLimitController::class,'send']);
  Route::post("limit_test",[sellLimitController::class,'sell']);




















