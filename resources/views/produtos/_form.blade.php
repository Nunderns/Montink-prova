<!-- Nome do Produto -->
<div class="col-span-6 sm:col-span-4">
    <label for="nome" class="block text-sm font-medium text-gray-700">
        Nome do Produto <span class="text-red-500">*</span>
    </label>
    <input type="text" 
           id="nome" 
           name="nome" 
           value="{{ old('nome', $produto->nome ?? '') }}" 
           required
           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('nome') border-red-500 @enderror">
    @error('nome')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Preço -->
<div class="col-span-6 sm:col-span-2">
    <label for="preco" class="block text-sm font-medium text-gray-700">
        Preço <span class="text-red-500">*</span>
    </label>
    <div class="mt-1 relative rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="text-gray-500 sm:text-sm">R$</span>
        </div>
        <input type="number" 
               step="0.01" 
               min="0" 
               id="preco" 
               name="preco" 
               value="{{ old('preco', isset($produto) ? number_format($produto->preco, 2, '.', '') : '') }}" 
               required
               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('preco') border-red-500 @enderror">
    </div>
    @error('preco')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Descrição -->
<div class="col-span-6">
    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
    <div class="mt-1">
        <textarea id="descricao" 
                  name="descricao" 
                  rows="3"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md @error('descricao') border-red-500 @enderror">{{ old('descricao', $produto->descricao ?? '') }}</textarea>
    </div>
    <p class="mt-2 text-sm text-gray-500">
        Forneça uma descrição detalhada do produto.
    </p>
    @error('descricao')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Imagem do Produto -->
<div class="col-span-6">
    <label class="block text-sm font-medium text-gray-700">
        Imagem do Produto
    </label>
    <div class="mt-1 flex items-center">
        <span class="h-12 w-12 rounded-full overflow-hidden bg-gray-100">
            @if(isset($produto) && $produto->imagem)
                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="Imagem atual" class="h-full w-full text-gray-300">
            @else
                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            @endif
        </span>
        <label class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
            <span>Alterar</span>
            <input id="imagem" name="imagem" type="file" class="sr-only" accept="image/*">
        </label>
        @if(isset($produto) && $produto->imagem)
            <div class="ml-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remove_imagem" id="remove_imagem" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Remover imagem</span>
                </label>
            </div>
        @endif
    </div>
    @error('imagem')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Variações e Estoque -->
<div class="col-span-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Variações e Estoque
                </h3>
                <button type="button" 
                        id="add-variation" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Adicionar Variação
                </button>
            </div>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Adicione variações como tamanho, cor, etc. e a quantidade em estoque para cada uma.
            </p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div id="variations-container" class="space-y-4">
                @if(isset($produto) && $produto->estoque->count() > 0)
                    @foreach($produto->estoque as $index => $estoque)
                        <div class="variation-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-index="{{ $index }}">
                            <input type="hidden" name="variacoes[{{ $index }}][id]" value="{{ $estoque->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-5">
                                    <label class="block text-sm font-medium text-gray-700">Nome da Variação *</label>
                                    <input type="text" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                           name="variacoes[{{ $index }}][nome]" 
                                           value="{{ old('variacoes.'.$index.'.nome', $estoque->variacao) }}" 
                                           required>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                    <input type="number" 
                                           min="0" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                           name="variacoes[{{ $index }}][quantidade]" 
                                           value="{{ old('variacoes.'.$index.'.quantidade', $estoque->quantidade) }}" 
                                           required>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Quantidade Mínima</label>
                                    <input type="number" 
                                           min="0" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                           name="variacoes[{{ $index }}][quantidade_minima]" 
                                           value="{{ old('variacoes.'.$index.'.quantidade_minima', $estoque->quantidade_minima) }}">
                                </div>
                                <div class="md:col-span-1 flex items-end">
                                    <button type="button" 
                                            class="btn-remove-variation inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                            {{ $loop->first && $loop->count == 1 ? 'disabled' : '' }}>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="variation-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700">Nome da Variação *</label>
                                <input type="text" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       name="variacoes[0][nome]" 
                                       value="{{ old('variacoes.0.nome', 'Padrão') }}" 
                                       required>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                <input type="number" 
                                       min="0" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       name="variacoes[0][quantidade]" 
                                       value="{{ old('variacoes.0.quantidade', 0) }}" 
                                       required>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Quantidade Mínima</label>
                                <input type="number" 
                                       min="0" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       name="variacoes[0][quantidade_minima]" 
                                       value="{{ old('variacoes.0.quantidade_minima', 0) }}">
                            </div>
                            <div class="md:col-span-1 flex items-end">
                                <button type="button" 
                                        class="btn-remove-variation inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                        disabled>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Botões de Ação -->
<div class="flex justify-between mt-6">
    <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Voltar
    </a>
    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Salvar Produto
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('variations-container');
    const addButton = document.getElementById('add-variation');
    let variationCount = {{ isset($produto) && $produto->estoque->count() > 0 ? $produto->estoque->count() : 1 }};

    // Adicionar nova variação
    addButton.addEventListener('click', function() {
        const index = variationCount++;
        const variationItem = document.createElement('div');
        variationItem.className = 'variation-item bg-gray-50 p-4 rounded-lg border border-gray-200';
        variationItem.dataset.index = index;
        
        variationItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700">Nome da Variação *</label>
                    <input type="text" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           name="variacoes[${index}][nome]" 
                           value="" 
                           required>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                    <input type="number" 
                           min="0" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           name="variacoes[${index}][quantidade]" 
                           value="0" 
                           required>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Quantidade Mínima</label>
                    <input type="number" 
                           min="0" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           name="variacoes[${index}][quantidade_minima]" 
                           value="0">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" 
                            class="btn-remove-variation inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        container.insertBefore(variationItem, container.lastElementChild);
        updateRemoveButtons();
    });

    // Remover variação
    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-variation')) {
            const item = e.target.closest('.variation-item');
            if (container.querySelectorAll('.variation-item').length > 1) {
                item.remove();
                reindexVariations();
            }
        }
    });

    // Atualizar botões de remoção
    function updateRemoveButtons() {
        const items = container.querySelectorAll('.variation-item');
        const removeButtons = container.querySelectorAll('.btn-remove-variation');
        
        if (items.length <= 1) {
            removeButtons.forEach(btn => {
                btn.disabled = true;
            });
        } else {
            removeButtons.forEach(btn => {
                btn.disabled = false;
            });
        }
    }

    // Reindexar variações
    function reindexVariations() {
        document.querySelectorAll('.variation-item').forEach((item, index) => {
            item.setAttribute('data-index', index);
            
            // Atualiza os nomes dos campos
            const inputs = item.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/variacoes\[\d+\]/, `variacoes[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        
        updateRemoveButtons();
    }

    // Inicializar
    updateRemoveButtons();
});
</script>
@endpush
