<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Handle order status update webhook
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleOrderUpdate(Request $request)
    {
        // Validar os dados recebidos
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pedidos,id',
            'status' => 'required|string',
            'api_key' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            Log::error('Webhook validation failed', [
                'errors' => $validator->errors(),
                'payload' => $request->all(),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar a chave da API (opcional)
        $apiKey = $request->input('api_key');
        if ($apiKey && $apiKey !== config('services.webhook.api_key')) {
            Log::warning('Tentativa de acesso não autorizado ao webhook', [
                'ip' => $request->ip(),
                'api_key' => $apiKey
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $pedidoId = $request->input('id');
        $status = strtolower($request->input('status'));

        try {
            $pedido = Pedido::withTrashed()->findOrFail($pedidoId);

            Log::info('Processando webhook', [
                'pedido_id' => $pedidoId,
                'status' => $status,
                'ip' => $request->ip()
            ]);

            if ($status === 'cancelado') {
                // Se já estiver cancelado, não faz nada
                if ($pedido->trashed()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pedido já estava cancelado',
                        'pedido_id' => $pedidoId
                    ]);
                }

                // Restaurar estoque dos itens antes de deletar
                foreach ($pedido->itens as $item) {
                    $item->produto->increment('quantidade_estoque', $item->quantidade);
                }

                $pedido->delete();
                Log::info("Pedido #{$pedidoId} removido via webhook");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido cancelado e removido com sucesso',
                    'pedido_id' => $pedidoId
                ]);
            } else {
                // Se estiver cancelado, restaurar o pedido
                if ($pedido->trashed()) {
                    $pedido->restore();
                }

                    // Validar o status antes de atualizar
                $statusValidos = ['pendente', 'pago', 'em_processamento', 'enviado', 'entregue', 'cancelado'];
                
                if (!in_array($status, $statusValidos)) {
                    Log::error('Status inválido recebido no webhook', [
                        'pedido_id' => $pedidoId,
                        'status_recebido' => $status,
                        'status_validos' => $statusValidos
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Status inválido',
                        'status_recebido' => $status,
                        'status_validos' => $statusValidos
                    ], 400);
                }
                
                // Mapear status válidos para garantir compatibilidade
                $statusMap = [
                    'pendente' => 'pendente',
                    'pago' => 'pago',
                    'em_processamento' => 'em_processamento',
                    'enviado' => 'enviado',
                    'entregue' => 'entregue',
                    'cancelado' => 'cancelado'
                ];
                
                // Usar o status mapeado ou 'pendente' como padrão
                $mappedStatus = $statusMap[strtolower($status)] ?? 'pendente';
                
                // Atualizar usando o método save() do modelo
                $pedido->status = $mappedStatus;
                $pedido->save();
                
                // Recarregar o modelo para garantir que temos os dados atualizados
                $pedido->refresh();
                
                Log::info("Status do pedido #{$pedidoId} atualizado para: {$status}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Status do pedido atualizado com sucesso',
                    'pedido_id' => $pedidoId,
                    'novo_status' => $status
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pedido_id' => $pedidoId,
                'status' => $status,
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a requisição',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
