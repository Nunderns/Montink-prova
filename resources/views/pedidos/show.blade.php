@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                {{-- Cabeçalho --}}
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detalhes do Pedido #{{ $pedido->codigo }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Realizado em {{ $pedido->created_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'shipped' => 'bg-indigo-100 text-indigo-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ][$pedido->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses }}">
                            {{ $pedido->status_traduzido }}
                        </span>
                    </div>
                </div>

                {{-- Mensagens de alerta --}}
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Resumo do Pedido --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Itens do Pedido
                                </h3>
                            </div>
                            <div class="bg-white">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($pedido->itens as $item)
                                        <li class="p-4 hover:bg-gray-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-20 w-20 bg-gray-200 rounded-md overflow-hidden">
                                                    @if($item->produto && $item->produto->imagem)
                                                        <img src="{{ asset('storage/' . $item->produto->imagem) }}" alt="{{ $item->produto->nome }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="h-full w-full bg-gray-200 flex items-center justify-center text-gray-400">
                                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                                        <h4>
                                                            {{ $item->produto->nome ?? 'Produto não disponível' }}
                                                        </h4>
                                                        <p class="ml-4">
                                                            R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                                        </p>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        Quantidade: {{ $item->quantidade }}
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 mt-1">
                                                        Subtotal: R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Endereço de Entrega --}}
                        @if($pedido->enderecoEntrega)
                            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Endereço de Entrega
                                    </h3>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Destinatário</p>
                                            <p class="mt-1 text-sm text-gray-900">{{ $pedido->cliente->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">CEP</p>
                                            <p class="mt-1 text-sm text-gray-900">{{ $pedido->enderecoEntrega->cep }}</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-500">Endereço</p>
                                            <p class="mt-1 text-sm text-gray-900">
                                                {{ $pedido->enderecoEntrega->logradouro }}, {{ $pedido->enderecoEntrega->numero }}
                                                @if($pedido->enderecoEntrega->complemento)
                                                    - {{ $pedido->enderecoEntrega->complemento }}
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Bairro</p>
                                            <p class="mt-1 text-sm text-gray-900">{{ $pedido->enderecoEntrega->bairro }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Cidade/UF</p>
                                            <p class="mt-1 text-sm text-gray-900">
                                                {{ $pedido->enderecoEntrega->cidade }}/{{ $pedido->enderecoEntrega->estado }}
                                            </p>
                                        </div>
                                        @if($pedido->enderecoEntrega->referencia)
                                            <div class="md:col-span-2">
                                                <p class="text-sm text-gray-500">Ponto de referência</p>
                                                <p class="mt-1 text-sm text-gray-900">{{ $pedido->enderecoEntrega->referencia }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Resumo da Compra --}}
                    <div>
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Resumo do Pedido
                                </h3>
                            </div>
                            <div class="p-6">
                                <dl class="space-y-4">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Subtotal</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                            R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                                        </dd>
                                    </div>
                                    
                                    @if($pedido->desconto > 0)
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Desconto</dt>
                                            <dd class="text-sm text-red-600">
                                                - R$ {{ number_format($pedido->desconto, 2, ',', '.') }}
                                                @if($pedido->cupom)
                                                    <span class="block text-xs text-gray-500">Cupom: {{ $pedido->cupom->codigo }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    @endif
                                    
                                    <div class="border-t border-gray-200 pt-4 flex justify-between">
                                        <dt class="text-base font-medium text-gray-900">Total</dt>
                                        <dd class="text-base font-bold text-gray-900">
                                            R$ {{ number_format($pedido->valor_final, 2, ',', '.') }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-6">
                                    <a href="{{ route('profile.edit') }}?tab=pedidos" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Voltar para meus pedidos
                                    </a>
                                    
                                    @if($pedido->podeSerCancelado())
                                        <form action="{{ route('pedidos.cancel', $pedido) }}" method="POST" class="mt-3" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Cancelar Pedido
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Ajuda e Suporte --}}
                        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Precisa de ajuda?
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">Central de Ajuda</h4>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Dúvidas sobre seu pedido? Entre em contato conosco.
                                        </p>
                                        <p class="mt-2">
                                            <a href="mailto:suporte@montink.com.br" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                                suporte@montink.com.br
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
