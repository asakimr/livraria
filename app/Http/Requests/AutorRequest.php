<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AutorRequest extends FormRequest
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
            "Nome" => ["required", "string", "max:40"]
        ];

    }

    public function messages():array{
        return [
            "Nome.required" => "Nome do autor é um campo obrigatório!",
            "Nome.max" => "Nome do autor possuí o limite de 40 caracteres."
        ];
    }
}
