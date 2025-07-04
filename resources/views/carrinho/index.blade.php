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
                                                <form action="{{ route('carrinho.remover', $itemKey) }}" method="POST" class="d-inline remove-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Remover">
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
                
                <!-- Seção de Cupom de Desconto -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-tag text-primary"></i>
                                <span class="fw-semibold">Cupom de Desconto</span>
                            </div>
                            
                            @if(!session('applied_coupon'))
                                <form id="apply-coupon-form" class="d-flex gap-2" style="max-width: 400px;">
                                    @csrf
                                    <div class="position-relative flex-grow-1">
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               id="coupon_code" 
                                               name="coupon_code" 
                                               placeholder="Digite o código do cupom"
                                               required>
                                        <div id="coupon-message" class="position-absolute w-100 small mt-1"></div>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <span class="d-none spinner-border spinner-border-sm me-1" role="status" id="coupon-spinner"></span>
                                        Aplicar
                                    </button>
                                </form>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success d-flex align-items-center">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ session('applied_coupon.code') }}
                                    </span>
                                    <form id="remove-coupon-form" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-close btn-close-white" aria-label="Remover cupom"></button>
                                    </form>
                                </div>
                            @endif
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
                            
                            @if(session('applied_coupon') && $discount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">
                                        Cupom {{ session('applied_coupon.code') }}
                                        @if(session('applied_coupon.type') === 'percent')
                                            ({{ session('applied_coupon.value') }}% OFF)
                                        @endif
                                    </span>
                                    <span class="text-success fw-semibold">
                                        - R$ {{ number_format($discount, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                                <span class="text-muted">Frete</span>
                                <span class="fw-semibold" id="frete-value">
                                    @if(isset($shipping) && $shipping !== null)
                                        R$ {{ number_format($shipping, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            
                            <form id="form-calcular-frete">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="cep" 
                                           placeholder="Digite seu CEP" data-mask="00000-000" required>
                                    <button class="btn btn-outline-primary" type="submit" id="btn-calcular-frete">
                                        <i class="bi bi-truck"></i>
                                    </button>
                                </div>
                            </form>
                            
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
                        
                        <form action="{{ route('carrinho.finalizar') }}" method="POST" class="w-100">
                            @csrf
                            <div class="mb-3">
                                <label for="forma_pagamento" class="form-label fw-semibold mb-2">Forma de Pagamento</label>
                                <select name="forma_pagamento" id="forma_pagamento" class="form-select" required>
                                    <option value="">Selecione uma opção</option>
                                    <option value="pix">PIX</option>
                                    <option value="cartao_credito">Cartão de Crédito</option>
                                    <option value="cartao_debito">Cartão de Débito</option>
                                    <option value="boleto">Boleto Bancário</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                <i class="bi bi-credit-card me-2"></i> Finalizar Compra
                            </button>
                        </form>
                        
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

    {{-- Histórico de Pedidos --}}
    @if(isset($pedidos) && count($pedidos) > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Histórico de Pedidos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidos as $pedido)
                                    <tr>
                                        <td>{{ $pedido->codigo }}</td>
                                        <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ ucfirst($pedido->status) }}</span>
                                        </td>
                                        <td>R$ {{ number_format($pedido->valor_final, 2, ',', '.') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-secondary" title="Ver detalhes" disabled>
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
<script>
    // Função para formatar valor em reais
    function formatReal(value) {
        if (isNaN(value)) return 'R$ 0,00';
        return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    }
    
    // Função para atualizar os totais na página
    function updateTotals() {
        // Atualiza o subtotal
        $('[data-subtotal]').text(formatReal({{ $subtotal ?? 0 }}));
        
        // Atualiza o frete se disponível
        const shipping = parseFloat('{{ $shipping ?? 0 }}');
        if (shipping > 0) {
            $('#frete-value').text(formatReal(shipping));
        }
        
        // Calcula o total
        let total = {{ $subtotal ?? 0 }} + shipping;
        
        // Aplica desconto do cupom se existir
        @if(session('applied_coupon'))
            let discount = 0;
            @if(session('applied_coupon.type') === 'percent')
                discount = ({{ $subtotal ?? 0 }} * {{ session('applied_coupon.value') }}) / 100;
            @else
                discount = Math.min({{ session('applied_coupon.value') }}, {{ $subtotal ?? 0 }});
            @endif
            
            // Atualiza o total com desconto
            total = Math.max(0, total - discount);
        @endif
        
        // Atualiza o total na página
        $('#total-amount').text(formatReal(total));
    }
    
    console.log('Script do carrinho carregado'); // Debug
    
    // Configuração global do AJAX para incluir o token CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX global:', status, error);
            if (xhr.status === 419) {
                window.location.reload();
            }
        }
    });
    
    $(document).ready(function() {
        console.log('Documento pronto - jQuery funcionando'); // Debug
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content')); // Debug
        
        // Aplicar cupom
        $(document).on('submit', '#apply-coupon-form', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const couponCode = $('#coupon_code').val().trim();
            const couponMessage = $('#coupon-message');
            const submitBtn = form.find('button[type="submit"]');
            const spinner = $('#coupon-spinner');
            
            if (!couponCode) {
                couponMessage.html('<div class="text-danger">Por favor, insira um código de cupom.</div>');
                return;
            }
            
            // Mostra o spinner e desabilita o botão
            spinner.removeClass('d-none');
            submitBtn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("carrinho.aplicar-cupom") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: couponCode
                },
                success: function(response) {
                    if (response.success) {
                        // Recarrega a página para atualizar os totais
                        window.location.reload();
                    } else {
                        couponMessage.html('<div class="text-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Ocorreu um erro ao aplicar o cupom. Tente novamente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    couponMessage.html('<div class="text-danger">' + errorMessage + '</div>');
                },
                complete: function() {
                    // Esconde o spinner e reabilita o botão
                    spinner.addClass('d-none');
                    submitBtn.prop('disabled', false);
                }
            });
        });
        
        // Remover cupom
        $(document).on('submit', '#remove-coupon-form', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const removeBtn = form.find('button[type="submit"]');
            
            // Desabilita o botão para evitar múltiplos cliques
            removeBtn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("carrinho.remover-cupom") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    // Recarrega a página para atualizar os totais
                    window.location.reload();
                },
                error: function() {
                    alert('Ocorreu um erro ao remover o cupom. Tente novamente.');
                    removeBtn.prop('disabled', false);
                }
            });
        });
        
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
        
        // Debug: Verificar se os formulários de remoção estão sendo encontrados
        console.log('Formulários de remoção encontrados:', $('.remove-form').length);
        
        // Remover item do carrinho - versão simplificada
        $(document).on('submit', '.remove-form', function(e) {
            console.log('Tentando remover item - evento submit capturado'); // Debug
            e.preventDefault();
            
            const form = $(this);
            const url = form.attr('action');
            const itemKey = url.split('/').pop(); // Extrai a chave do item da URL
            const row = form.closest('tr');
            
            console.log('URL de remoção:', url);
            console.log('Chave do item extraída:', itemKey);
            console.log('Formulário HTML:', form[0].outerHTML);
            
            // Mostra um alerta de confirmação simples
            if (!confirm('Tem certeza que deseja remover este item do carrinho?')) {
                console.log('Remoção cancelada pelo usuário');
                return false;
            }
            
            // Desabilita o botão para evitar múltiplos cliques
            const submitButton = form.find('button[type="submit"]');
            submitButton.prop('disabled', true);
            
            console.log('Enviando requisição AJAX para:', url);
            console.log('Dados enviados:', {
                _method: 'DELETE',
                _token: $('meta[name="csrf-token"]').attr('content')
            });
            
            // Faz a requisição AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response); // Debug
                    
                    if (response && response.success) {
                        console.log('Item removido com sucesso, atualizando a página...'); // Debug
                        // Recarrega a página para garantir que tudo esteja atualizado
                        window.location.reload();
                    } else {
                        const errorMsg = response && response.message || 'Ocorreu um erro ao remover o item do carrinho.';
                        console.error('Erro ao remover item:', errorMsg); // Debug
                        alert(errorMsg);
                        submitButton.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusText: xhr.statusText
                    });
                    
                    if (xhr.status === 419) {
                        // Token CSRF expirado - recarrega a página
                        console.log('Token CSRF expirado, recarregando a página...');
                        window.location.reload();
                    } else {
                        const errorMsg = 'Ocorreu um erro ao remover o item do carrinho. Por favor, tente novamente.';
                        console.error(errorMsg, error);
                        alert(errorMsg);
                        submitButton.prop('disabled', false);
                    }
                }
            });
        });
        
        // Calcular frete
        $('#form-calcular-frete').on('submit', function(e) {
            e.preventDefault();
            
            const cep = $('#cep').val().replace(/\D/g, '');
            
            if (cep.length !== 8) {
                Swal.fire({
                    icon: 'warning',
                    title: 'CEP inválido',
                    text: 'Por favor, informe um CEP válido com 8 dígitos.'
                });
                return false;
            }
            
            const btn = $('#btn-calcular-frete');
            const btnOriginalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Calculando...');
            
            // Esconde mensagens de erro anteriores
            $('.invalid-feedback').remove();
            $('#cep').removeClass('is-invalid');
            
            // Faz a chamada AJAX para o backend para calcular o frete
            $.ajax({
                url: '{{ route("carrinho.calcular-frete") }}',
                type: 'POST',
                data: {
                    cep: cep,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Formata o endereço para exibição
                        const enderecoParts = [
                            response.endereco.logradouro,
                            response.endereco.bairro,
                            response.endereco.localidade,
                            response.endereco.uf
                        ].filter(Boolean);
                        
                        const enderecoFormatado = enderecoParts.join(', ');
                        
                        // Atualiza a interface
                        $('#frete-detalhes').removeClass('d-none');
                        $('#endereco-entrega').text(enderecoFormatado);
                        
                        // Define o prazo de entrega com base no CEP (simulação)
                        const prazo = cep.startsWith('01') ? '1-2 dias úteis' : '3-5 dias úteis';
                        $('#prazo-entrega').text(prazo);
                        
                        // Formata o valor do frete
                        const freteFormatado = parseFloat(response.frete).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        
                        // Atualiza o valor do frete no resumo
                        $('#frete-value').text(`R$ ${freteFormatado}`);
                        
                        // Atualiza o total
                        const subtotal = parseFloat('{{ $subtotal }}');
                        const frete = parseFloat(response.frete);
                        const total = subtotal + frete;
                        
                        $('#total-pedido').text('R$ ' + total.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        
                        // Mostra uma mensagem de sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'Frete calculado!',
                            html: `
                                <div class="text-start">
                                    <p class="mb-2">Frete calculado com sucesso para:</p>
                                    <p class="mb-1 fw-bold">${enderecoFormatado}</p>
                                    <p class="mb-0">Valor: <span class="fw-bold">R$ ${freteFormatado}</span></p>
                                    <p class="mb-0">Prazo: ${prazo}</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Entendi',
                            timer: 5000
                        });
                        
                        // Salva o CEP no localStorage
                        localStorage.setItem('ultimoCep', cep);
                    } else {
                        // Mostra mensagem de erro
                        $('#cep').addClass('is-invalid');
                        $('<div class="invalid-feedback d-block">' + (response.message || 'Não foi possível calcular o frete para o CEP informado.') + '</div>').insertAfter('#cep');
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Não foi possível calcular o frete',
                            text: response.message || 'Verifique o CEP informado e tente novamente.',
                            confirmButtonText: 'Entendi'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro ao calcular frete:', xhr);
                    
                    let errorMessage = 'Ocorreu um erro ao tentar calcular o frete. Por favor, tente novamente.';
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors && errors.cep) {
                            errorMessage = errors.cep[0];
                        }
                    }
                    
                    $('#cep').addClass('is-invalid');
                    $(`<div class="invalid-feedback d-block">${errorMessage}</div>`).insertAfter('#cep');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: errorMessage,
                        confirmButtonText: 'Entendi'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html(btnOriginalHtml);
                }
            });
            
            return false;
        });
        
        // Ao carregar a página, verifica se já tem um CEP salvo e preenche o campo
        $(window).on('load', function() {
            const cepSalvo = localStorage.getItem('ultimoCep');
            if (cepSalvo) {
                $('#cep').val(cepSalvo);
            }
        });
        
        // Formata o CEP enquanto o usuário digita
        $('#cep').on('input', function(e) {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d{1,3})?/, '$1-$2');
            }
            $(this).val(value);
            
            // Remove mensagens de erro ao começar a digitar
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
                $(this).nextAll('.invalid-feedback').remove();
            }
        });
        
        // Inicializa o valor do frete ao carregar a página
        @if(isset($shipping) && $shipping !== null)
        (function() {
            const frete = parseFloat({{ $shipping }});
            const freteFormatado = frete.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            // Atualiza o valor do frete no resumo
            $('#frete-value').text(`R$ ${freteFormatado}`);
            
            // Atualiza o total
            const subtotal = parseFloat({{ $subtotal ?? 0 }});
            const total = subtotal + frete;
            
            $('#total-pedido').text('R$ ' + total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        })();
        @endif
    });
</script>
@endpush