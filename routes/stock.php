<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V2\Front\Stock\FrontStockController;
use App\Http\Controllers\V2\Front\Stock\makeContractController;
use App\Http\Controllers\V2\Front\Stock\UploadDataForContractController;





Route::middleware('EnsureTokenIsValid')->group(function () {


    Route::get('stock', [FrontStockController::class, 'stock']);
    Route::post('check-verified-stock', [FrontStockController::class, 'checkVerified']);

    Route::post('upload-user-data', [UploadDataForContractController::class, 'upload']);

    Route::post('test', [makeContractController::class, 'uploadFile']);
});
