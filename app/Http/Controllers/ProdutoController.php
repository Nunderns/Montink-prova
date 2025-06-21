<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::with('estoque')->latest()->paginate(10);
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProdutoRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Cria o produto
            $produto = Produto::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'preco' => $request->preco,
                'ativo' => true,
            ]);

            // Adiciona variações/estoque
            if ($request->has('variacoes')) {
                foreach ($request->variacoes as $variacao) {
                    $produto->estoque()->create([
                        'variacao' => $variacao['nome'],
                        'quantidade' => $variacao['quantidade'],
                        'quantidade_minima' => $variacao['quantidade_minima'] ?? 0,
                    ]);
                }
            } else {
                // Se não houver variações, cria um registro de estoque padrão
                $produto->estoque()->create([
                    'quantidade' => 0,
                    'quantidade_minima' => 0,
                ]);
            }


            DB::commit();
            return redirect()->route('produtos.index')
                ->with('success', 'Produto criado com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao criar produto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        $produto->load('estoque');
        return view('produtos.show', compact('produto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        $produto->load('estoque');
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutoRequest $request, Produto $produto)
    {
        DB::beginTransaction();
        
        try {
            // Atualiza o produto
            $produto->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'preco' => $request->preco,
            ]);

            // Atualiza ou cria variações/estoque
            if ($request->has('variacoes')) {
                $variacoesIds = [];
                
                foreach ($request->variacoes as $variacao) {
                    $estoqueData = [
                        'variacao' => $variacao['nome'],
                        'quantidade' => $variacao['quantidade'],
                        'quantidade_minima' => $variacao['quantidade_minima'] ?? 0,
                    ];
                    
                    if (isset($variacao['id'])) {
                        // Atualiza variação existente
                        $produto->estoque()->where('id', $variacao['id'])->update($estoqueData);
                        $variacoesIds[] = $variacao['id'];
                    } else {
                        // Cria nova variação
                        $newEstoque = $produto->estoque()->create($estoqueData);
                        $variacoesIds[] = $newEstoque->id;
                    }
                }
                
                // Remove variações não enviadas
                $produto->estoque()->whereNotIn('id', $variacoesIds)->delete();
            }

            DB::commit();
            return redirect()->route('produtos.index')
                ->with('success', 'Produto atualizado com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        DB::beginTransaction();
        
        try {
            // Remove o estoque primeiro por causa da chave estrangeira
            $produto->estoque()->delete();
            $produto->delete();
            
            DB::commit();
            return redirect()->route('produtos.index')
                ->with('success', 'Produto removido com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover produto: ' . $e->getMessage());
        }
    }
}
