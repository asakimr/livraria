<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LivroRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "Titulo" =>["required", "string", "max:40"],
            "Editora" => ["required", "string", "max:40"],
            "Edicao" => ["required", "integer"],
            "AnoPublicacao" => ["required","digits:4 "]
        ];
    }

    public function messages():array{
        return [
            #Título
            "Titulo.required" => "O título do livro é um campo obrigatório.",
            "Titulo.string"   => "O título deve ser um texto válido.",
            "Titulo.max"      => "O título não pode ultrapassar o limite de 40 caracteres.",

            #Editora
            "Editora.required" => "O nome da editora é obrigatório.",
            "Editora.string"   => "A editora deve ser um texto válido.",
            "Editora.max"      => "A editora não pode ultrapassar o limite de 40 caracteres.",

            #Edição
            "Edicao.required" => "O número da edição é obrigatório.",
            "Edicao.integer"  => "A edição deve ser um número inteiro válido.",

            #Ano de Publicação
            "AnoPublicacao.required" => "O ano de publicação é obrigatório.",
            "AnoPublicacao.digits" => "O ano de publicação deve ter exatamente 4 dígitos numéricos."
        ];
    }
}
