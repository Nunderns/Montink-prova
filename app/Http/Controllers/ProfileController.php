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
        $enderecos = collect();
        
        if ($user) {
            // Obter os pedidos do usuário com paginação
            $pedidos = \App\Models\Pedido::with(['itens.produto', 'enderecoEntrega'])
                ->where('cliente_id', $user->id)
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'pedidos_page');
                
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
        $data = [
            'user' => $user,
            'pedidos' => $pedidos,
            'enderecos' => $enderecos,
            'header' => 'Minha Conta',
        ];
        
        // Debug: Verificar dados que estão sendo passados para a view
        \Log::info('Dados da view de perfil:', $data);
        
        return view('profile.edit', $data);
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
