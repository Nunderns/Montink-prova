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
            $price = $this->resolvePrice($variation, $product);
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
            $price = $this->resolvePrice($variation, $product);
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

    public function getItemPrice(int $produtoId, ?int $variacaoId): float
    {
        $product = Produto::find($produtoId);
        $variation = $variacaoId ? Estoque::find($variacaoId) : null;
        return $this->resolvePrice($variation, $product);
    }

    protected function resolvePrice(?Estoque $variation, Produto $product): float
    {
        if ($variation && isset($variation->preco) && $variation->preco !== null) {
            return $variation->preco;
        }

        return $product->preco;
    }

    protected function makeKey(int $produtoId, ?int $variacaoId): string
    {
        return $produtoId . '_' . ($variacaoId ?: '0');
    }
}
