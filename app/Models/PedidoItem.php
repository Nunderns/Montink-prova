<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Estoque;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'produto_id',
        'variacao_id',
        'variacao',
        'quantidade',
        'preco_unitario',
        'total',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function variacao()
    {
        return $this->belongsTo(Estoque::class, 'variacao_id');
    }
}
