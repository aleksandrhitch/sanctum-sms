<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthAdminController;
use App\Http\Controllers\Api\AuthClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('login', [AuthAdminController::class, 'login']);
    });

    Route::prefix('client')->group(function () {
        Route::post('send-sms-code', [AuthClientController::class, 'sendSmsCode']);
        Route::post('login', [AuthClientController::class, 'login']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('get-admin', [AuthAdminController::class, 'getAdmin']);
    Route::post('get-client', [AuthClientController::class, 'getClient']);
});
