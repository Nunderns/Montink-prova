<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event)
    {
        $pedido = $event->pedido;
        
        Log::info('Iniciando envio de e-mail de confirmação', [
            'pedido_id' => $pedido->id,
            'cliente_id' => $pedido->cliente_id,
            'email_cliente' => $pedido->cliente->email ?? 'não informado'
        ]);
        
        try {
            // Verifica se o cliente tem e-mail
            if (empty($pedido->cliente->email)) {
                throw new \Exception('Cliente não possui endereço de e-mail cadastrado');
            }
            
            // Log antes de enviar o e-mail
            Log::info('Preparando para enviar e-mail', [
                'pedido_id' => $pedido->id,
                'para' => $pedido->cliente->email,
                'endereco_entrega' => $pedido->enderecoEntrega ? 'presente' : 'ausente'
            ]);
            
            // Envia o e-mail para o cliente
            Mail::to($pedido->cliente->email)
                ->send(new OrderConfirmation($pedido));
                
            Log::info('E-mail de confirmação enviado com sucesso', [
                'pedido_id' => $pedido->id,
                'cliente' => $pedido->cliente->email,
                'endereco_entrega' => $pedido->enderecoEntrega ? $pedido->enderecoEntrega->toArray() : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de confirmação', [
                'pedido_id' => $pedido->id,
                'erro' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Relança a exceção para que o Laravel possa lidar com ela
            throw $e;
        }
    }
}
