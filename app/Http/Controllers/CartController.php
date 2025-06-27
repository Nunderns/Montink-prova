<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        $shipping = $this->calculateShipping($subtotal);
        
        // Calculate subtotal and get product details
        $cartItems = [];
        foreach ($cart as $itemKey => $item) {
            $product = Produto::find($item['produto_id']);
            $variation = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
            
            $price = $variation && $variation->preco ? $variation->preco : $product->preco;
            $itemTotal = $price * $item['quantidade'];
            $subtotal += $itemTotal;
            
            $cartItems[$itemKey] = [
                'produto' => $product,
                'variacao' => $variation,
                'quantidade' => $item['quantidade'],
                'preco' => $price,
                'total' => $itemTotal
            ];
        }
        
        // Recalculate shipping with final subtotal
        $shipping = $this->calculateShipping($subtotal);
        $total = $subtotal + $shipping;
        
        // Buscar histórico de pedidos do usuário autenticado
        $pedidos = [];
        if (auth()->check()) {
            $pedidos = \App\Models\Pedido::with('itens')
                ->where('cliente_id', auth()->id())
                ->orderByDesc('created_at')
                ->get();
        }

        return view('carrinho.index', [
            'itens' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'pedidos' => $pedidos
        ]);
    }
    
    public function adicionar(Request $request, $produtoId)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1',
            'variacao_id' => 'nullable|exists:estoque,id'
        ]);
        
        $produto = Produto::findOrFail($produtoId);
        $variacao = $request->variacao_id ? Estoque::findOrFail($request->variacao_id) : null;
        
        // Verifica se há estoque suficiente
        if ($variacao && $variacao->quantidade < $request->quantidade) {
            return back()->with('error', 'Quantidade em estoque insuficiente para esta variação.');
        } elseif (!$variacao && $produto->estoque->sum('quantidade') < $request->quantidade) {
            return back()->with('error', 'Quantidade em estoque insuficiente.');
        }
        
        $cart = Session::get('cart', []);
        $itemKey = $produtoId . '_' . ($request->variacao_id ?: '0');
        
        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantidade'] += $request->quantidade;
        } else {
            $cart[$itemKey] = [
                'produto_id' => $produtoId,
                'variacao_id' => $request->variacao_id,
                'quantidade' => $request->quantidade
            ];
        }
        
        Session::put('cart', $cart);
        
        return redirect()->route('carrinho.index')
            ->with('success', 'Produto adicionado ao carrinho!');
    }
    
    public function atualizar(Request $request, $itemKey)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1'
        ]);
        
        $cart = Session::get('cart', []);
        
        if (!isset($cart[$itemKey])) {
            return back()->with('error', 'Item não encontrado no carrinho.');
        }
        
        // Verifica estoque
        $item = $cart[$itemKey];
        $variacao = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
        
        if ($variacao && $variacao->quantidade < $request->quantidade) {
            return back()->with('error', 'Quantidade em estoque insuficiente para esta variação.');
        } elseif (!$variacao) {
            $produto = Produto::find($item['produto_id']);
            if ($produto->estoque->sum('quantidade') < $request->quantidade) {
                return back()->with('error', 'Quantidade em estoque insuficiente.');
            }
        }
        
        $cart[$itemKey]['quantidade'] = $request->quantidade;
        Session::put('cart', $cart);
        
        return redirect()->route('carrinho.index')
            ->with('success', 'Carrinho atualizado!');
    }
    
    public function remover($itemKey)
    {
        $cart = Session::get('cart', []);
        
        // Log para depuração
        \Log::info('Tentando remover item do carrinho', [
            'item_key' => $itemKey,
            'carrinho_atual' => $cart,
            'chaves_disponiveis' => array_keys($cart)
        ]);
        
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            Session::put('cart', $cart);
            
            \Log::info('Item removido com sucesso', ['novo_carrinho' => $cart]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removido do carrinho.',
                    'cart_count' => count($cart),
                    'debug' => [
                        'item_key' => $itemKey,
                        'cart_keys' => array_keys($cart)
                    ]
                ]);
            }
            
            return back()->with('success', 'Item removido do carrinho.');
        }
        
        \Log::warning('Item não encontrado no carrinho', [
            'item_key' => $itemKey,
            'chaves_disponiveis' => array_keys($cart)
        ]);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado no carrinho.',
                'debug' => [
                    'item_key' => $itemKey,
                    'cart_keys' => array_keys($cart)
                ]
            ], 404);
        }
        
        return back()->with('error', 'Item não encontrado no carrinho.');
    }
    
    public function calcularFrete(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|size:9|regex:/^\d{5}-\d{3}$/'
        ]);
        
        // Simula consulta ao ViaCEP
        $cep = str_replace('-', '', $request->cep);
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $endereco = json_decode($response, true);
        
        if (isset($endereco['erro'])) {
            return response()->json([
                'success' => false,
                'message' => 'CEP não encontrado.'
            ]);
        }
        
        // Calcula o frete baseado no valor do carrinho
        $subtotal = 0;
        $cart = Session::get('cart', []);
        
        foreach ($cart as $item) {
            $product = Produto::find($item['produto_id']);
            $variation = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
            
            $price = $variation && $variation->preco ? $variation->preco : $product->preco;
            $subtotal += $price * $item['quantidade'];
        }
        
        $shipping = $this->calculateShipping($subtotal);
        
        return response()->json([
            'success' => true,
            'endereco' => [
                'logradouro' => $endereco['logradouro'] ?? '',
                'bairro' => $endereco['bairro'] ?? '',
                'cidade' => $endereco['localidade'] ?? '',
                'uf' => $endereco['uf'] ?? ''
            ],
            'frete' => number_format($shipping, 2, ',', '.'),
            'subtotal' => number_format($subtotal, 2, ',', '.'),
            'total' => number_format($subtotal + $shipping, 2, ',', '.')
        ]);
    }
    
    public function finalizar(Request $request)
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('carrinho.index')
                ->with('error', 'Seu carrinho está vazio.');
        }
        
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Você precisa estar logado para finalizar a compra.');
        }
        
        // Calcular totais
        $subtotal = 0;
        $itensPedido = [];
        
        foreach ($cart as $item) {
            $produto = Produto::find($item['produto_id']);
            $variacao = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
            
            $preco = $variacao ? $variacao->preco : $produto->preco;
            $totalItem = $preco * $item['quantidade'];
            $subtotal += $totalItem;
            
            $itensPedido[] = [
                'produto_id' => $produto->id,
                'variacao_id' => $variacao ? $variacao->id : null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $preco,
                'total' => $totalItem
            ];
        }
        
        $frete = $this->calculateShipping($subtotal);
        $total = $subtotal + $frete;

        // Garantir que a tabela correta exista mesmo em bancos antigos
        if (!Schema::hasTable('pedido_items') && Schema::hasTable('pedido_itens')) {
            Schema::rename('pedido_itens', 'pedido_items');
        }
        
        // Iniciar transação para garantir a integridade dos dados
        \DB::beginTransaction();
        
        try {
            // Criar o pedido
            $pedido = new \App\Models\Pedido();
            $pedido->codigo = 'PED' . time() . strtoupper(\Str::random(5));
            $pedido->cliente_id = auth()->id();
            $pedido->valor_total = $subtotal;
            $pedido->desconto = 0; // Pode ser ajustado se houver cupom
            if (Schema::hasColumn('pedidos', 'frete')) {
                $pedido->frete = $frete;
            }
            $pedido->valor_final = $total;
            $pedido->status = 'pending';
            if (Schema::hasColumn('pedidos', 'forma_pagamento')) {
                $pedido->forma_pagamento = $request->input('forma_pagamento', 'pix');
            }
            $pedido->save();
            
            // Adicionar itens ao pedido
            foreach ($itensPedido as $item) {
                $pedidoItem = new \App\Models\PedidoItem();
                $pedidoItem->pedido_id = $pedido->id;
                $pedidoItem->produto_id = $item['produto_id'];
                if (Schema::hasColumn('pedido_items', 'variacao_id')) {
                    $pedidoItem->variacao_id = $item['variacao_id'];
                } elseif (Schema::hasColumn('pedido_items', 'estoque_id')) {
                    $pedidoItem->estoque_id = $item['variacao_id'];
                }
                $pedidoItem->quantidade = $item['quantidade'];
                $pedidoItem->preco_unitario = $item['preco_unitario'];
                if (Schema::hasColumn('pedido_items', 'total')) {
                    $pedidoItem->total = $item['total'];
                } elseif (Schema::hasColumn('pedido_items', 'subtotal')) {
                    $pedidoItem->subtotal = $item['total'];
                }
                $pedidoItem->save();
                
                // Atualizar estoque
                if ($item['variacao_id']) {
                    $estoque = Estoque::find($item['variacao_id']);
                    if ($estoque) {
                        $estoque->quantidade -= $item['quantidade'];
                        $estoque->save();
                    }
                } else {
                    // Para produtos sem variação específica, atualiza o estoque
                    // geral do produto na tabela de estoque (linha sem variação).
                    $estoqueGeral = Estoque::where('produto_id', $item['produto_id'])
                        ->whereNull('variacao')
                        ->first();

                    if ($estoqueGeral) {
                        $estoqueGeral->quantidade -= $item['quantidade'];
                        $estoqueGeral->save();
                    }
                }
            }
            
            // Se chegou até aqui, tudo deu certo. Confirmar a transação.
            \DB::commit();
            
            // Log para depuração
            \Log::info('Pedido criado com sucesso', [
                'pedido_id' => $pedido->id,
                'codigo' => $pedido->codigo,
                'cliente_id' => $pedido->cliente_id,
                'valor_total' => $pedido->valor_total
            ]);
            
            // Limpar o carrinho após a finalização
            Session::forget('cart');
            
            // Log do redirecionamento
            $redirectUrl = route('pedidos.show', ['pedido' => $pedido->id]);
            \Log::info('Redirecionando para', ['url' => $redirectUrl]);
            
            // Redirecionar para a página de confirmação
            return redirect($redirectUrl)
                ->with('success', 'Pedido realizado com sucesso! Número do pedido: ' . $pedido->codigo);
                
        } catch (\Exception $e) {
            // Em caso de erro, desfaz as alterações no banco de dados
            \DB::rollBack();
            
            // Log do erro para depuração
            \Log::error('Erro ao finalizar pedido: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->route('carrinho.index')
                ->with('error', 'Ocorreu um erro ao processar seu pedido. Por favor, tente novamente. ' . 
                      ($e instanceof \Illuminate\Database\QueryException ? 'Erro no banco de dados.' : ''));
        }
    }
    
    private function calculateShipping($subtotal)
    {
        if ($subtotal > 200) {
            return 0; // Frete grátis
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
}
