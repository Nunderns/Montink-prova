@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Detalhes do Produto</h2>
    </div>
    <div class="col text-end">
        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">{{ $produto->nome }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p class="mb-3">{{ $produto->descricao ?? 'Sem descrição' }}</p>
                <h4 class="text-primary">R$ {{ number_format($produto->preco, 2, ',', '.') }}</h4>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-{{ $produto->ativo ? 'success' : 'secondary' }}">
                    {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <h5>Histórico de Atualizações</h5>
    <p class="text-muted">Última atualização: {{ $produto->updated_at->format('d/m/Y H:i') }}</p>
</div>

<div class="mt-4">
    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline" 
          onsubmit="return confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-trash"></i> Excluir Produto
        </button>
    </form>
</div>
@endsection
