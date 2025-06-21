@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nossos Produtos</h1>
            <p class="text-gray-600 mt-1">Confira nossa seleção exclusiva de produtos</p>
        </div>
        @auth
        <a href="{{ route('produtos.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-lg shadow hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Novo Produto
        </a>
        @endauth
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center bg-green-50 border-l-4 border-green-500 p-4 rounded shadow">
            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-green-700 text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if($produtos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($produtos as $produto)
                <div class="bg-white rounded-2xl shadow group hover:shadow-lg transition overflow-hidden flex flex-col">
                    <div class="relative h-48 bg-gray-100 flex items-center justify-center">
                        @if($produto->imagem)
                            <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                        <div class="absolute top-2 right-2">
                            @if($produto->estoque->sum('quantidade') > 0)
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">Em Estoque</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-full">Esgotado</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-lg font-semibold text-gray-900 truncate">{{ $produto->nome }}</h2>
                            <span class="text-lg font-bold text-indigo-600 whitespace-nowrap">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                        </div>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $produto->descricao ?: 'Sem descrição.' }}</p>
                        @if($produto->estoque->count() > 1)
                            <div class="mb-3">
                                <label for="variacao-{{ $produto->id }}" class="block text-xs font-medium text-gray-700 mb-1">Variação</label>
                                <select id="variacao-{{ $produto->id }}" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @foreach($produto->estoque as $estoque)
                                        <option value="{{ $estoque->id }}" data-quantity="{{ $estoque->quantidade }}" {{ $estoque->quantidade <= 0 ? 'disabled' : '' }}>
                                            {{ $estoque->variacao }}
                                            @if($estoque->quantidade > 0)
                                                ({{ $estoque->quantidade }} disponíveis)
                                            @else
                                                (Esgotado)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="flex flex-col gap-2 mt-auto">
                            @if($produto->estoque->sum('quantidade') > 0)
                                <form action="{{ route('carrinho.adicionar', $produto->id) }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    @if($produto->estoque->count() > 1)
                                        <input type="hidden" name="variacao_id" id="variacao-id-{{ $produto->id }}" value="{{ $produto->estoque->first()->id }}">
                                    @elseif($produto->estoque->count() === 1)
                                        <input type="hidden" name="variacao_id" value="{{ $produto->estoque->first()->id }}">
                                    @endif
                                    <input type="number" name="quantidade" value="1" min="1" max="{{ $produto->estoque->sum('quantidade') }}" id="quantidade-{{ $produto->id }}" class="w-20 px-2 py-1 border rounded focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded shadow transition">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.48 19h9.04a2 2 0 001.83-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"></path>
                                        </svg>
                                        Comprar
                                    </button>
                                </form>
                            @else
                                <button class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 text-gray-500 font-semibold rounded shadow cursor-not-allowed" disabled>
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"></path>
                                    </svg>
                                    Indisponível
                                </button>
                            @endif
                            <a href="{{ route('produtos.show', $produto->id) }}" class="inline-flex items-center justify-center px-3 py-2 border border-indigo-600 text-indigo-600 font-semibold rounded hover:bg-indigo-50 transition">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ver Detalhes
                            </a>
                            @auth
                            <div class="flex gap-2 mt-1">
                                <a href="{{ route('produtos.edit', $produto->id) }}" class="inline-flex items-center justify-center px-2 py-1 border border-gray-400 text-gray-700 rounded hover:bg-gray-100 transition" title="Editar">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h6a2 2 0 002-2v-6a2 2 0 00-2-2H3v8z" />
                                    </svg>
                                </a>
                                <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border border-red-400 text-red-600 rounded hover:bg-red-50 transition" title="Excluir">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-xl shadow-inner mt-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7m-2 0V5a2 2 0 00-2-2H7a2 2 0 00-2 2v2m14 0H5" />
            </svg>
            <h4 class="text-lg font-semibold text-gray-700 mb-1">Nenhum produto encontrado</h4>
            <p class="text-gray-500 mb-4">Parece que ainda não há produtos cadastrados no sistema.</p>
            @auth
            <a href="{{ route('produtos.create') }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white font-semibold rounded shadow hover:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Adicionar Primeiro Produto
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
