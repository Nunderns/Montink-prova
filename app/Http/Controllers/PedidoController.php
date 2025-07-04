<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibe os detalhes de um pedido específico.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Pedido $pedido)
    {
        // Verifica se o pedido pertence ao usuário autenticado
        if ($pedido->cliente_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Você não tem permissão para visualizar este pedido.');
        }

        // Carrega os relacionamentos necessários
        $pedido->load(['itens.produto', 'enderecoEntrega', 'cupom']);

        return view('pedidos.show', compact('pedido'));
    }
    
    /**
     * Cancela um pedido específico.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Pedido $pedido)
    {
        // Verifica se o pedido pertence ao usuário autenticado
        if ($pedido->cliente_id !== Auth::id() && !Auth::user()->is_admin) {
            return back()
                ->with('error', 'Você não tem permissão para cancelar este pedido.');
        }

        // Verifica se o pedido pode ser cancelado
        if (!$pedido->podeSerCancelado()) {
            return back()
                ->with('error', 'Este pedido não pode mais ser cancelado.');
        }

        try {
            // Atualiza o status do pedido para cancelado
            $pedido->update(['status' => 'cancelled']);
            
            // Aqui você pode adicionar lógica para reverter estoque, se necessário
            // $pedido->restaurarEstoque();
            
            Log::info("Pedido #{$pedido->id} cancelado pelo usuário #" . Auth::id());
            
            return redirect()
                ->route('profile.edit', ['tab' => 'pedidos'])
                ->with('success', 'Pedido cancelado com sucesso!');
                
        } catch (\Exception $e) {
            Log::error("Erro ao cancelar pedido #{$pedido->id}: " . $e->getMessage());
            
            return back()
                ->with('error', 'Ocorreu um erro ao cancelar o pedido. Por favor, tente novamente.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}
