<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

// Rota de teste
Route::get('/teste', function() {
    return 'Teste de rota funcionando!';
});

// Rota principal redireciona para a lista de produtos
Route::redirect('/', '/produtos');

// Rotas de autenticação
require __DIR__.'/auth.php';

// Rotas públicas
Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');

// Rotas protegidas por autenticação
Route::middleware(['auth', 'verified'])->group(function () {
    // Alternar status do produto
    Route::post('/produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])->name('produtos.toggle-status');
    // Página inicial
Route::get('/', function () {
    return redirect()->route('produtos.index');
})->name('home');

    // Rotas do carrinho de compras
    Route::prefix('carrinho')->name('carrinho.')->group(function () {
        // Visualizar carrinho
        Route::get('/', [CartController::class, 'index'])->name('index');
        
        // Adicionar item ao carrinho
        Route::post('/adicionar/{produto}', [CartController::class, 'adicionar'])
            ->name('adicionar')
            ->where('produto', '[0-9]+');
            
        // Atualizar quantidade de um item
        Route::post('/atualizar/{itemKey}', [CartController::class, 'atualizar'])
            ->name('atualizar')
            ->where('itemKey', '.+');
            
        // Remover item do carrinho
        Route::delete('/remover/{itemKey}', [CartController::class, 'remover'])
            ->name('remover')
            ->where('itemKey', '.+');
            
        // Calcular frete
        Route::post('/calcular-frete', [CartController::class, 'calcularFrete'])
            ->name('calcular-frete');
            
        // Finalizar compra
        Route::post('/finalizar', [CartController::class, 'finalizar'])
            ->name('finalizar');
            
        // Aplicar cupom
        Route::post('/aplicar-cupom', [CartController::class, 'applyCoupon'])
            ->name('aplicar-cupom');
            
        // Remover cupom
        Route::post('/remover-cupom', [CartController::class, 'removeCoupon'])
            ->name('remover-cupom');
    });
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de perfil do usuário
    Route::middleware(['auth', 'verified'])->group(function () {
        // Visualização e atualização do perfil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        
        // Atualização de senha
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password.update');
            
        // Gerenciamento de endereços
        Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])
            ->name('profile.addresses.store');
        Route::put('/profile/addresses/{endereco}', [ProfileController::class, 'updateAddress'])
            ->name('profile.addresses.update');
        Route::delete('/profile/addresses/{endereco}', [ProfileController::class, 'destroyAddress'])
            ->name('profile.addresses.destroy');
        Route::post('/profile/addresses/{endereco}/default', [ProfileController::class, 'setDefaultAddress'])
            ->name('profile.addresses.set-default');
    });

    // Rotas para pedidos
    Route::prefix('pedidos')->name('pedidos.')->group(function () {
        Route::get('/{pedido}', [PedidoController::class, 'show'])->name('show');
        Route::patch('/{pedido}/cancelar', [PedidoController::class, 'cancel'])->name('cancel');
    });

    // Rotas de administração de produtos - Temporariamente sem autenticação para depuração
    Route::prefix('produtos')->group(function () {
    Route::get('/create', [ProdutoController::class, 'create'])->name('produtos.create');
    Route::post('/', [ProdutoController::class, 'store'])->name('produtos.store');
    Route::get('/{produto}/edit', [ProdutoController::class, 'edit'])->name('produtos.edit');
    Route::put('/{produto}', [ProdutoController::class, 'update'])->name('produtos.update');
    Route::delete('/{produto}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');
});
});

// Rota de depuração temporária
Route::get('/debug', function() {
    return response()->json([
        'current_route' => request()->path(),
        'is_authenticated' => auth()->check(),
        'user' => auth()->user(),
        'is_admin' => auth()->check() ? auth()->user()->isAdmin() : false,
        'roles' => auth()->check() ? auth()->user()->getRoleNames() : [],
    ]);
});

// Rota de teste para admin
Route::get('/admin/test', function() {
    return 'Você é um administrador!';
})->middleware(['auth', 'admin']);

// Rota de exibição de produto deve vir por último para não conflitar com /create
Route::get('/produtos/{produto}', [ProdutoController::class, 'show'])->name('produtos.show');

// Rotas de administração de cupons
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->except(['show']);
});

// Rotas para gerenciamento de cupons no carrinho
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/carrinho/aplicar-cupom', [\App\Http\Controllers\CartController::class, 'applyCoupon'])
        ->name('carrinho.aplicar-cupom');
        
    Route::post('/carrinho/remover-cupom', [\App\Http\Controllers\CartController::class, 'removeCoupon'])
        ->name('carrinho.remover-cupom');
});
