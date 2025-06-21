<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Painel de Controle') }}
            </h2>
            @can('manage-products')
            <a href="{{ route('produtos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="bi bi-plus-circle mr-2"></i> Novo Produto
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card de Produtos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="bi bi-box-seam text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Produtos</h3>
                                <p class="text-sm text-gray-500">Gerencie seus produtos</p>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <a href="{{ route('produtos.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                Ver todos
                            </a>
                            @can('manage-products')
                            <a href="{{ route('produtos.create') }}" class="text-sm font-medium text-green-600 hover:text-green-800">
                                Adicionar novo
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Card de Pedidos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="bi bi-cart-check text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Pedidos</h3>
                                <p class="text-sm text-gray-500">Acompanhe os pedidos</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                Ver pedidos recentes
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card de Relatórios -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                <i class="bi bi-graph-up text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Relatórios</h3>
                                <p class="text-sm text-gray-500">Acompanhe suas vendas</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                Ver relatórios
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Estatísticas Rápidas -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Visão Geral</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Total de Produtos</p>
                            <p class="text-2xl font-semibold">{{ \App\Models\Produto::count() }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Produtos em Estoque</p>
                            <p class="text-2xl font-semibold">{{ \App\Models\Produto::withSum('estoque', 'quantidade')->get()->sum('estoque_sum_quantidade') }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Vendas Hoje</p>
                            <p class="text-2xl font-semibold">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
    </style>
    @endpush
</x-app-layout>
