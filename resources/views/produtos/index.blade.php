@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Produtos</h2>
    </div>
    <div class="col text-end">
        <a href="{{ route('produtos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Produto
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($produtos->isEmpty())
            <div class="text-center p-4">
                <p class="text-muted">Nenhum produto cadastrado.</p>
                <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Adicionar Primeiro Produto
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Variações</th>
                            <th>Estoque Total</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produtos as $produto)
                            <tr>
                                <td>{{ $produto->nome }}</td>
                                <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                <td>{{ $produto->estoque->count() }}</td>
                                <td>{{ $produto->estoque->sum('quantidade') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-sm btn-info text-white" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $produtos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
