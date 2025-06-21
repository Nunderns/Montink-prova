<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply auth middleware to all methods except index and show
        $this->middleware('auth')->except(['index', 'show']);
    }

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
            $data = $request->validated();
            $data['slug'] = Str::slug($data['nome']);
            
            // Handle image upload
            if ($request->hasFile('imagem')) {
                $path = $request->file('imagem')->store('produtos', 'public');
                $data['imagem'] = $path;
            }
            
            $produto = Produto::create($data);
            
            // Adiciona variações de estoque
            if ($request->has('variacoes')) {
                foreach ($request->variacoes as $variacao) {
                    $produto->estoque()->create([
                        'variacao' => $variacao['nome'],
                        'quantidade' => $variacao['quantidade'],
                        'quantidade_minima' => $variacao['quantidade_minima'] ?? 0,
                    ]);
                }
            } else {
                // Cria um registro de estoque padrão se não houver variações
                $produto->estoque()->create([
                    'variacao' => 'Padrão',
                    'quantidade' => $request->estoque,
                    'quantidade_minima' => 5,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('produtos.index')
                ->with('success', 'Produto criado com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar produto: ' . $e->getMessage());
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
            $data = $request->validated();
            
            if ($request->has('nome')) {
                $data['slug'] = Str::slug($request->nome);
            }
            
            // Handle image upload
            if ($request->hasFile('imagem')) {
                // Remove a imagem antiga se existir
                if ($produto->imagem) {
                    Storage::disk('public')->delete($produto->imagem);
                }
                
                $path = $request->file('imagem')->store('produtos', 'public');
                $data['imagem'] = $path;
            } elseif ($request->has('remove_imagem')) {
                // Remove a imagem se o checkbox estiver marcado
                if ($produto->imagem) {
                    Storage::disk('public')->delete($produto->imagem);
                    $data['imagem'] = null;
                }
            }
            
            $produto->update($data);
            
            // Atualiza ou cria variações de estoque
            if ($request->has('variacoes')) {
                $variacoesIds = [];
                
                foreach ($request->variacoes as $variacao) {
                    if (isset($variacao['id'])) {
                        // Atualiza variação existente
                        $estoque = Estoque::where('id', $variacao['id'])
                            ->where('produto_id', $produto->id)
                            ->first();
                            
                        if ($estoque) {
                            $estoque->update([
                                'variacao' => $variacao['nome'],
                                'quantidade' => $variacao['quantidade'],
                                'quantidade_minima' => $variacao['quantidade_minima'] ?? 0,
                            ]);
                            $variacoesIds[] = $estoque->id;
                        }
                    } else {
                        // Cria nova variação
                        $novoEstoque = $produto->estoque()->create([
                            'variacao' => $variacao['nome'],
                            'quantidade' => $variacao['quantidade'],
                            'quantidade_minima' => $variacao['quantidade_minima'] ?? 0,
                        ]);
                        $variacoesIds[] = $novoEstoque->id;
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
            return back()->with('error', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        DB::beginTransaction();
        
        try {
            // Remove a imagem se existir
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            
            // Remove todas as variações de estoque
            $produto->estoque()->delete();
            
            // Remove o produto
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
