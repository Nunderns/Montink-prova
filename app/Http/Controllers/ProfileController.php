<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    /**
     * Exibe o formulário de edição de perfil do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $pedidos = collect();
        $pedidosFechados = collect();
        $enderecos = collect();
        
        // Log para depuração
        \Log::info('ProfileController@edit - Iniciando', [
            'user_id' => $user ? $user->id : null,
            'is_authenticated' => $user ? 'sim' : 'não'
        ]);
        
        if ($user) {
            // Obter os pedidos abertos do usuário com paginação
            $queryPedidos = \App\Models\Pedido::with(['itens.produto', 'enderecoEntrega'])
                ->where('cliente_id', $user->id)
                ->whereIn('status', ['pending', 'processing', 'shipped'])
                ->orderByDesc('created_at');
                
            // Log da consulta SQL gerada
            \Log::info('Consulta SQL para pedidos abertos:', [
                'sql' => $queryPedidos->toSql(),
                'bindings' => $queryPedidos->getBindings()
            ]);
            
            $pedidos = $queryPedidos->paginate(10, ['*'], 'pedidos_page');
            
            // Log dos pedidos encontrados
            \Log::info('Pedidos abertos encontrados:', [
                'total' => $pedidos->total(),
                'ids' => $pedidos->pluck('id')->toArray()
            ]);
                
            // Obter os pedidos fechados (entregues ou cancelados) para o resumo
            $queryFechados = \App\Models\Pedido::with(['itens.produto', 'enderecoEntrega'])
                ->where('cliente_id', $user->id)
                ->whereIn('status', ['delivered', 'cancelled'])
                ->orderByDesc('created_at');
                
            // Log da consulta SQL gerada para pedidos fechados
            \Log::info('Consulta SQL para pedidos fechados:', [
                'sql' => $queryFechados->toSql(),
                'bindings' => $queryFechados->getBindings()
            ]);
            
            $pedidosFechados = $queryFechados->take(5) // Limita a 5 pedidos no resumo
                ->get()
                ->map(function($pedido) {
                    return $pedido->gerarResumo();
                });
                
            // Log dos pedidos fechados encontrados
            \Log::info('Pedidos fechados encontrados:', [
                'total' => $pedidosFechados->count(),
                'ids' => $pedidosFechados->pluck('id')->toArray()
            ]);
                
            // Obter endereços do usuário
            $enderecos = $user->addresses()
                ->orderBy('principal', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(5, ['*'], 'enderecos_page');
                
            // Adicionar informações adicionais a cada pedido
            $pedidos->getCollection()->transform(function ($pedido) {
                // Calcular o total de itens
                $pedido->total_itens = $pedido->itens->sum('quantidade');
                
                // Formatar a data para exibição
                $pedido->data_formatada = $pedido->created_at->format('d/m/Y H:i');
                
                // Adicionar status traduzido
                $statusTraduzidos = [
                    'pending' => 'Pendente',
                    'processing' => 'Processando',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregue',
                    'cancelled' => 'Cancelado',
                ];
                
                $pedido->status_traduzido = $statusTraduzidos[$pedido->status] ?? ucfirst($pedido->status);
                
                return $pedido;
            });
        }
        
        // Garantir que o usuário está disponível para a view
        $viewData = [
            'user' => $request->user(),
            'pedidos' => $pedidos,
            'pedidosFechados' => $pedidosFechados,
            'enderecos' => $enderecos,
            'header' => 'Minha Conta',
        ];
        
        // Log para depuração
        \Log::info('Dados da view de perfil:', [
            'user_id' => $request->user() ? $request->user()->id : null,
            'pedidos_count' => $pedidos->count(),
            'pedidos_fechados_count' => $pedidosFechados->count(),
            'enderecos_count' => $enderecos->count(),
        ]);
        
        return view('profile.edit', $viewData);
    }

    /**
     * Atualiza as informações do perfil do usuário.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'Perfil atualizado com sucesso!');
    }
    
    /**
     * Atualiza a senha do usuário.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Verifica se a senha atual está correta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual fornecida não está correta.'
            ]);
        }
        
        // Atualiza a senha
        $user->password = Hash::make($request->password);
        $user->save();
        
        return back()->with('status', 'Senha atualizada com sucesso!');
    }
    
    /**
     * Adiciona um novo endereço de entrega.
     */
    public function storeAddress(UserAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Se for o primeiro endereço, define como principal
        $isPrincipal = $user->addresses()->count() === 0;
        
        $endereco = new UserAddress($request->validated());
        $endereco->user_id = $user->id;
        $endereco->principal = $isPrincipal;
        $endereco->save();
        
        return back()->with('status', 'Endereço adicionado com sucesso!');
    }
    
    /**
     * Atualiza um endereço de entrega existente.
     */
    public function updateAddress(UserAddressRequest $request, UserAddress $endereco): RedirectResponse
    {
        $this->authorize('update', $endereco);
        
        $endereco->update($request->validated());
        
        return back()->with('status', 'Endereço atualizado com sucesso!');
    }
    
    /**
     * Remove um endereço de entrega.
     */
    public function destroyAddress(UserAddress $endereco): RedirectResponse
    {
        $this->authorize('delete', $endereco);
        
        $endereco->delete();
        
        return back()->with('status', 'Endereço removido com sucesso!');
    }
    
    /**
     * Define um endereço como principal.
     */
    public function setDefaultAddress(UserAddress $endereco): RedirectResponse
    {
        $this->authorize('update', $endereco);
        
        // Remove a definição de principal de todos os endereços do usuário
        $endereco->user->addresses()->update(['principal' => false]);
        
        // Define o endereço selecionado como principal
        $endereco->update(['principal' => true]);
        
        return back()->with('status', 'Endereço principal atualizado com sucesso!');
    }

    /**
     * Exclui a conta do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Realiza logout
        Auth::logout();

        // Remove a conta do usuário
        $user->delete();

        // Invalida a sessão e gera um novo token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Sua conta foi excluída com sucesso!');
    }
}
