<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiLeilaoController;
use App\Http\Controllers\Api\ApiUsersController;
use App\Http\Controllers\Api\ApiLanceController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/health-check', [ApiLeilaoController::class, 'healthCheck']);
Route::apiResource('/leilao', ApiLeilaoController::class);
Route::get('/leilao/{id}', [ApiLeilaoController::class, 'show']);

// Rotas de usuários
Route::apiResource('/users', ApiUsersController::class);
Route::get('/users/{id}', [ApiUsersController::class, 'show']);

// Rotas de lances
Route::apiResource('/lances', ApiLanceController::class);
Route::get('/lances/{id}', [ApiLanceController::class, 'show']);


// Route::get('/api/leilao/check-access', 'App\Http\Controllers\api\LeilaoApiController@checkAccess');
// Route::get('/api/leilao', 'App\Http\Controllers\api\LeilaoApiController@index');
