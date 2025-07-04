<div id="seguranca" class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-shield-lock me-2 text-indigo-600"></i> Segurança da Conta
        </h2>
        <p class="mt-1 text-sm text-gray-500">Atualize a senha da sua conta.</p>
    </div>
    
    <div class="p-6">
        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
            @csrf
            
            <div>
                <x-input-label for="current_password" value="Senha Atual" />
                <div class="mt-1 relative">
                    <x-text-input 
                        id="current_password" 
                        name="current_password" 
                        type="password" 
                        class="w-full" 
                        placeholder="Digite sua senha atual"
                        autocomplete="current-password"
                    />
                    <button type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility('current_password')">
                        <i class="bi bi-eye-slash" id="toggle-current_password"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
            </div>
            
            <div>
                <x-input-label for="password" value="Nova Senha" />
                <div class="mt-1 relative">
                    <x-text-input 
                        id="password" 
                        name="password" 
                        type="password" 
                        class="w-full" 
                        placeholder="Digite a nova senha"
                        autocomplete="new-password"
                    />
                    <button type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility('password')">
                        <i class="bi bi-eye-slash" id="toggle-password"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                <p class="mt-1 text-xs text-gray-500">
                    Use pelo menos 8 caracteres, incluindo letras maiúsculas, minúsculas, números e símbolos.
                </p>
            </div>
            
            <div>
                <x-input-label for="password_confirmation" value="Confirme a Nova Senha" />
                <div class="mt-1 relative">
                    <x-text-input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="w-full" 
                        placeholder="Confirme a nova senha"
                        autocomplete="new-password"
                    />
                    <button type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility('password_confirmation')">
                        <i class="bi bi-eye-slash" id="toggle-password_confirmation"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
            </div>
            
            <div class="flex items-center justify-end">
                <x-primary-button type="submit">
                    <i class="bi bi-key me-2"></i> Atualizar Senha
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(`toggle-${fieldId}`);
        
        if (field.type === 'password') {
            field.type = 'text';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        } else {
            field.type = 'password';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        }
    }
</script>
@endpush
