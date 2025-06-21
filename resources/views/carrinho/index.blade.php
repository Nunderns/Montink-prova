@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-0"><i class="bi bi-cart3 me-2"></i>Meu Carrinho</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('produtos.index') }}">Produtos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Carrinho</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('produtos.index') }}" class="btn btn-light shadow-sm">
                <i class="bi bi-arrow-left me-2"></i> Continuar Comprando
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(empty($itens))
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-cart-x display-3 text-muted mb-4"></i>
                    <h3 class="mb-3">Seu carrinho está vazio</h3>
                    <p class="text-muted mb-4">Parece que você ainda não adicionou nenhum produto ao seu carrinho.</p>
                    <a href="{{ route('produtos.index') }}" class="btn btn-primary px-4">
                        <i class="bi bi-bag me-2"></i> Ver Produtos
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4 border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-list-check me-2"></i>Itens no Carrinho</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 45%;">Produto</th>
                                        <th class="text-center">Preço Unitário</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-center">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itens as $itemKey => $item)
                                        <tr class="border-bottom">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="position-relative">
                                                        <img src="{{ $item['produto']->imagem ?? 'https://via.placeholder.com/80' }}" 
                                                             alt="{{ $item['produto']->nome }}" 
                                                             class="rounded-2" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee;">
                                                        <span class="position-absolute top-0 start-100 translate-middle badge bg-primary rounded-pill">
                                                            {{ $item['quantidade'] }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold d-block">{{ $item['produto']->nome }}</span>
                                                        @if($item['variacao'])
                                                            <span class="badge bg-light text-dark border mt-1">{{ $item['variacao']->variacao }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-semibold">R$ {{ number_format($item['preco'], 2, ',', '.') }}</td>
                                            <td class="text-center" style="width: 160px;">
                                                <form action="{{ route('carrinho.atualizar', $itemKey) }}" method="POST" class="d-flex align-items-center justify-content-center gap-2">
                                                    @csrf
                                                    <div class="input-group input-group-sm" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary decrement" type="button">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" name="quantidade" value="{{ $item['quantidade'] }}" min="1" 
                                                               class="form-control text-center border-secondary">
                                                        <button class="btn btn-outline-secondary increment" type="button">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary px-2" title="Atualizar">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-center fw-semibold">R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('carrinho.remover', $itemKey) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Remover" onclick="return confirm('Remover este item do carrinho?')">
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
                
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-tag text-primary"></i>
                                <span class="fw-semibold">Cupom de Desconto</span>
                            </div>
                            <form class="d-flex gap-2" style="max-width: 300px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Digite seu cupom">
                                <button class="btn btn-outline-primary btn-sm">Aplicar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2"></i>Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-semibold">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                                <span class="text-muted">Frete</span>
                                <span class="fw-semibold" id="frete-value">-</span>
                            </div>
                            
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="cep" 
                                       placeholder="Digite seu CEP" data-mask="00000-000">
                                <button class="btn btn-outline-primary" type="button" id="btn-calcular-frete">
                                    <i class="bi bi-truck"></i>
                                </button>
                            </div>
                            
                            <div id="frete-detalhes" class="alert alert-info p-2 d-none">
                                <small class="d-block">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <span id="endereco-entrega"></span>
                                </small>
                                <small class="d-block mt-1">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Prazo estimado: <span id="prazo-entrega"></span>
                                </small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
                            <span class="fw-bold">Total</span>
                            <span class="fs-4 fw-bold text-primary" id="total-pedido">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        
                        <a href="#" class="btn btn-primary w-100 py-3 fw-bold disabled" title="Funcionalidade em desenvolvimento">
                            <i class="bi bi-credit-card me-2"></i> Finalizar Compra
                        </a>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted d-block">
                                <i class="bi bi-lock-fill me-1"></i> Compra 100% segura
                            </small>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <img src="https://via.placeholder.com/40" alt="Bandeira 1" class="img-fluid" style="height: 20px;">
                                <img src="https://via.placeholder.com/40" alt="Bandeira 2" class="img-fluid" style="height: 20px;">
                                <img src="https://via.placeholder.com/40" alt="Bandeira 3" class="img-fluid" style="height: 20px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .empty-state {
        max-width: 400px;
        margin: 0 auto;
    }
    .table th {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .table td {
        vertical-align: middle;
    }
    .input-group-text {
        background-color: transparent;
    }
    .input-group-sm > .form-control {
        padding: 0.25rem 0.5rem;
    }
    .badge.bg-light {
        border: 1px solid #dee2e6;
    }
    .sticky-top {
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        // Máscara para CEP
        $('#cep').mask('00000-000');
        
        // Incrementar/decrementar quantidade
        $('.increment').click(function() {
            const input = $(this).closest('.input-group').find('input');
            input.val(parseInt(input.val()) + 1);
        });
        
        $('.decrement').click(function() {
            const input = $(this).closest('.input-group').find('input');
            if (parseInt(input.val()) > 1) {
                input.val(parseInt(input.val()) - 1);
            }
        });
        
        // Calcular frete
        $('#btn-calcular-frete').click(function() {
            const cep = $('#cep').val().replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('Por favor, informe um CEP válido com 8 dígitos.');
                return;
            }
            
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin me-1"></i> Calculando...');
            
            // Simulação de cálculo de frete (substitua por sua chamada AJAX real)
            setTimeout(function() {
                // Esta é uma simulação - na prática você faria uma chamada AJAX para seu backend
                $('#frete-detalhes').removeClass('d-none');
                $('#endereco-entrega').text('Rua Exemplo, 123 - Centro, São Paulo/SP');
                $('#prazo-entrega').text('3-5 dias úteis');
                $('#frete-value').text('R$ 15,90');
                $('#total-pedido').text('R$ ' + ({{ $total }} + 15.90).toFixed(2).replace('.', ','));
                
                btn.prop('disabled', false).html('<i class="bi bi-truck"></i>');
                
                // Mostrar toast de sucesso
                const toast = new bootstrap.Toast(document.getElementById('freteToast'));
                toast.show();
            }, 1500);
        });
    });
</script>
@endpush