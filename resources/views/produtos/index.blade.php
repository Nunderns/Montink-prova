@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h2>Nossos Produtos</h2>
            <p class="text-muted">Confira nossa seleção de produtos</p>
        </div>
        @auth
        <div class="col-md-6 text-end">
            <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Adicionar Produto
            </a>
        </div>
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($produtos->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($produtos as $produto)
                <div class="col">
                    <div class="card h-100 product-card">
                        @if($produto->imagem)
                            <img src="{{ asset('storage/' . $produto->imagem) }}" 
                                 class="card-img-top" 
                                 alt="{{ $produto->nome }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $produto->nome }}</h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit($produto->descricao, 100) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-primary">
                                    R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                </h6>
                                @if($produto->estoque->sum('quantidade') > 0)
                                    <span class="badge bg-success">Em estoque</span>
                                @else
                                    <span class="badge bg-secondary">Indisponível</span>
                                @endif
                            </div>
                            
                            @if($produto->estoque->count() > 1)
                                <select class="form-select form-select-sm mb-3" id="variacao-{{ $produto->id }}">
                                    @foreach($produto->estoque as $estoque)
                                        <option value="{{ $estoque->id }}" 
                                                data-quantity="{{ $estoque->quantidade }}"
                                                {{ $estoque->quantidade <= 0 ? 'disabled' : '' }}>
                                            {{ $estoque->variacao }}
                                            @if($estoque->quantidade > 0)
                                                ({{ $estoque->quantidade }} disponíveis)
                                            @else
                                                (Esgotado)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            
                            <div class="d-grid gap-2">
                                @if($produto->estoque->sum('quantidade') > 0)
                                    <form action="{{ route('carrinho.adicionar', $produto->id) }}" method="POST" class="d-grid">
                                        @csrf
                                        @if($produto->estoque->count() > 1)
                                            <input type="hidden" name="variacao_id" id="variacao-id-{{ $produto->id }}" value="{{ $produto->estoque->first()->id }}">
                                        @elseif($produto->estoque->count() === 1)
                                            <input type="hidden" name="variacao_id" value="{{ $produto->estoque->first()->id }}">
                                        @endif
                                        <div class="input-group mb-2">
                                            <input type="number" 
                                                   name="quantidade" 
                                                   class="form-control" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $produto->estoque->sum('quantidade') }}"
                                                   id="quantidade-{{ $produto->id }}">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="bi bi-cart-plus"></i> Comprar
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="bi bi-x-circle"></i> Indisponível
                                    </button>
                                @endif
                                
                                <a href="{{ route('produtos.show', $produto->id) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver Detalhes
                                </a>
                                
                                @auth
                                <div class="btn-group" role="group">
                                    <a href="{{ route('produtos.edit', $produto->id) }}" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('produtos.destroy', $produto->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center p-5 bg-light rounded">
            <i class="bi bi-box-seam display-4 text-muted mb-3"></i>
            <h4>Nenhum produto encontrado</h4>
            <p class="text-muted">Parece que ainda não há produtos cadastrados no sistema.</p>
            @auth
            <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Adicionar Primeiro Produto
            </a>
            @endauth
        </div>
    @endif

    @if($produtos->hasPages())
        <div class="mt-4">
            {{ $produtos->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Atualiza a quantidade máxima e o ID da variação quando a seleção mudar
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($produtos as $produto)
            @if($produto->estoque->count() > 1)
                const select{{ $produto->id }} = document.getElementById('variacao-{{ $produto->id }}');
                const quantidadeInput{{ $produto->id }} = document.getElementById('quantidade-{{ $produto->id }}');
                const variacaoIdInput{{ $produto->id }} = document.getElementById('variacao-id-{{ $produto->id }}');
                
                if (select{{ $produto->id }} && quantidadeInput{{ $produto->id }} && variacaoIdInput{{ $produto->id }}) {
                    select{{ $produto->id }}.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const maxQuantity = parseInt(selectedOption.getAttribute('data-quantity')) || 0;
                        
                        // Atualiza o input hidden com o ID da variação
                        variacaoIdInput{{ $produto->id }}.value = selectedOption.value;
                        
                        // Atualiza a quantidade máxima
                        quantidadeInput{{ $produto->id }}.max = maxQuantity;
                        
                        // Ajusta o valor atual se for maior que o novo máximo
                        if (parseInt(quantidadeInput{{ $produto->id }}.value) > maxQuantity) {
                            quantidadeInput{{ $produto->id }}.value = maxQuantity > 0 ? 1 : 0;
                        }
                    });
                }
            @endif
        @endforeach
    });
</script>
@endpush

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    .card-img-top {
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .card-body {
        display: flex;
        flex-direction: column;
    }
    .card-text {
        flex-grow: 1;
    }
</style>
@endsection
