<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Estoque;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): array
    {
        return Session::get('cart', []);
    }

    public function saveCart(array $cart): void
    {
        Session::put('cart', $cart);
    }

    public function clear(): void
    {
        Session::forget('cart');
    }

    public function addItem(int $produtoId, ?int $variacaoId, int $quantidade): void
    {
        $cart = $this->getCart();
        $key = $this->makeKey($produtoId, $variacaoId);
        if (isset($cart[$key])) {
            $cart[$key]['quantidade'] += $quantidade;
        } else {
            $cart[$key] = [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
                'quantidade' => $quantidade,
            ];
        }
        $this->saveCart($cart);
    }

    public function updateQuantity(string $key, int $quantidade): bool
    {
        $cart = $this->getCart();
        if (!isset($cart[$key])) {
            return false;
        }
        $cart[$key]['quantidade'] = $quantidade;
        $this->saveCart($cart);
        return true;
    }

    public function removeItem(string $key): bool
    {
        $cart = $this->getCart();
        if (!isset($cart[$key])) {
            return false;
        }
        unset($cart[$key]);
        $this->saveCart($cart);
        return true;
    }

    public function calculateSubtotal(): float
    {
        $subtotal = 0;
        foreach ($this->getCart() as $item) {
            $product = Produto::find($item['produto_id']);
            $variation = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
            $price = $variation && $variation->preco ? $variation->preco : $product->preco;
            $subtotal += $price * $item['quantidade'];
        }
        return $subtotal;
    }

    public function buildCartItems(): array
    {
        $items = [];
        foreach ($this->getCart() as $key => $item) {
            $product = Produto::find($item['produto_id']);
            $variation = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
            $price = $variation && $variation->preco ? $variation->preco : $product->preco;
            $items[$key] = [
                'produto' => $product,
                'variacao' => $variation,
                'quantidade' => $item['quantidade'],
                'preco' => $price,
                'total' => $price * $item['quantidade'],
            ];
        }
        return $items;
    }

    protected function makeKey(int $produtoId, ?int $variacaoId): string
    {
        return $produtoId . '_' . ($variacaoId ?: '0');
    }
}
