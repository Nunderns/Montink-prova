<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'variacoes' => 'nullable|array',
            'variacoes.*.nome' => 'required_with:variacoes|string|max:100',
            'variacoes.*.quantidade' => 'required_with:variacoes|integer|min:0',
            'variacoes.*.quantidade_minima' => 'nullable|integer|min:0',
        ];
    }
}
