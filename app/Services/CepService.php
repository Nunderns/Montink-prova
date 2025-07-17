<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CepService
{
    /**
     * Busca os dados de um CEP na API ViaCEP
     *
     * @param string $cep
     * @return array|null
     */
    public function buscarCep(string $cep): ?array
    {
        try {
            // Remove caracteres não numéricos
            $cep = preg_replace('/[^0-9]/', '', $cep);
            
            // Verifica se o CEP tem 8 dígitos
            if (strlen($cep) !== 8) {
                return null;
            }
            
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $dados = $response->json();
                
                // Verifica se o CEP foi encontrado
                if (isset($dados['erro']) && $dados['erro'] === true) {
                    return null;
                }
                
                return [
                    'cep' => $dados['cep'] ?? null,
                    'logradouro' => $dados['logradouro'] ?? null,
                    'complemento' => $dados['complemento'] ?? null,
                    'bairro' => $dados['bairro'] ?? null,
                    'cidade' => $dados['localidade'] ?? null,
                    'estado' => $dados['uf'] ?? null,
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar CEP: ' . $e->getMessage());
            return null;
        }
    }
}
