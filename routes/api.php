<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IntegrationController;

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


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/integrations', [IntegrationController::class,'getIntegration']);
    Route::post('/integrations', [IntegrationController::class,'addIntegration']);
    Route::get('/integration/{_id}/updateName', [IntegrationController::class, 'updateName']);
    Route::get('/integration/{_id}/update', [IntegrationController::class, 'update']);

});