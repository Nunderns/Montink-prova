<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Cupom;
use App\Models\PedidoItem;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'cliente_id',
        'cupom_id',
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
}
