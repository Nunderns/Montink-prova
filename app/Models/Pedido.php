<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Cupom;
use App\Models\PedidoItem;
use App\Models\UserAddress;
use App\Notifications\OrderPlaced;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class Pedido extends Model
{
    use HasFactory;
    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\OrderPlaced::class,
    ];

    protected $fillable = [
        'codigo',
        'cliente_id',
        'cupom_id',
        'user_address_id',
        'valor_total',
        'desconto',
        'valor_final',
        'frete',
        'forma_pagamento',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'frete' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function cupom()
    {
        return $this->belongsTo(Cupom::class);
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * Relacionamento com o endereço de entrega
     */
    public function enderecoEntrega()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }
    
    /**
     * Alias para compatibilidade com o código existente
     */
    public function endereco()
    {
        return $this->enderecoEntrega();
    }

    /**
     * Escopo para filtrar pedidos por status
     */
    public function scopeComStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Verifica se o pedido pode ser cancelado
     */
    public function podeSerCancelado()
    {
        return in_array($this->status, [
            'pending', 'processing',
            'pendente', 'em_processamento', 'pago', 'enviado'
        ]);
    }

    /**
     * Formata o valor para exibição
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_final, 2, ',', '.');
    }
    
    /**
     * Gera um resumo do pedido para exibição
     *
     * @return array
     */
    public function gerarResumo()
    {
        // Mapeamento de status para suas traduções
        $statusTraduzidos = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            'em_processamento' => 'Processando',
            'enviado' => 'Enviado',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
        ];
        
        $status = $statusTraduzidos[$this->status] ?? ucfirst($this->status);
        
        return [
            'codigo' => $this->codigo,
            'data' => $this->created_at->format('d/m/Y'),
            'status' => $status,
            'status_original' => $this->status,
            'total_itens' => $this->itens->sum('quantidade'),
            'valor_total' => 'R$ ' . number_format($this->valor_total, 2, ',', '.'),
            'desconto' => $this->desconto > 0 ? 'R$ ' . number_format($this->desconto, 2, ',', '.') : 'Nenhum',
            'valor_final' => 'R$ ' . number_format($this->valor_final, 2, ',', '.'),
            'forma_pagamento' => ucfirst($this->forma_pagamento),
            'endereco_entrega' => $this->enderecoEntrega ? 
                $this->enderecoEntrega->logradouro . ', ' . 
                $this->enderecoEntrega->numero . 
                ($this->enderecoEntrega->complemento ? ', ' . $this->enderecoEntrega->complemento : '') . 
                ' - ' . $this->enderecoEntrega->bairro . 
                ', ' . $this->enderecoEntrega->cidade . 
                '/' . $this->enderecoEntrega->estado : 'Não informado'
        ];
    }
}
