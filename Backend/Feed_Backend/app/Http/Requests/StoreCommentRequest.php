<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
        return ['content' => ['required', 'string', 'max:1000']];
    }
    public function messages(): array
    {
        return [
            'content.required' => 'Escreva algo para comentar.',
            'content.max' => 'O comentário pode ter no máximo :max caracteres.',
        ];
    }
}
