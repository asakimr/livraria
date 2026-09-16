<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RelatorioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'titulo' => 'nullable|string|max:40',
            'autor_id' => 'nullable|integer|min:1|max:4294967295',
            'assuntos' => 'nullable|array|list',
            'assuntos.*' => 'integer|min:1|max:4294967295|distinct',
        ];
    }
}
