<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/test-webhook', function () {
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'X-API-Key' => env('WEBHOOK_API_KEY')
    ])->post('http://localhost:8000/api/webhook/order-update', [
        'id' => 1, // ID do pedido para teste
        'status' => 'em_processamento',
        'test' => true
    ]);

    return [
        'status' => $response->status(),
        'response' => $response->json()
    ];
});
