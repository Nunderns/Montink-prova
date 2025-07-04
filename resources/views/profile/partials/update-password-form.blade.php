<section>
    <div class="space-y-4">
        <p class="text-sm text-gray-600">
            Certifique-se de que sua conta esteja usando uma senha longa e aleatória para se manter segura.
        </p>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <x-input-label for="update_password_current_password" value="Senha Atual" />
                <div class="mt-1">
                    <x-text-input 
                        id="update_password_current_password" 
                        name="current_password" 
                        type="password" 
                        class="w-full" 
                        placeholder="Digite sua senha atual"
                        autocomplete="current-password" 
                    />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="update_password_password" value="Nova Senha" />
                <div class="mt-1">
                    <x-text-input 
                        id="update_password_password" 
                        name="password" 
                        type="password" 
                        class="w-full" 
                        placeholder="Digite a nova senha"
                        autocomplete="new-password" 
                    />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                <p class="mt-1 text-xs text-gray-500">
                    Use pelo menos 8 caracteres, incluindo letras maiúsculas, minúsculas, números e símbolos.
                </p>
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" value="Confirme a Nova Senha" />
                <div class="mt-1">
                    <x-text-input 
                        id="update_password_password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="w-full" 
                        placeholder="Digite a nova senha novamente"
                        autocomplete="new-password" 
                    />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
            </div>

            <div class="pt-2">
                <x-primary-button class="w-full justify-center">
                    <i class="bi bi-key-fill me-2"></i> Alterar Senha
                </x-primary-button>
            </div>

            @if (session('status') === 'password-updated')
                <div class="p-3 bg-green-50 text-green-700 text-sm rounded-md flex items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span>Sua senha foi atualizada com sucesso!</span>
                </div>
            @endif
        </form>
    </div>
</section>
