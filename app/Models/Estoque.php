<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;
use App\Models\PedidoItem;

class Estoque extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estoque';

    protected $fillable = [
        'produto_id',
        'variacao',
        'quantidade',
        'quantidade_minima',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'quantidade_minima' => 'integer',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function itensPedido()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
