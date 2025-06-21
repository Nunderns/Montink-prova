@csrf

<div class="row mb-3">
    <div class="col-md-8">
        <label for="nome" class="form-label">Nome do Produto *</label>
        <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" 
               value="{{ old('nome', $produto->nome ?? '') }}" required>
        @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="preco" class="form-label">Preço *</label>
        <div class="input-group">
            <span class="input-group-text">R$</span>
            <input type="number" step="0.01" min="0" class="form-control @error('preco') is-invalid @enderror" 
                   id="preco" name="preco" value="{{ old('preco', isset($produto) ? number_format($produto->preco, 2, '.', '') : '') }}" required>
        </div>
        @error('preco')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="descricao" class="form-label">Descrição</label>
    <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" 
              rows="3">{{ old('descricao', $produto->descricao ?? '') }}</textarea>
    @error('descricao')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Variações e Estoque</span>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-variation">
            <i class="bi bi-plus-lg"></i> Adicionar Variação
        </button>
    </div>
    <div class="card-body">
        <div id="variations-container">
            @if(isset($produto) && $produto->estoque->count() > 0)
                @foreach($produto->estoque as $index => $estoque)
                    <div class="variation-item mb-3" data-index="{{ $index }}">
                        <input type="hidden" name="variacoes[{{ $index }}][id]" value="{{ $estoque->id }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Nome da Variação *</label>
                                <input type="text" class="form-control" name="variacoes[{{ $index }}][nome]" 
                                       value="{{ old('variacoes.'.$index.'.nome', $estoque->variacao) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade *</label>
                                <input type="number" min="0" class="form-control" name="variacoes[{{ $index }}][quantidade]" 
                                       value="{{ old('variacoes.'.$index.'.quantidade', $estoque->quantidade) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade Mínima</label>
                                <input type="number" min="0" class="form-control" name="variacoes[{{ $index }}][quantidade_minima]" 
                                       value="{{ old('variacoes.'.$index.'.quantidade_minima', $estoque->quantidade_minima) }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-remove-variation" {{ $loop->first && $loop->count == 1 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="variation-item mb-3" data-index="0">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Nome da Variação *</label>
                            <input type="text" class="form-control" name="variacoes[0][nome]" 
                                   value="{{ old('variacoes.0.nome', 'Padrão') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantidade *</label>
                            <input type="number" min="0" class="form-control" name="variacoes[0][quantidade]" 
                                   value="{{ old('variacoes.0.quantidade', 0) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantidade Mínima</label>
                            <input type="number" min="0" class="form-control" name="variacoes[0][quantidade_minima]" 
                                   value="{{ old('variacoes.0.quantidade_minima', 0) }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-remove-variation" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> Salvar
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('variations-container');
        const addButton = document.getElementById('add-variation');
        
        // Adicionar variação
        addButton.addEventListener('click', function() {
            const index = document.querySelectorAll('.variation-item').length;
            const newVariation = `
                <div class="variation-item mb-3" data-index="${index}">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Nome da Variação *</label>
                            <input type="text" class="form-control" name="variacoes[${index}][nome]" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantidade *</label>
                            <input type="number" min="0" class="form-control" name="variacoes[${index}][quantidade]" value="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantidade Mínima</label>
                            <input type="number" min="0" class="form-control" name="variacoes[${index}][quantidade_minima]" value="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-remove-variation">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', newVariation);
            updateRemoveButtons();
        });
        
        // Remover variação
        container.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-variation')) {
                const variationItem = e.target.closest('.variation-item');
                if (document.querySelectorAll('.variation-item').length > 1) {
                    variationItem.remove();
                    reindexVariations();
                }
            }
        });
        
        function updateRemoveButtons() {
            const variationItems = document.querySelectorAll('.variation-item');
            const removeButtons = document.querySelectorAll('.btn-remove-variation');
            
            if (variationItems.length === 1) {
                removeButtons[0].disabled = true;
            } else {
                removeButtons.forEach(btn => btn.disabled = false);
            }
        }
        
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
        
        // Inicializa os botões de remoção
        updateRemoveButtons();
    });
</script>
@endpush
