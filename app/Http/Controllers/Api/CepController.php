<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CepService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CepController extends Controller
{
    protected $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    /**
     * Busca os dados de um CEP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function buscarCep(Request $request): JsonResponse
    {
        $request->validate([
            'cep' => 'required|string|min:8|max:9',
        ]);

        $dados = $this->cepService->buscarCep($request->cep);

        if ($dados === null) {
            return response()->json([
                'success' => false,
                'message' => 'CEP não encontrado ou inválido.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dados,
        ]);
    }
}
