<?php
 use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V2\Upvela_Max\Baffer\App\bafferController;
use App\Http\Controllers\V2\Upvela_Max\Baffer\App\ActivateBafferController;
use App\Http\Controllers\V2\Upvela_Max\Baffer\App\historyBufferController;
use App\Http\Controllers\V2\Upvela_Max\Baffer\App\AllSubscribeBafferController;
use App\Http\Controllers\V2\Upvela_Max\Baffer\App\PlanBafferController;
use App\Http\Controllers\Helper\OTP\OtpRegisterController;
use App\Http\Controllers\limitBinance\buyController;
 use App\Http\Controllers\V2\Dashbord\ProfitMax\divite_user_bufferController;
 use App\Http\Controllers\V2\Dashbord\ProfitMax\bufer_precantageController;
 use App\Http\Controllers\V2\Dashbord\ProfitMax\divite_bufferController;
 use App\Http\Controllers\V2\Dashbord\ProfitMax\push_profitController;
 use App\Http\Controllers\V2\Dashbord\ProfitMax_Show\DivitebuferactiveController;
use App\Http\Controllers\V2\Dashbord\ProfitMax_Show\divirbuffer_fordayController;
use App\Http\Controllers\V2\Dashbord\ProfitMax_Show\divirbuffer_for_userController;
use App\Http\Controllers\V2\Upvela_Max\Baffer\App\Afflite_maxController;
use App\Http\Controllers\V2\Upvela_Max\Baffer\App\HistoryProfitBufferController;










Route::middleware(['EnsureTokenIsValid'])->group(function () {

 Route::post('all_buffer',[bafferController::class,'getAllBaffer']);
 Route::post('one_buffer',[bafferController::class,'getOneBuffer']); // delete it when push update
  Route::post('one_buffer_new',[bafferController::class,'getOneBufferNew']);

 Route::middleware('Threeminute')->group(function () {

  Route::post('activate_buffer',[ActivateBafferController::class,'activate']);
  
 });
 
 Route::post('history_buffer',[historyBufferController::class,'history']);
 Route::post('profit-history-buffer', [HistoryProfitBufferController::class, 'history']);

 
 Route::post('plan_bufer',[PlanBafferController::class,'plans']);
 Route::post('all_buffers_subscribe',[AllSubscribeBafferController::class,'allSubscribe']);
  Route::post('all_buffer_plans',[PlanBafferController::class,'plan_buffer']);
  Route::post('t',[OtpRegisterController::class,'index']);
});
  Route::post('buy',[buyController::class,'buy']);

  
  
  
      
// Route::middleware('SuperAdmin')->group(function () {
    
// Route::post('divit_precantage_buffer_index',[bufer_precantageController::class,'index']);  // first-step-divite-precantage
// Route::post('divit_for_buffer',[divite_bufferController::class,'index']); // secound-step get money for one day
// Route::post('divite_for_user',[divite_user_bufferController::class,'store_profit_to_user']);// three step its very danger
// Route::post('push_profit_max',[push_profitController::class,'push']);





// ///////////////////////////////////////////show/////////////////////////////////
// Route::post("precentage_puffers_show",[DivitebuferactiveController::class,'profitbufferActive']);
// Route::post('buffer_for_day',[divirbuffer_fordayController::class,'onedayInBuffer']);
// Route::post('divite_for_user_show',[divirbuffer_for_userController::class,'divite_for_user']);
// Route::post("all_mony_for_buffers",[DivitebuferactiveController::class,'totlemony_inBuufers']);


    
// });
    
  
  
  // afflite max
  
  Route::post('afflite_max',[Afflite_maxController::class,'afflite_max']);

  



