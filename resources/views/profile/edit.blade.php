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
                                                            @php
                                                                $statusClasses = [
                                                                    'pending' => 'bg-warning-subtle text-warning-emphasis',
                                                                    'pendente' => 'bg-warning-subtle text-warning-emphasis',
                                                                    'processing' => 'bg-info-subtle text-info-emphasis',
                                                                    'em_processamento' => 'bg-info-subtle text-info-emphasis',
                                                                    'pago' => 'bg-info-subtle text-info-emphasis',
                                                                    'shipped' => 'bg-primary-subtle text-primary-emphasis',
                                                                    'enviado' => 'bg-primary-subtle text-primary-emphasis',
                                                                    'delivered' => 'bg-success-subtle text-success-emphasis',
                                                                    'entregue' => 'bg-success-subtle text-success-emphasis',
                                                                    'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                                                    'cancelado' => 'bg-danger-subtle text-danger-emphasis',
                                                                ][$pedido->status] ?? 'bg-light text-dark';
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $pedido->codigo }}</td>
                                                                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                                                <td>
                                                                    <span class="badge {{ $statusClasses }}">{{ ucfirst($pedido->status) }}</span>
                                                                </td>
                                                                <td>R$ {{ number_format($pedido->valor_final, 2, ',', '.') }}</td>
                                                                <td>
                                                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
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
