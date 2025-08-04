<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\FaqController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KairoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/


Route::post('/kairo', [KairoController::class, 'handleMessage']);
Route::get('/config', [ConfigController::class, 'getConfig']);
Route::post('/verify-token', [ApiTokenController::class, 'verifyToken']);
Route::get('/faqs', [FaqController::class, 'apiIndex']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
