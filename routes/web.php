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

// Carrinho de compras
    Route::get('/carrinho', [CartController::class, 'index'])->name('carrinho.index');
    Route::post('/carrinho/calcular-frete', [CartController::class, 'calcularFrete'])->name('carrinho.calcular-frete');
Route::post('/carrinho/atualizar/{itemKey}', [CartController::class, 'atualizar'])->name('carrinho.atualizar');
Route::delete('/carrinho/remover/{itemKey}', [CartController::class, 'remover'])->name('carrinho.remover');
    Route::post('/carrinho/adicionar/{produto}', [CartController::class, 'adicionar'])->name('carrinho.adicionar');
    Route::post('/carrinho/finalizar', [CartController::class, 'finalizar'])->name('carrinho.finalizar');
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
        'routes' => \Illuminate\Support\Facades\Route::getRoutes()->getRoutes(),
        'current_route' => request()->path(),
        'is_authenticated' => auth()->check(),
        'user' => auth()->user(),
    ]);
});

// Rota de exibição de produto deve vir por último para não conflitar com /create
Route::get('/produtos/{produto}', [ProdutoController::class, 'show'])->name('produtos.show');
