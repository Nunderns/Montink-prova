<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
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
            'apelido' => ['nullable', 'string', 'max:50'],
            'cep' => ['required', 'string', 'size:9', 'regex:/^\d{5}-\d{3}$/'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'principal' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cep.required' => 'O CEP é obrigatório.',
            'cep.size' => 'O CEP deve ter 9 caracteres.',
            'cep.regex' => 'O CEP deve estar no formato 00000-000.',
            'logradouro.required' => 'O logradouro é obrigatório.',
            'logradouro.max' => 'O logradouro não pode ter mais de 255 caracteres.',
            'numero.required' => 'O número é obrigatório.',
            'numero.max' => 'O número não pode ter mais de 20 caracteres.',
            'complemento.max' => 'O complemento não pode ter mais de 255 caracteres.',
            'bairro.required' => 'O bairro é obrigatório.',
            'bairro.max' => 'O bairro não pode ter mais de 100 caracteres.',
            'cidade.required' => 'A cidade é obrigatória.',
            'cidade.max' => 'A cidade não pode ter mais de 100 caracteres.',
            'estado.required' => 'O estado é obrigatório.',
            'estado.size' => 'O estado deve ter 2 caracteres.',
            'referencia.max' => 'A referência não pode ter mais de 255 caracteres.',
            'apelido.max' => 'O apelido não pode ter mais de 50 caracteres.',
        ];
    }
}
