<section>
    <div class="space-y-4">
        <p class="text-sm text-gray-600">
            Atualize as informações do seu perfil e endereço de e-mail.
        </p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="name" value="Nome completo" />
                <div class="mt-1">
                    <x-text-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="w-full" 
                        :value="old('name', isset($user) ? $user->name : '')" 
                        required 
                        autofocus 
                        autocomplete="name" 
                        placeholder="Digite seu nome completo"
                    />
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="E-mail" />
                <div class="mt-1">
                    <x-text-input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="w-full" 
                        :value="old('email', isset($user) ? $user->email : '')" 
                        required 
                        autocomplete="email"
                        placeholder="seu@email.com"
                    />
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 p-3 bg-yellow-50 text-yellow-700 text-sm rounded-md">
                        <p class="font-medium">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Seu e-mail não foi verificado.
                        </p>
                        <p class="mt-1">
                            Antes de continuar, por favor verifique seu e-mail. Se você não recebeu o e-mail de verificação,
                            <button form="send-verification" class="text-blue-600 hover:text-blue-800 font-medium">
                                clique aqui para reenviar
                            </button>.
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-green-600 font-medium">
                                <i class="bi bi-check-circle-fill me-1"></i> Um novo link de verificação foi enviado para o seu e-mail.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="pt-2">
                <x-primary-button class="w-full justify-center">
                    <i class="bi bi-check-lg me-2"></i> Salvar alterações
                </x-primary-button>
            </div>
        </form>
    </div>
</section>
