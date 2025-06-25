<section class="space-y-4">
    <div class="border border-red-100 bg-red-50 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-red-800">
                    Excluir Conta
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>
                        Uma vez que sua conta for excluída, todos os seus dados serão permanentemente removidos. 
                        Antes de excluir sua conta, por favor baixe todos os dados ou informações que deseja manter.
                    </p>
                </div>
                <div class="mt-4">
                    <x-danger-button
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="w-full justify-center"
                    >
                        <i class="bi bi-trash me-2"></i> Excluir Minha Conta
                    </x-danger-button>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                </div>
                <h2 class="mt-3 text-lg font-medium text-gray-900">
                    Tem certeza que deseja excluir sua conta?
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    Esta ação não pode ser desfeita. Todos os seus dados serão permanentemente removidos do nosso sistema.
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="Digite sua senha para confirmar" class="sr-only" />
                    <div class="relative">
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            class="w-full"
                            placeholder="Sua senha"
                        />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
                    </div>
                </div>


                <div class="mt-6 flex justify-center space-x-4">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')" class="px-6">
                        <i class="bi bi-x-lg me-2"></i> Cancelar
                    </x-secondary-button>

                    <x-danger-button type="submit" class="px-6">
                        <i class="bi bi-trash me-2"></i> Sim, excluir conta
                    </x-danger-button>
                </div>
            </div>
        </form>
    </x-modal>
</section>
