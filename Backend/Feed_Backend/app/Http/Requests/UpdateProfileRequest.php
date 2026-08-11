<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name'     => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'username')->ignore($this->user()->id)],
            'bio'      => ['sometimes', 'nullable', 'string', 'max:500'],
            'avatar'   => ['sometimes', 'nullable', 'image', 'max:2048'],
        ];
    }
    public function messages(): array
    {
        return [
            'username.unique' => 'Esse nome de usuário já está em uso.',
            'bio.max' => 'A bio pode ter no máximo :max caracteres.',
            'avatar.image' => 'O avatar precisa ser uma imagem.',
            'avatar.max' => 'A imagem não pode passar de 2MB.',
        ];
    }
}
