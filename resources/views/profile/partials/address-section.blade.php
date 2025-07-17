<div id="enderecos" class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-geo-alt me-2 text-indigo-600"></i> Meus Endereços
        </h2>
        <button type="button" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                onclick="document.getElementById('add-address-modal').classList.remove('hidden')">
            <i class="bi bi-plus-lg me-2"></i> Adicionar Endereço
        </button>
    </div>
    
    <div class="p-6">
        @if(isset($enderecos) && $enderecos && $enderecos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($enderecos as $endereco)
                    <div class="border rounded-lg p-4 {{ $endereco->principal ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200' }}">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-medium text-gray-900">
                                @if($endereco->principal)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <i class="bi bi-star-fill text-yellow-500 me-1"></i> Principal
                                    </span>
                                @endif
                                {{ $endereco->apelido ?? 'Endereço ' . $loop->iteration }}
                            </h3>
                            <div class="flex space-x-2">
                                @if(!$endereco->principal)
                                    <form action="{{ route('profile.addresses.set-default', $endereco) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-gray-500 hover:text-indigo-600"
                                                title="Definir como principal">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" 
                                        class="text-gray-500 hover:text-indigo-600 edit-address"
                                        data-address='@json($endereco)'
                                        title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('profile.addresses.destroy', $endereco) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir este endereço?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-600" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ $endereco->logradouro }}, {{ $endereco->numero }}<br>
                            @if($endereco->complemento)
                                {{ $endereco->complemento }}<br>
                            @endif
                            {{ $endereco->bairro }}<br>
                            {{ $endereco->cidade }} - {{ $endereco->estado }}<br>
                            CEP: {{ $endereco->cep }}
                        </p>
                        @if($endereco->referencia)
                            <p class="mt-2 text-sm text-gray-500">
                                <span class="font-medium">Referência:</span> {{ $endereco->referencia }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{-- Paginação --}}
            @if($enderecos->hasPages())
                <div class="mt-6">
                    {{ $enderecos->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <div class="mx-auto w-16 h-16 flex items-center justify-center bg-gray-100 rounded-full mb-4">
                    <i class="bi bi-geo-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Nenhum endereço cadastrado</h3>
                <p class="text-gray-500 mb-6">Adicione um endereço para facilitar suas compras.</p>
                <button type="button" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        onclick="document.getElementById('add-address-modal').classList.remove('hidden')">
                    <i class="bi bi-plus-lg me-2"></i> Adicionar Endereço
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Adicionar/Editar Endereço -->
<div id="add-address-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Adicionar Endereço</h3>
            <button type="button" 
                    class="text-gray-400 hover:text-gray-500"
                    onclick="document.getElementById('add-address-modal').classList.add('hidden')">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        
        <form id="address-form" method="POST" action="{{ route('profile.addresses.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="apelido" value="Apelido (opcional)" />
                    <x-text-input 
                        id="apelido" 
                        name="apelido" 
                        type="text" 
                        class="w-full" 
                        placeholder="Ex: Casa, Trabalho, etc."
                    />
                    <x-input-error :messages="$errors->get('apelido')" class="mt-1" />
                </div>
                
                <div class="md:col-span-1">
                    <x-input-label for="cep" value="CEP" />
                    <div class="flex">
                        <div class="flex">
                            <x-text-input 
                                id="cep" 
                                name="cep" 
                                type="text" 
                                class="mt-1 block w-full rounded-r-none" 
                                placeholder="00000-000" 
                                onblur="this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d{3})$/, '$1-$3')"
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 0"
                                maxlength="9"
                                required />
                            <button 
                                type="button"
                                id="buscar-cep-btn"
                                onclick="buscarCEP()"
                                class="mt-1 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-r-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                title="Buscar CEP">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('cep')" class="mt-1" />
                </div>
                
                <div class="md:col-span-2">
                    <x-input-label for="logradouro" value="Logradouro" />
                    <x-text-input 
                        id="logradouro" 
                        name="logradouro" 
                        type="text" 
                        class="w-full" 
                        placeholder="Rua, Avenida, etc."
                    />
                    <x-input-error :messages="$errors->get('logradouro')" class="mt-1" />
                </div>
                
                <div>
                    <x-input-label for="numero" value="Número" />
                    <x-text-input 
                        id="numero" 
                        name="numero" 
                        type="text" 
                        class="w-full" 
                        placeholder="Número"
                    />
                    <x-input-error :messages="$errors->get('numero')" class="mt-1" />
                </div>
                
                <div>
                    <x-input-label for="complemento" value="Complemento (opcional)" />
                    <x-text-input 
                        id="complemento" 
                        name="complemento" 
                        type="text" 
                        class="w-full" 
                        placeholder="Apto, Bloco, etc."
                    />
                    <x-input-error :messages="$errors->get('complemento')" class="mt-1" />
                </div>
                
                <div>
                    <x-input-label for="bairro" value="Bairro" />
                    <x-text-input 
                        id="bairro" 
                        name="bairro" 
                        type="text" 
                        class="w-full" 
                        placeholder="Bairro"
                    />
                    <x-input-error :messages="$errors->get('bairro')" class="mt-1" />
                </div>
                
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="cidade" value="Cidade" />
                        <x-text-input 
                            id="cidade" 
                            name="cidade" 
                            type="text" 
                            class="w-full" 
                            placeholder="Cidade"
                        />
                        <x-input-error :messages="$errors->get('cidade')" class="mt-1" />
                    </div>
                    
                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Selecione um estado</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-1" />
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <x-input-label for="referencia" value="Referência (opcional)" />
                    <x-text-input 
                        id="referencia" 
                        name="referencia" 
                        type="text" 
                        class="w-full" 
                        placeholder="Ponto de referência para facilitar a localização"
                    />
                    <x-input-error :messages="$errors->get('referencia')" class="mt-1" />
                </div>
                
                <div class="md:col-span-2 flex items-center">
                    <input id="principal" name="principal" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="principal" class="ml-2 block text-sm text-gray-700">
                        Definir como endereço principal
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        onclick="document.getElementById('add-address-modal').classList.add('hidden')">
                    Cancelar
                </button>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="bi bi-save me-2"></i> Salvar Endereço
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Função para buscar CEP na nossa API
    function buscarCEP() {
        const cepInput = document.getElementById('cep');
        const cep = cepInput.value.replace(/\D/g, '');
        const buscarCepBtn = document.getElementById('buscar-cep-btn');
        const originalBtnText = buscarCepBtn.innerHTML;
        
        if (cep.length !== 8) {
            alert('Por favor, digite um CEP válido com 8 dígitos.');
            cepInput.focus();
            return;
        }
        
        // Mostrar loading no botão
        buscarCepBtn.disabled = true;
        buscarCepBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Buscando...';
        
        // Limpar mensagens de erro
        const errorElement = document.getElementById('cep-error');
        if (errorElement) {
            errorElement.remove();
        }
        
        // Chamar nossa API personalizada
        fetch(`/api/cep/buscar?cep=${cep}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao buscar CEP');
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'CEP não encontrado');
                }
                
                // Preencher os campos com os dados retornados
                document.getElementById('logradouro').value = data.data.logradouro || '';
                document.getElementById('bairro').value = data.data.bairro || '';
                document.getElementById('cidade').value = data.data.cidade || '';
                document.getElementById('estado').value = data.data.estado || '';
                
                // Focar no campo de número
                document.getElementById('numero').focus();
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                
                // Adicionar mensagem de erro abaixo do campo CEP
                const errorElement = document.createElement('p');
                errorElement.id = 'cep-error';
                errorElement.className = 'mt-1 text-sm text-red-600';
                errorElement.textContent = 'CEP não encontrado. Por favor, verifique o número e tente novamente.';
                
                const cepContainer = cepInput.closest('div');
                if (cepContainer && !document.getElementById('cep-error')) {
                    cepContainer.appendChild(errorElement);
                }
                
                // Focar no campo de CEP para correção
                cepInput.focus();
            })
            .finally(() => {
                // Restaurar o botão
                buscarCepBtn.disabled = false;
                buscarCepBtn.innerHTML = originalBtnText;
            });
    }
    
    // Configurar eventos para edição de endereço
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-address');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const address = JSON.parse(this.getAttribute('data-address'));
                const form = document.getElementById('address-form');
                
                // Atualizar o título do modal
                document.getElementById('modal-title').textContent = 'Editar Endereço';
                
                // Atualizar o método do formulário para PUT
                document.getElementById('form-method').value = 'PUT';
                form.action = `/profile/addresses/${address.id}`;
                
                // Preencher os campos do formulário com os dados do endereço
                document.getElementById('apelido').value = address.apelido || '';
                document.getElementById('cep').value = address.cep || '';
                document.getElementById('logradouro').value = address.logradouro || '';
                document.getElementById('numero').value = address.numero || '';
                document.getElementById('complemento').value = address.complemento || '';
                document.getElementById('bairro').value = address.bairro || '';
                document.getElementById('cidade').value = address.cidade || '';
                document.getElementById('estado').value = address.estado || '';
                document.getElementById('referencia').value = address.referencia || '';
                document.getElementById('principal').checked = address.principal || false;
                
                // Exibir o modal
                document.getElementById('add-address-modal').classList.remove('hidden');
            });
        });
        
        // Resetar o formulário ao abrir para adicionar novo endereço
        const addButtons = document.querySelectorAll('[onclick*="add-address-modal"]');
        addButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Resetar o formulário
                document.getElementById('address-form').reset();
                document.getElementById('modal-title').textContent = 'Adicionar Endereço';
                document.getElementById('form-method').value = 'POST';
                document.getElementById('address-form').action = '{{ route("profile.addresses.store") }}';
            });
        });
    });
</script>
@endpush
