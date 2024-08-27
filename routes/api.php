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

// Rotas de leilões
Route::apiResource('/leilao', ApiLeilaoController::class);
Route::get('/leilao/{id}', [ApiLeilaoController::class, 'show']);
Route::get('leilao/{id}/lances', [ApiLeilaoController::class, 'showLeilaoELances']);

// Rotas de usuários
Route::apiResource('/users', ApiUsersController::class);
Route::get('/users/{id}', [ApiUsersController::class, 'show']);

// Rotas de lances
Route::apiResource('/lances', ApiLanceController::class);
Route::get('/lances/{id}', [ApiLanceController::class, 'show']);

// Enpoint de encerramento de leilões expirados
Route::post('/leilao/encerra-expirados', [ApiLeilaoController::class, 'encerraLeiloes']);
Route::post('/leilao/{id}/finaliza', [ApiLeilaoController::class, 'finalizaLeilao']);

