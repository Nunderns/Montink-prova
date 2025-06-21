<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarrinhoController extends Controller
{
    public function adicionar(Request $request, \App\Models\Produto $produto)
    {
        // Aqui você pode implementar a lógica do carrinho futuramente (sessão, estoque, etc)
        // Por enquanto, só retorna mensagem de sucesso
        return back()->with('success', 'Produto adicionado ao carrinho!');
    }
}
