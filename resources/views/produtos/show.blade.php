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
            <a href="{{ route('produtos.edit', $produto->id) }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2L7 17H5v-2l10-10z"/></svg>
                Editar
            </a>
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

    <!-- Cabeçalho -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('produtos.index') }}">Produtos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
            <h1 class="display-6 fw-bold text-primary mb-0">{{ $produto->nome }}</h1>
            <p class="text-muted mb-0">ID: {{ $produto->id }}</p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-pencil-fill me-2"></i>Editar
            </a>
            <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Card Principal do Produto -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="row g-0">
            <!-- Imagem do Produto -->
            <div class="col-md-5 bg-light">
                @if($produto->imagem)
                    <img src="{{ asset('storage/' . $produto->imagem) }}" 
                         class="img-fluid h-100 w-100 object-fit-cover" 
                         alt="{{ $produto->nome }}"
                         style="min-height: 300px;">
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                        <i class="bi bi-image" style="font-size: 5rem;"></i>
                        <p class="mt-3 mb-0">Sem imagem</p>
                    </div>
                @endif
            </div>
            
            <!-- Detalhes do Produto -->
            <div class="col-md-7">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge rounded-pill bg-{{ $produto->ativo ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $produto->ativo ? 'success' : 'secondary' }} px-3 py-2 mb-2">
                                <i class="bi bi-{{ $produto->ativo ? 'check-circle' : 'slash-circle' }} me-1"></i>
                                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                            <h2 class="h4 fw-bold mb-3">{{ $produto->nome }}</h2>
                        </div>
                        <h3 class="text-primary fw-bold mb-0">
                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                        </h3>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="h6 text-uppercase text-muted mb-3">Descrição</h5>
                        <div class="p-3 bg-light rounded-3">
                            <p class="mb-0">{{ $produto->descricao ?? 'Nenhuma descrição fornecida.' }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5 class="h6 text-uppercase text-muted mb-3">Data de Criação</h5>
                            <p class="mb-0">{{ $produto->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5 class="h6 text-uppercase text-muted mb-3">Última Atualização</h5>
                            <p class="mb-0">{{ $produto->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Ações -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 p-4 bg-light rounded-3">
        <div>
            <h5 class="h6 text-uppercase text-muted mb-2">Ações do Produto</h5>
            <p class="small text-muted mb-0">Gerencie o status e as configurações deste produto</p>
        </div>
        
        <div class="d-flex gap-2">
            <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" 
                  onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este produto?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                    <i class="bi bi-trash-fill me-2"></i>Excluir
                </button>
            </form>
            
            <form action="{{ route('produtos.toggle-status', $produto->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $produto->ativo ? 'warning' : 'success' }} rounded-pill px-4">
                    <i class="bi bi-{{ $produto->ativo ? 'x-circle' : 'check-circle' }}-fill me-2"></i>
                    {{ $produto->ativo ? 'Desativar' : 'Ativar' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Seção de Variações (se aplicável) -->
    @if($produto->estoque->count() > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Variações de Estoque</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Variação</th>
                            <th class="text-end">Quantidade</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produto->estoque as $estoque)
                        <tr>
                            <td>{{ $estoque->variacao ?? 'Padrão' }}</td>
                            <td class="text-end">{{ $estoque->quantidade }}</td>
                            <td class="text-end">
                                <span class="badge bg-{{ $estoque->quantidade > 0 ? 'success' : 'secondary' }}">
                                    {{ $estoque->quantidade > 0 ? 'Disponível' : 'Esgotado' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>