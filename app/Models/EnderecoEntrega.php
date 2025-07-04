<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Pedido;

class EnderecoEntrega extends Model
{
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pedido_id',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'referencia',
    ];

    /**
     * Obtém o pedido associado a este endereço de entrega.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Formata o endereço completo para exibição.
     */
    public function getEnderecoCompletoAttribute(): string
    {
        $endereco = "{$this->logradouro}, {$this->numero}";
        
        if ($this->complemento) {
            $endereco .= " - {$this->complemento}";
        }
        
        $endereco .= " - {$this->bairro}, {$this->cidade}/{$this->estado} - CEP: {$this->cep}";
        
        if ($this->referencia) {
            $endereco .= " (Referência: {$this->referencia})";
        }
        
        return $endereco;
    }

    /**
     * Formata o endereço em uma única linha.
     */
    public function getEnderecoResumidoAttribute(): string
    {
        return "{$this->logradouro}, {$this->numero}, {$this->bairro} - {$this->cidade}/{$this->estado}";
    }
}
