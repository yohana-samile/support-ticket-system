<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\SassAppController;
use App\Http\Controllers\Api\SenderIdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('sender_id')->group(function () {
        Route::post('/store', [SenderIdController::class, 'store']);
        Route::get('/list', [SenderIdController::class, 'index']);
        Route::get('/show/{id}', [SenderIdController::class, 'show']);
        Route::put('/update/{id}', [SenderIdController::class, 'update']);
        Route::delete('/destroy/{id}', [SenderIdController::class, 'destroy']);
    });

    Route::prefix('saas_app')->group(function () {
        Route::post('/store', [SassAppController::class, 'store']);
        Route::get('/list', [SassAppController::class, 'index']);
        Route::get('/show/{id}', [SassAppController::class, 'show']);
        Route::put('/update/{id}', [SassAppController::class, 'update']);
        Route::delete('/destroy/{id}', [SassAppController::class, 'destroy']);
    });

    Route::prefix('client')->group(function () {
        Route::post('/store', [ClientController::class, 'store']);
        Route::get('/list', [ClientController::class, 'index']);
        Route::get('/show/{id}', [ClientController::class, 'show']);
        Route::put('/update/{id}', [ClientController::class, 'update']);
        Route::delete('/destroy/{id}', [ClientController::class, 'destroy']);
    });
//});
