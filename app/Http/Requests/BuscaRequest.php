<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:200',
            'page' => 'nullable|integer|min:1|max:2147483647',
        ];
    }
}
