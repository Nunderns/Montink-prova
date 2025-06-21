@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Meu Carrinho</h2>
    </div>
    <div class="col text-end">
        <a href="{{ route('produtos.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Continuar Comprando
        </a>
    </div>
</div>

@if(empty($itens))
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
            <h3>Seu carrinho está vazio</h3>
            <p class="text-muted">Adicione produtos ao seu carrinho para continuar.</p>
            <a href="{{ route('produtos.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-bag"></i> Ver Produtos
            </a>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itens as $itemKey => $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <img src="{{ $item['produto']->imagem ?? 'https://via.placeholder.com/80' }}" 
                                                         alt="{{ $item['produto']->nome }}" 
                                                         style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $item['produto']->nome }}</h6>
                                                    @if($item['variacao'])
                                                        <p class="text-muted small mb-0">
                                                            {{ $item['variacao']->variacao }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>R$ {{ number_format($item['preco'], 2, ',', '.') }}</td>
                                        <td style="width: 150px;">
                                            <form action="{{ route('carrinho.atualizar', $itemKey) }}" method="POST" class="d-flex">
                                                @csrf
                                                <input type="number" name="quantidade" value="{{ $item['quantidade'] }}" min="1" 
                                                       class="form-control form-control-sm me-2" style="width: 70px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td>R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                                        <td>
                                            <form action="{{ route('carrinho.remover', $itemKey) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cep" class="form-label">Calcular Frete</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cep" 
                                   placeholder="00000-000" data-mask="00000-000">
                            <button class="btn btn-outline-secondary" type="button" id="btn-calcular-frete">
                                Calcular
                            </button>
                        </div>
                        <div id="frete-detalhes" class="mt-2 d-none">
                            <small class="text-muted">
                                <span id="endereco-entrega"></span><br>
                                Frete: <span id="valor-frete"></span>
                            </small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span id="total-pedido">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
                        Finalizar Compra
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        // Máscara para CEP
        $('#cep').mask('00000-000');
        
        // Calcular frete
        $('#btn-calcular-frete').click(function() {
            const cep = $('#cep').val().replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('Por favor, informe um CEP válido.');
                return;
            }
            
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            
            $.ajax({
                url: '{{ route("carrinho.calcular-frete") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cep: cep
                },
                success: function(response) {
                    if (response.success) {
                        $('#frete-detalhes').removeClass('d-none');
                        $('#endereco-entrega').html(
                            response.endereco.logradouro + ', ' + 
                            response.endereco.bairro + ' - ' + 
                            response.endereco.cidade + '/' + response.endereco.uf
                        );
                        $('#valor-frete').text('R$ ' + response.frete);
                        $('#total-pedido').text('R$ ' + response.total);
                    } else {
                        alert(response.message || 'Erro ao calcular frete.');
                    }
                },
                error: function() {
                    alert('Erro ao conectar ao servidor. Tente novamente.');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Calcular');
                }
            });
        });
    });
</script>
@endpush
@endsection
