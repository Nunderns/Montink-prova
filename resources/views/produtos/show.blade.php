@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Breadcrumb e header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <nav class="text-sm mb-2" aria-label="Breadcrumb">
                <ol class="flex space-x-2 text-gray-500">
                    <li><a href="{{ route('produtos.index') }}" class="hover:underline">Produtos</a></li>
                    <li>/</li>
                    <li class="text-indigo-600 font-semibold">Detalhes</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">{{ $produto->nome }}</h1>
            <p class="text-gray-400 text-xs mt-1">ID: {{ $produto->id }}</p>
        </div>
        <div class="flex gap-2">
            @auth
                @if(in_array(Auth::user()->cargo, ['admin', 'gerente']))
                    <a href="{{ route('produtos.edit', $produto->id) }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2L7 17H5v-2l10-10z"/></svg>
                        Editar
                    </a>
                @endif
            @endauth
            @guest
                <form action="{{ route('carrinho.adicionar', $produto->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v18H3V3zm3 6h12v2H6V9zm0 4h12v2H6v-2z"/></svg>
                        Comprar
                    </button>
                </form>
            @endguest
            @auth
                @if(!in_array(Auth::user()->cargo, ['admin', 'gerente']))
                    <form action="{{ route('carrinho.adicionar', $produto->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v18H3V3zm3 6h12v2H6V9zm0 4h12v2H6v-2z"/></svg>
                            Comprar
                        </button>
                    </form>
                @endif
            @endauth
            <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg shadow hover:bg-gray-100 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Card principal -->
    <div class="bg-white rounded-2xl shadow flex flex-col md:flex-row overflow-hidden mb-8">
        <!-- Imagem -->
        <div class="md:w-2/5 bg-gray-100 flex items-center justify-center min-h-[280px]">
            @if($produto->imagem)
                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="object-cover w-full h-72 md:h-full">
            @else
                <div class="flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 11l2 2 4-4m-6 5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <span class="text-sm">Sem imagem</span>
                </div>
            @endif
        </div>

        <!-- Detalhes -->
        <div class="flex-1 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $produto->ativo ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            @if($produto->ativo)
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            @endif
                        </svg>
                        {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                    <span class="ml-auto text-2xl font-bold text-indigo-700">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                </div>
                <p class="text-gray-700 mb-3">{{ $produto->descricao ?? 'Nenhuma descrição fornecida.' }}</p>
                <div class="flex flex-col sm:flex-row gap-4 text-xs text-gray-400 mb-4">
                    <span>Criado em: {{ $produto->created_at->format('d/m/Y H:i') }}</span>
                    <span>Última atualização: {{ $produto->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Ações -->
            @auth
                @if(in_array(Auth::user()->cargo, ['admin', 'gerente']))
                <div class="flex flex-wrap gap-2 mt-4">
                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este produto?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            Excluir
                        </button>
                    </form>
                    <form action="{{ route('produtos.toggle-status', $produto->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 {{ $produto->ativo ? 'bg-yellow-400 hover:bg-yellow-500 text-yellow-900' : 'bg-green-600 hover:bg-green-700 text-white' }} font-semibold rounded-lg shadow transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                @if($produto->ativo)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                @endif
                            </svg>
                            {{ $produto->ativo ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>
                </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Variações de Estoque -->
    @if($produto->estoque->count() > 0)
    <div class="bg-white rounded-2xl shadow mt-8 overflow-x-auto">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Variações de Estoque</h2>
        </div>
        <div class="p-6">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 uppercase text-xs border-b">
                        <th class="py-2">Variação</th>
                        <th class="py-2 text-right">Quantidade</th>
                        <th class="py-2 text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produto->estoque as $estoque)
                        <tr class="border-b last:border-0">
                            <td class="py-2">{{ $estoque->variacao ?? 'Padrão' }}</td>
                            <td class="py-2 text-right">{{ $estoque->quantidade }}</td>
                            <td class="py-2 text-right">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $estoque->quantidade > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                    {{ $estoque->quantidade > 0 ? 'Disponível' : 'Esgotado' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
