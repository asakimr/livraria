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

    /** @return array<string, mixed> */
    public function validationData(): array
    {
        $dados = $this->all();
        # Normaliza apenas a cópia validada; o formulário mantém o valor original em caso de erro.
        if (! $this->is('api/*') && isset($dados['Valor']) && is_string($dados['Valor'])) {
            $valor = $dados['Valor'];
            if (preg_match('/^\d{1,3}(?:\.\d{3})*,\d{2}$|^\d+(?:,\d{1,2})?$/D', $valor)) {
                $dados['Valor'] = str_replace(['.', ','], ['', '.'], $valor);
            } else {
                $dados['Valor'] = null;
            }
        }

        return $dados;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "Titulo" => ["required", "string", "max:40"],
            "Editora" => ["required", "string", "max:40"],
            "Edicao" => ["required", "integer", "min:1", "max:2147483647"],
            "AnoPublicacao" => ["required", "integer", "digits:4"],
            "Valor" => ["required", "numeric", "min:0", "max:999999.99", "regex:/^\d+(?:\.\d{1,2})?$/D"],

            'autores' => 'required|array|list|min:1',
            'autores.*' => [$this->is('api/*') ? 'integer:strict' : 'integer', 'distinct', 'exists:Autor,CodAu'],

            'assuntos' => 'required|array|list|min:1',
            'assuntos.*' => [$this->is('api/*') ? 'integer:strict' : 'integer', 'distinct', 'exists:Assunto,codAs'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array{
        return [
            # Título
            "Titulo.required" => "O título do livro é um campo obrigatório.",
            "Titulo.string" => "O título deve ser um texto válido.",
            "Titulo.max" => "O título não pode ultrapassar o limite de 40 caracteres.",

            # Editora
            "Editora.required" => "O nome da editora é obrigatório.",
            "Editora.string" => "A editora deve ser um texto válido.",
            "Editora.max" => "A editora não pode ultrapassar o limite de 40 caracteres.",

            # Edição
            "Edicao.required" => "O número da edição é obrigatório.",
            "Edicao.integer" => "A edição deve ser um número inteiro válido.",

            # Ano de Publicação
            "AnoPublicacao.required" => "O ano de publicação é obrigatório.",
            "AnoPublicacao.digits" => "O ano de publicação deve ter exatamente 4 dígitos numéricos.",

            # Valor
            'Valor.required' => 'O campo Valor é obrigatório.',
            'Valor.regex' => 'Informe um valor com no máximo duas casas decimais.',
            'Valor.numeric' => 'O formato do valor informado é inválido.',
            'Valor.min' => 'O valor do livro não pode ser negativo.',
            'Valor.max' => 'O valor máximo permitido é de R$ 999.999,99.',

            # Autores
            'autores.required' => 'É obrigatório selecionar pelo menos um autor.',
            'autores.min' => 'É obrigatório selecionar pelo menos um autor.',
            'autores.*.exists' => 'Um dos autores selecionados é inválido.',

            # Assuntos
            'assuntos.required' => 'É obrigatório selecionar pelo menos um assunto.',
            'assuntos.min' => 'É obrigatório selecionar pelo menos um assunto.',
            'assuntos.*.exists' => 'Um dos assuntos selecionados é inválido.',
        ];
    }
}
