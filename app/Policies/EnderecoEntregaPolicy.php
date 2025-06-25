<?php

namespace App\Policies;

use App\Models\EnderecoEntrega;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnderecoEntregaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Usuários autenticados podem ver seus próprios endereços
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EnderecoEntrega $enderecoEntrega): bool
    {
        return $user->id === $enderecoEntrega->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Qualquer usuário autenticado pode criar endereços
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EnderecoEntrega $enderecoEntrega): bool
    {
        return $user->id === $enderecoEntrega->user_id;
    }
    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EnderecoEntrega $enderecoEntrega): bool
    {
        return $user->id === $enderecoEntrega->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EnderecoEntrega $enderecoEntrega): bool
    {
        return false; // Não suportamos restauração de endereços excluídos
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EnderecoEntrega $enderecoEntrega): bool
    {
        return false; // Não suportamos exclusão forçada
    }
    
    /**
     * Determine whether the user can set the address as default.
     */
    public function setDefault(User $user, EnderecoEntrega $enderecoEntrega): bool
    {
        return $user->id === $enderecoEntrega->user_id;
    }
}
