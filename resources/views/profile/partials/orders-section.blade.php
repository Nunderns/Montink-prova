{{-- Seção de Resumo de Pedidos Fechados --}}
@if(isset($pedidosFechados) && $pedidosFechados->isNotEmpty())
<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-archive me-2 text-indigo-600"></i> Resumo de Pedidos Finalizados
        </h2>
        <p class="mt-1 text-sm text-gray-500">Seus pedidos concluídos ou cancelados</p>
    </div>
    
    <div class="p-6">
        <div class="space-y-4">
            @foreach($pedidosFechados as $pedido)
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div class="flex items-center">
                            <span class="font-medium text-gray-900">Pedido #{{ $pedido['codigo'] }}</span>
                            <span class="mx-2 text-gray-300 hidden sm:inline">•</span>
                            <span class="text-sm text-gray-500">{{ $pedido['data'] }}</span>
                        </div>
                        <div>
                            @php
                                $statusClasses = [
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'entregue' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'cancelado' => 'bg-red-100 text-red-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'pendente' => 'bg-yellow-100 text-yellow-800',
                                    'pago' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'em_processamento' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'enviado' => 'bg-indigo-100 text-indigo-800',
                                ][$pedido['status_original']] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                {{ $pedido['status'] }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Itens: <span class="text-gray-900">{{ $pedido['total_itens'] }}</span></p>
                                <p class="text-gray-500">Valor Total: <span class="font-medium text-gray-900">{{ $pedido['valor_total'] }}</span></p>
                                @if($pedido['desconto'] !== 'Nenhum')
                                    <p class="text-gray-500">Desconto: <span class="text-green-600">-{{ $pedido['desconto'] }}</span></p>
                                @endif
                                <p class="text-gray-500">Total Pago: <span class="font-semibold text-indigo-600">{{ $pedido['valor_final'] }}</span></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Forma de Pagamento: <span class="text-gray-900">{{ $pedido['forma_pagamento'] }}</span></p>
                                <div class="mt-2">
                                    <p class="text-gray-500 mb-1">Endereço de Entrega:</p>
                                    <p class="text-gray-900 text-sm">{{ $pedido['endereco_entrega'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Seção de Pedidos em Andamento --}}
<div id="pedidos" class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-box-seam me-2 text-indigo-600"></i> Meus Pedidos em Andamento
        </h2>
        <p class="mt-1 text-sm text-gray-500">Acompanhe o status dos seus pedidos recentes</p>
    </div>
    
    <div class="p-6">
        {{-- DEBUG: Mostrar quantidade de pedidos --}}
        @if(isset($pedidos))
            <div class="mb-4 p-4 text-sm text-blue-800 bg-blue-50 rounded-lg">
                <b>Debug:</b> Total de pedidos encontrados: {{ $pedidos->count() }}<br>
                IDs: @foreach($pedidos as $p) {{ $p->id }} @endforeach
            </div>
        @endif

        @if(isset($pedidos) && $pedidos->count() > 0)
            <div class="space-y-4">
                @foreach($pedidos as $pedido)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div class="flex items-center">
                                <span class="font-medium text-gray-900">Pedido #{{ $pedido->codigo }}</span>
                                <span class="mx-2 text-gray-300 hidden sm:inline">•</span>
                                <span class="text-sm text-gray-500">{{ $pedido->data_formatada }}</span>
                            </div>
                            <div>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'pendente' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'em_processamento' => 'bg-blue-100 text-blue-800',
                                        'pago' => 'bg-blue-100 text-blue-800',
                                        'shipped' => 'bg-indigo-100 text-indigo-800',
                                        'enviado' => 'bg-indigo-100 text-indigo-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'entregue' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'cancelado' => 'bg-red-100 text-red-800',
                                    ][$pedido->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                    {{ $pedido->status_traduzido }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Itens</h4>
                                    <p class="text-sm text-gray-900">{{ $pedido->total_itens }} ite{{ $pedido->total_itens > 1 ? 'ns' : 'm' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Valor Total</h4>
                                    <p class="text-sm font-medium text-gray-900">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Forma de Pagamento</h4>
                                    <p class="text-sm text-gray-900">{{ ucfirst($pedido->forma_pagamento) }}</p>
                                </div>
                            </div>
                            
                            @if($pedido->endereco_entrega)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Endereço de Entrega</h4>
                                    <p class="text-sm text-gray-900">
                                        {{ $pedido->endereco_entrega->logradouro }}, {{ $pedido->endereco_entrega->numero }}
                                        @if($pedido->endereco_entrega->complemento)
                                            , {{ $pedido->endereco_entrega->complemento }}
                                        @endif
                                        <br>
                                        {{ $pedido->endereco_entrega->bairro }} - {{ $pedido->endereco_entrega->cidade }}/{{ $pedido->endereco_entrega->estado }}
                                        <br>
                                        CEP: {{ $pedido->endereco_entrega->cep }}
                                    </p>
                                </div>
                            @endif
                            
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('pedidos.show', $pedido) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="bi bi-eye me-1"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Paginação --}}
            @if($pedidos->hasPages())
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700">
                    <div class="mb-2 sm:mb-0">
                        Mostrando {{ $pedidos->firstItem() }} a {{ $pedidos->lastItem() }} de {{ $pedidos->total() }} resultados
                    </div>
                    <div class="flex space-x-1">
                        {{-- Botão Anterior --}}
                        @if($pedidos->onFirstPage())
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                &laquo; Anterior
                            </span>
                        @else
                            <a href="{{ $pedidos->previousPageUrl() }}" class="px-3 py-1 bg-white text-gray-700 rounded-md hover:bg-gray-50">
                                &laquo; Anterior
                            </a>
                        @endif
                        
                        {{-- Números de Página --}}
                        @foreach($pedidos->getUrlRange(1, $pedidos->lastPage()) as $page => $url)
                            @if($page == $pedidos->currentPage())
                                <span class="px-3 py-1 bg-indigo-600 text-white rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 bg-white text-gray-700 rounded-md hover:bg-gray-50">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                        
                        {{-- Botão Próximo --}}
                        @if($pedidos->hasMorePages())
                            <a href="{{ $pedidos->nextPageUrl() }}" class="px-3 py-1 bg-white text-gray-700 rounded-md hover:bg-gray-50">
                                Próximo &raquo;
                            </a>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                Próximo &raquo;
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-20 h-20 flex items-center justify-center bg-gray-100 rounded-full mb-4">
                    <i class="bi bi-cart-x text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Nenhum pedido encontrado</h3>
                <p class="text-gray-500 mb-6">Você ainda não realizou nenhum pedido em nossa loja.</p>
                <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="bi bi-arrow-left me-2"></i> Continuar Comprando
                </a>
            </div>
        @endif
    </div>
</div>
