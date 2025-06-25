<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Cupom;
use App\Models\PedidoItem;
use App\Models\UserAddress;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'cliente_id',
        'cupom_id',
        'user_address_id',
        'valor_total',
        'desconto',
        'valor_final',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
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
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Formata o valor para exibição
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_final, 2, ',', '.');
    }
}
