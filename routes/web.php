<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;

Route::get('/', function () {
    return redirect()->route('produtos.index');
});

// Rotas de produtos
Route::resource('produtos', ProdutoController::class);
