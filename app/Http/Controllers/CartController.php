<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use App\Services\CartService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        $cartItems = $this->cart->buildCartItems();
        $subtotal = $this->cart->calculateSubtotal();
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
        
        // Usa o método addItem do CartService que já faz a verificação de estoque
        $result = $this->cart->addItem(
            $produtoId, 
            $request->variacao_id, 
            $request->quantidade
        );
        
        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }
        
        return redirect()->route('carrinho.index')
            ->with('success', $result['message']);
    }
    
    public function atualizar(Request $request, $itemKey)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1'
        ]);
        
        // Usa o método updateQuantity do CartService que já faz a verificação de estoque
        $result = $this->cart->updateQuantity($itemKey, $request->quantidade);
        
        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }
        
        return redirect()->route('carrinho.index')
            ->with('success', $result['message']);
    }
    
    public function remover($itemKey)
    {
        $cart = $this->cart->getCart();
        
        // Log para depuração
        \Log::info('Tentando remover item do carrinho', [
            'item_key' => $itemKey,
            'carrinho_atual' => $cart,
            'chaves_disponiveis' => array_keys($cart)
        ]);
        
        if (isset($cart[$itemKey])) {
            $this->cart->removeItem($itemKey);
            $cart = $this->cart->getCart();

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
    
    /**
     * Calcula o frete para um determinado CEP
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularFrete(Request $request)
    {
        try {
            // Validação do CEP
            $request->validate([
                'cep' => ['required', 'string', 'regex:/^\d{5}-?\d{3}$/']
            ]);
            
            // Formata o CEP (remove traço se existir)
            $cep = str_replace('-', '', $request->cep);
            
            // Verifica se o carrinho está vazio
            $cart = $this->cart->getCart();
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seu carrinho está vazio.'
                ], 400);
            }
            
            // Tenta buscar o endereço usando o ViaCEP
            $endereco = $this->consultarViaCEP($cep);
            
            if (!$endereco) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível encontrar o endereço para o CEP informado.'
                ], 404);
            }
            
            // Calcula o frete baseado no valor do carrinho
            $subtotal = $this->cart->calculateSubtotal();
            $shipping = $this->cart->calcularFrete($subtotal);
            
            // Prepara os dados de retorno
            $response = [
                'success' => true,
                'endereco' => [
                    'logradouro' => $endereco['logradouro'] ?? 'Não informado',
                    'bairro' => $endereco['bairro'] ?? 'Não informado',
                    'localidade' => $endereco['localidade'] ?? 'Não informado',
                    'uf' => $endereco['uf'] ?? 'Não informado'
                ],
                'frete' => number_format($shipping, 2, '.', ''), // Formato para cálculo
                'frete_formatado' => 'R$ ' . number_format($shipping, 2, ',', '.'), // Formato para exibição
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'subtotal_formatado' => 'R$ ' . number_format($subtotal, 2, ',', '.'),
                'total' => number_format($subtotal + $shipping, 2, '.', ''),
                'total_formatado' => 'R$ ' . number_format($subtotal + $shipping, 2, ',', '.')
            ];
            
            // Log da operação
            \Log::info('Frete calculado com sucesso', [
                'cep' => $cep,
                'endereco' => $endereco,
                'subtotal' => $subtotal,
                'frete' => $shipping,
                'total' => $subtotal + $shipping
            ]);
            
            return response()->json($response);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Erro de validação
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido. Por favor, verifique o formato do CEP.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            // Erro genérico
            \Log::error('Erro ao calcular frete: ' . $e->getMessage(), [
                'cep' => $request->cep ?? null,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao calcular o frete. Por favor, tente novamente mais tarde.'
            ], 500);
        }
    }
    
    /**
     * Consulta o endereço no ViaCEP
     * 
     * @param  string  $cep
     * @return array|null
     */
    protected function consultarViaCEP($cep)
    {
        try {
            $url = "https://viacep.com.br/ws/{$cep}/json/";
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                throw new \Exception('Erro na requisição: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new \Exception("Erro ao consultar CEP. Código HTTP: {$httpCode}");
            }
            
            $endereco = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar resposta do ViaCEP: ' . json_last_error_msg());
            }
            
            if (isset($endereco['erro'])) {
                return null;
            }
            
            return $endereco;
            
        } catch (\Exception $e) {
            \Log::error('Erro na consulta ao ViaCEP: ' . $e->getMessage(), [
                'cep' => $cep,
                'exception' => $e
            ]);
            
            // Em caso de falha na API, retorna um endereço genérico
            return [
                'logradouro' => 'Não foi possível obter o endereço',
                'bairro' => 'Verifique o CEP informado',
                'localidade' => 'Cidade não identificada',
                'uf' => 'UF'
            ];
        }
    }
    
    public function finalizar(Request $request)
    {
        $cart = $this->cart->getCart();
        
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
        $subtotal = $this->cart->calculateSubtotal();
        $itensPedido = [];
        foreach ($cart as $item) {
            $produto = Produto::find($item['produto_id']);
            $variacao = $item['variacao_id'] ? Estoque::find($item['variacao_id']) : null;
            $preco = $this->cart->getItemPrice($produto->id, $variacao ? $variacao->id : null);
            $totalItem = $preco * $item['quantidade'];

            $itensPedido[] = [
                'produto_id' => $produto->id,
                'variacao_id' => $variacao ? $variacao->id : null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $preco,
                'total' => $totalItem,
            ];
        }
        
        // Usa o método do CartService para calcular o frete
        $frete = $this->cart->calcularFrete($subtotal);
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

            // Determinar o status inicial respeitando esquemas mais antigos
            $status = 'pending';
            try {
                $column = \DB::selectOne(
                    "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pedidos' AND COLUMN_NAME = 'status'"
                );
                if ($column && isset($column->COLUMN_TYPE) && str_contains($column->COLUMN_TYPE, 'pendente')) {
                    $status = 'pendente';
                }
            } catch (\Exception $e) {
                \Log::warning('Não foi possível detectar tipo da coluna status', ['error' => $e->getMessage()]);
                // Se a consulta falhar, usa o valor padrão
            }
            $pedido->status = $status;
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
                } elseif (Schema::hasColumn('pedido_items', 'variacao')) {
                    // Estruturas antigas armazenavam a descrição em "variacao"
                    $variation = Estoque::find($item['variacao_id']);
                    $pedidoItem->variacao = $variation ? $variation->variacao : null;
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
            $this->cart->clear();
            
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
                ->with('error', 'Ocorreu um erro ao processar seu pedido. Por favor, tente novamente. '
                      . ($e instanceof \Illuminate\Database\QueryException
                          ? 'Erro no banco de dados: ' . $e->getMessage()
                          : $e->getMessage()));
        }
    }
    
    // Método mantido para compatibilidade, mas a lógica foi movida para o CartService
    private function calculateShipping($subtotal)
    {
        return $this->cart->calcularFrete($subtotal);
    }
}
