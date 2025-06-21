<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;

class Cupom extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descricao',
        'tipo_desconto',
        'valor_desconto',
        'validade',
        'usos_maximo',
        'usos_atual',
        'ativo',
    ];

    protected $casts = [
        'valor_desconto' => 'decimal:2',
        'validade' => 'datetime',
        'usos_maximo' => 'integer',
        'usos_atual' => 'integer',
        'ativo' => 'boolean',
    ];

    protected $dates = [
        'validade',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function podeSerUtilizado()
    {
        if (!$this->ativo) {
            return false;
        }

        if ($this->validade->isPast()) {
            return false;
        }

        if ($this->usos_maximo !== null && $this->usos_atual >= $this->usos_maximo) {
            return false;
        }

        return true;
    }

    public function registrarUso()
    {
        $this->increment('usos_atual');
    }
}
