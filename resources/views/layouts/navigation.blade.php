<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')">
                                {{ __('Cupons') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown / Auth Links -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Carrinho -->
                @if (!request()->routeIs('carrinho.index'))
                <div class="dropdown">
                    <button class="btn btn-light position-relative d-flex align-items-center" type="button" id="cartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://cdn-icons-png.flaticon.com/512/1170/1170678.png" alt="Carrinho" style="width: 26px; height: 26px;">
                        @php $cart = session('cart', []); $cartCount = array_sum(array_column($cart, 'quantidade')); @endphp
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">{{ $cartCount }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg" style="min-width: 350px; max-width: 400px;">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-semibold"><i class="bi bi-cart4 me-2"></i>Meu Carrinho</span>
                            <button type="button" class="btn-close" data-bs-dismiss="dropdown" aria-label="Fechar"></button>
                        </div>
                        <div style="max-height: 340px; overflow-y: auto;">
                            @if(count($cart))
                                @php
                                    $produtos = \App\Models\Produto::whereIn('id', array_column($cart, 'produto_id'))->get()->keyBy('id');
                                    $variacoes = \App\Models\Estoque::whereIn('id', array_filter(array_column($cart, 'variacao_id')))->get()->keyBy('id');
                                    $total = 0;
                                @endphp
                                @foreach($cart as $item)
                                    @php
                                        $produto = $produtos[$item['produto_id']] ?? null;
                                        $variacao = $item['variacao_id'] ? ($variacoes[$item['variacao_id']] ?? null) : null;
                                        $preco = $variacao && $variacao->preco ? $variacao->preco : ($produto ? $produto->preco : 0);
                                        $itemTotal = $preco * $item['quantidade'];
                                        $total += $itemTotal;
                                    @endphp
                                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                                        <img src="{{ $produto && $produto->imagem ? $produto->imagem : 'https://via.placeholder.com/50' }}" alt="{{ $produto ? $produto->nome : '' }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small mb-1">{{ $produto ? $produto->nome : 'Produto removido' }}</div>
                                            @if($variacao)
                                                <div class="text-muted small">{{ $variacao->variacao }}</div>
                                            @endif
                                            <div class="text-muted small">Qtd: {{ $item['quantidade'] }}</div>
                                        </div>
                                        <div class="fw-semibold text-end small" style="min-width: 70px;">R$ {{ number_format($itemTotal, 2, ',', '.') }}</div>
                                    </div>
                                @endforeach
                                <div class="p-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">Total</span>
                                        <span class="fw-bold text-primary">R$ {{ number_format($total, 2, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('carrinho.index') }}" class="btn btn-outline-primary w-100 mb-2">Ver carrinho</a>
                                </div>
                            @else
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-cart-x display-6 mb-2"></i>
                                    <div>Seu carrinho está vazio.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <!-- Fim Carrinho -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth
            @guest
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100">Entrar</a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Registrar</a>
            </div>
            @endguest

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
            @endauth
            @guest
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">Visitante</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    Entrar
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    Registrar
                </x-responsive-nav-link>
            </div>
            @endguest
        </div>
    </div>
</nav>
