<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MonthlyClosureRequest extends FormRequest
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
                'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],

        ];
    }

    public function attributes()
    {
        return [
            'year' => 'ano',
            'month' => 'mês',
        ];
    }

    public function messages()
    {
        return [
                'year.required' => 'O campo ano é obrigatório.',
                'year.integer' => 'O campo ano deve ser um número inteiro.',
                'year.min' => 'O campo ano deve ser no mínimo 2000.',
                'year.max' => 'O campo ano deve ser no máximo 2100.',
                'month.required' => 'O campo mês é obrigatório.',
                'month.integer' => 'O campo mês deve ser um número inteiro.',
                'month.min' => 'O campo mês deve ser no mínimo 1.',
                'month.max' => 'O campo mês deve ser no máximo 12.',
        ];
    }
}
