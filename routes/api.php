<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\PaymentController;

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

// For User
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/integrations', [IntegrationController::class,'index']);
    Route::post('/integrations', [IntegrationController::class,'addIntegration']);
    Route::get('/integration/{_id}/updateName', [IntegrationController::class, 'updateName']);
    Route::get('/integration/{_id}/update', [IntegrationController::class, 'update']);
    Route::get('/integration/{_id}', [IntegrationController::class, 'getIntegration']);
    Route::delete('/integration/{_id}/delete', [IntegrationController::class, 'delete']);
    Route::get('/payments/owner', [PaymentController::class, 'getPaymentForOwner']);
    Route::get('/payments/integration', [PaymentController::class, 'getPaymentForIntegration']);
    Route::get('/tokenIn/integration', [PaymentController::class, 'getTokenInForIntegration']);
    Route::get('/tokenIn/owner', [PaymentController::class, 'getTokenInForOwner']);
});


// for Admin
Route::get('/payments', [PaymentController::class, 'index']);

// for library
Route::post('/payments', [PaymentController::class, 'store']);
