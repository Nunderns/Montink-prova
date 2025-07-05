<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestWebhookController extends Controller
{
    public function testWebhook()
    {
        // Obter o pedido mais recente para teste
        $pedido = Pedido::latest()->first();
        
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum pedido encontrado para teste.'
            ]);
        }
        
        $url = url('/api/webhook/order-update');
        $apiKey = config('services.webhook.api_key');
        
        // Dados para o webhook
        $data = [
            'id' => $pedido->id,
            'status' => 'em_processamento',
            'api_key' => $apiKey
        ];
        
        try {
            // Fazer a requisição para o webhook
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->post($url, $data);
            
            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
                'request' => [
                    'url' => $url,
                    'method' => 'POST',
                    'headers' => [
                        'Accept' => 'application/json',
                        'X-API-Key' => '*** (oculto)'
                    ],
                    'body' => array_merge($data, ['api_key' => '*** (oculto)'])
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao chamar o webhook',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function testCancelOrder()
    {
        // Obter o pedido mais recente para teste
        $pedido = Pedido::latest()->first();
        
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum pedido encontrado para teste.'
            ]);
        }
        
        $url = url('/api/webhook/order-update');
        $apiKey = config('services.webhook.api_key');
        
        // Dados para cancelar o pedido
        $data = [
            'id' => $pedido->id,
            'status' => 'cancelado',
            'api_key' => $apiKey
        ];
        
        try {
            // Fazer a requisição para o webhook
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->post($url, $data);
            
            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'response' => $response->json(),
                'request' => [
                    'url' => $url,
                    'method' => 'POST',
                    'headers' => [
                        'Accept' => 'application/json',
                        'X-API-Key' => '*** (oculto)'
                    ],
                    'body' => array_merge($data, ['api_key' => '*** (oculto)'])
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao chamar o webhook',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
