<?php

use App\Http\Controllers\ConfigController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KairoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/kairo', [KairoController::class, 'handleMessage']);
Route::get('/config', [ConfigController::class, 'getConfig']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
