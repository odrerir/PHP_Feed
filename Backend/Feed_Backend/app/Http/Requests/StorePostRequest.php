<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'caption' => ['nullable', 'string', 'max:2200'],
            'media'   => ['required', 'image', 'max:5120'],
        ];
    }
    public function messages(): array
    {
        return [
            'media.required' => 'Escolha uma imagem para o post.',
            'media.image' => 'O arquivo precisa ser uma imagem.',
            'media.max' => 'A imagem não pode passar de 5MB.',
            'caption.max' => 'A legenda pode ter no máximo :max caracteres.',
        ];
    }
}
