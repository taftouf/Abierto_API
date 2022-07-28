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

Route::post('/login', [AuthController::class, 'login']);

// Route::post('/token/insert', [TokenController::class, 'insert']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/integrations', [IntegrationController::class,'index']);
    Route::post('/integrations', [IntegrationController::class,'addIntegration']);
    Route::get('/integration/{_id}/updateName', [IntegrationController::class, 'updateName']);
    Route::get('/integration/{_id}/update', [IntegrationController::class, 'update']);
    Route::get('/integration/{_id}', [IntegrationController::class, 'getIntegration']);
    Route::delete('/integration/{_id}/delete', [IntegrationController::class, 'delete']);
});

Route::post('/payments', [PaymentController::class, 'store']);
