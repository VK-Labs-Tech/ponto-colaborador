<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class KioskRequest extends FormRequest
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
            'employee_id' => ['required', 'integer'],
            'pin' => ['required', 'string', 'min:4', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function attributes()
    {
        return [
            'employee_id' => 'ID do funcionário',
            'pin' => 'PIN',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'O campo ID do funcionário é obrigatório.',
            'employee_id.integer' => 'O campo ID do funcionário deve ser um número inteiro.',
            'pin.required' => 'O campo PIN é obrigatório.',
            'pin.string' => 'O campo PIN deve ser uma string.',
            'pin.min' => 'O campo PIN deve conter ao menos 4 caracteres.',
            'pin.max' => 'O campo PIN deve conter no máximo 4 caracteres.',
        ];
    }
}
