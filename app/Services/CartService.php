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

    public function addItem(int $produtoId, ?int $variacaoId, int $quantidade): array
    {
        $cart = $this->getCart();
        $key = $this->makeKey($produtoId, $variacaoId);
        
        // Verificar se há estoque suficiente
        $estoqueDisponivel = $this->verificarEstoque($produtoId, $variacaoId);
        
        if ($estoqueDisponivel < $quantidade) {
            return [
                'success' => false,
                'message' => 'Quantidade em estoque insuficiente.',
                'estoque_disponivel' => $estoqueDisponivel
            ];
        }
        
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
        
        return [
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!'
        ];
    }
    
    /**
     * Verifica a quantidade disponível em estoque
     */
    public function verificarEstoque(int $produtoId, ?int $variacaoId): int
    {
        if ($variacaoId) {
            $estoque = \App\Models\Estoque::find($variacaoId);
            return $estoque ? $estoque->quantidade : 0;
        } else {
            // Para produtos sem variação, soma todo o estoque disponível
            return \App\Models\Estoque::where('produto_id', $produtoId)
                ->sum('quantidade');
        }
    }

    public function updateQuantity(string $key, int $quantidade): array
    {
        $cart = $this->getCart();
        if (!isset($cart[$key])) {
            return [
                'success' => false,
                'message' => 'Item não encontrado no carrinho.'
            ];
        }
        
        // Verificar se há estoque suficiente
        $item = $cart[$key];
        $estoqueDisponivel = $this->verificarEstoque($item['produto_id'], $item['variacao_id']);
        
        if ($quantidade > $estoqueDisponivel) {
            return [
                'success' => false,
                'message' => 'Quantidade em estoque insuficiente.',
                'estoque_disponivel' => $estoqueDisponivel
            ];
        }
        
        $cart[$key]['quantidade'] = $quantidade;
        $this->saveCart($cart);
        
        return [
            'success' => true,
            'message' => 'Quantidade atualizada com sucesso!'
        ];
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
    
    /**
     * Calcula o valor do frete com base no subtotal
     */
    public function calcularFrete(float $subtotal): float
    {
        if ($subtotal > 200) {
            return 0; // Frete grátis
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
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
