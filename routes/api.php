<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\CepController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rota de teste para verificar se a API está funcionando
Route::get('/test', function () {
    return response()->json(['message' => 'API está funcionando!']);
});

// Webhook para atualização de status de pedidos
Route::post('/webhook/order-update', [WebhookController::class, 'handleOrderUpdate'])
    ->middleware(['api', 'verify.webhook.ip'])
    ->name('api.webhook.order-update');

// Consulta de CEP
Route::get('/cep/buscar', [CepController::class, 'buscarCep']);

// Outras rotas públicas da API podem ser adicionadas aqui

// Rotas protegidas (requerem autenticação)
Route::middleware(['auth:api'])->group(function () {
    // Rotas protegidas da API aqui
});
