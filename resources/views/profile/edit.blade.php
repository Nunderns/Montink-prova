@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center mb-4">
            <i class="bi bi-person-circle text-2xl text-gray-700 me-3"></i>
            <h1 class="text-2xl font-bold text-gray-800">Minha Conta</h1>
        </div>
        <p class="text-sm text-gray-500 mb-6">Gerencie suas informações pessoais, segurança e histórico de pedidos</p>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Mensagem de sucesso --}}
            @if (session('status'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 5000)"
                     class="p-4 mb-6 text-sm text-green-800 bg-green-50 rounded-lg flex items-center">
                    <i class="bi bi-check-circle-fill text-green-500 text-lg me-2"></i>
                    <span>{{ session('status') === 'profile-updated' ? 'Perfil atualizado com sucesso!' : session('status') }}</span>
                </div>
            @endif

            {{-- Cabeçalho do perfil --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 mb-6">
                <div class="p-6 flex items-center">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-2xl">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</h3>
                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <div x-data="{ activeTab: 'perfil' }">
                {{-- Navegação por abas --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto">
                        <button @click="activeTab = 'perfil'" 
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'perfil', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'perfil' }" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <i class="bi bi-person-lines-fill mr-2"></i>
                            Meu Perfil
                        </button>
                        <button @click="activeTab = 'seguranca'" 
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'seguranca', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'seguranca' }" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <i class="bi bi-shield-lock mr-2"></i>
                            Segurança
                        </button>
                        <button @click="activeTab = 'enderecos'" 
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'enderecos', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'enderecos' }" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <i class="bi bi-geo-alt mr-2"></i>
                            Meus Endereços
                        </button>
                        <button @click="activeTab = 'pedidos'" 
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'pedidos', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pedidos' }" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <i class="bi bi-box-seam mr-2"></i>
                            Meus Pedidos
                        </button>
                    </nav>
                </div>

                {{-- Conteúdo das abas --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                <div class="space-y-6">
                    {{-- Aba de Perfil --}}
                    <div x-show="activeTab === 'perfil'" class="p-6">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="bi bi-person-lines-fill me-2 text-indigo-600"></i> Informações do Perfil
                            </h2>
                        </div>
                        <div class="p-6">
                            @php global $user, $enderecos, $pedidos; @endphp
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Aba de Segurança --}}
                    <div x-show="activeTab === 'seguranca'" class="p-6">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="bi bi-shield-lock me-2 text-indigo-600"></i> Segurança
                            </h2>
                        </div>
                        <div class="p-6">
                            @php global $user, $enderecos, $pedidos; @endphp
                            @include('profile.partials.security-section')
                        </div>
                    </div>

                    {{-- Aba de Endereços --}}
                    <div x-show="activeTab === 'enderecos'" class="p-6">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="bi bi-geo-alt me-2 text-indigo-600"></i> Meus Endereços
                            </h2>
                        </div>
                        <div class="p-6">
                            @php global $user, $enderecos, $pedidos; @endphp
                            @include('profile.partials.address-section')
                        </div>
                    </div>

                    {{-- Aba de Pedidos --}}
                    <div x-show="activeTab === 'pedidos'" class="p-6">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="bi bi-box-seam me-2 text-indigo-600"></i> Meus Pedidos
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">Acompanhe o histórico dos seus pedidos recentes</p>
                        </div>
                        <div class="p-6">
                            @if(isset($pedidos) && $pedidos->count() > 0)
                                <div class="space-y-4">
                                    @foreach($pedidos as $pedido)
                                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                                <div class="flex items-center">
                                                    <span class="font-medium text-gray-900">Pedido #{{ $pedido->codigo }}</span>
                                                    <span class="mx-2 text-gray-300">•</span>
                                                    <span class="text-sm text-gray-500">{{ $pedido->data_formatada }}</span>
                                                </div>
                                                <div class="flex items-center">
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
                                                        @if($pedido->itens->count() > 0)
                                                            <ul class="mt-1 text-sm text-gray-600">
                                                                @foreach($pedido->itens->take(2) as $item)
                                                                    <li class="truncate">
                                                                        {{ $item->quantidade }}x {{ $item->produto->nome ?? 'Produto não encontrado' }}
                                                                    </li>
                                                                @endforeach
                                                                @if($pedido->itens->count() > 2)
                                                                    <li class="text-indigo-600">+{{ $pedido->itens->count() - 2 }} mais</li>
                                                                @endif
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($pedido->enderecoEntrega)
                                                    <div>
                                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Entrega</h4>
                                                        <p class="text-sm text-gray-900">{{ $pedido->enderecoEntrega->cidade }}/{{ $pedido->enderecoEntrega->estado }}</p>
                                                        <p class="text-xs text-gray-500 truncate">{{ $pedido->enderecoEntrega->logradouro }}, {{ $pedido->enderecoEntrega->numero }}</p>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="text-right">
                                                        <h4 class="text-sm font-medium text-gray-500">Valor total</h4>
                                                        <p class="text-lg font-bold text-gray-900">R$ {{ number_format($pedido->valor_final, 2, ',', '.') }}</p>
                                                        <div class="mt-2 space-x-2">
                                                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                <i class="bi bi-eye-fill mr-1"></i> Detalhes
                                                            </a>
                                                            @if($pedido->podeSerCancelado())
                                                                <form action="{{ route('pedidos.cancel', $pedido->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?');">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                                        <i class="bi bi-x-circle-fill mr-1"></i> Cancelar
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Paginação --}}
                                @if($pedidos->hasPages())
                                    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700">
                                        <div class="mb-2 sm:mb-0">
                                            Mostrando <span class="font-medium">{{ $pedidos->firstItem() }}</span> a 
                                            <span class="font-medium">{{ $pedidos->lastItem() }}</span> de 
                                            <span class="font-medium">{{ $pedidos->total() }}</span> pedidos
                                        </div>
                                        
                                        <div class="flex space-x-1">
                                            {{-- Botão Anterior --}}
                                            @if($pedidos->onFirstPage())
                                                <span class="px-3 py-1 bg-gray-100 rounded-md text-gray-400 cursor-not-allowed">
                                                    &larr; Anterior
                                                </span>
                                            @else
                                                <a href="{{ $pedidos->previousPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                    &larr; Anterior
                                                </a>
                                            @endif
                                            
                                            {{-- Botão Próximo --}}
                                            @if($pedidos->hasMorePages())
                                                <a href="{{ $pedidos->nextPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                    Próximo &rarr;
                                                </a>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 rounded-md text-gray-400 cursor-not-allowed">
                                                    Próximo &rarr;
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
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h3>
                                    <p class="text-gray-500 mb-6 max-w-md mx-auto">Você ainda não realizou nenhum pedido em nossa loja. Comece a explorar nossos produtos agora mesmo!</p>
                                    <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="bi bi-arrow-left me-2"></i> Continuar Comprando
                                    </a>
                                </div>
                            @endif
                    </div>

                    {{-- Seção de Exclusão de Conta --}}
                    <div x-show="activeTab === 'perfil'" class="bg-white rounded-lg shadow-sm overflow-hidden border border-red-100 mt-6">
                        <div class="px-6 py-4 border-b border-red-100 bg-red-50">
                            <h2 class="text-lg font-semibold text-red-800">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Zona de Perigo
                            </h2>
                        </div>
                        <div class="p-6">
                            @php global $user, $enderecos, $pedidos; @endphp
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
    // Script para manter a aba ativa ao recarregar a página
    document.addEventListener('alpine:init', () => {
        const urlHash = window.location.hash.substring(1);
        if (urlHash) {
            Alpine.store('activeTab', urlHash);
        }
    });
</script>
@endpush
@endsection
