<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $endereco;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return void
     */
    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
        $this->endereco = $pedido->enderecoEntrega;
        
        // Log para depuração
        \Log::info('OrderConfirmation construído', [
            'pedido_id' => $pedido->id,
            'cliente_id' => $pedido->cliente_id,
            'endereco' => $this->endereco ? $this->endereco->toArray() : null
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            // Log antes de construir o e-mail
            \Log::info('Construindo e-mail de confirmação', [
                'pedido_id' => $this->pedido->id,
                'view' => 'emails.order-confirmation',
                'endereco' => $this->endereco ? 'presente' : 'ausente'
            ]);
            
            // Verificar se o template existe
            if (!view()->exists('emails.order-confirmation')) {
                throw new \Exception('Template de e-mail não encontrado: emails.order-confirmation');
            }
            
            // Construir o e-mail
            $email = $this->subject('Confirmação de Pedido #' . $this->pedido->codigo)
                        ->view('emails.order-confirmation')
                        ->with([
                            'pedido' => $this->pedido,
                            'endereco' => $this->endereco,
                        ]);
            
            \Log::info('E-mail construído com sucesso', ['pedido_id' => $this->pedido->id]);
            return $email;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao construir e-mail de confirmação: ' . $e->getMessage(), [
                'pedido_id' => $this->pedido->id,
                'exception' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
