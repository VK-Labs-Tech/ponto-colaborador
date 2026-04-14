<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTimePunchRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'entry_1' => ['nullable', 'date_format:H:i'],
            'exit_1' => ['nullable', 'date_format:H:i'],
            'entry_2' => ['nullable', 'date_format:H:i'],
            'exit_2' => ['nullable', 'date_format:H:i'],
            'reason' => ['required', 'string', 'min:5'],
        ];
    }

    public function attributes()
    {
        return [
            'reason' => 'justificativa',
            'entry_1' => 'entrada 1',
            'exit_1' => 'saída 1',
            'entry_2' => 'entrada 2',
            'exit_2' => 'saída 2',
        ];
    }

    public function messages()
    {
        return [       
             'reason.min' => 'A justificativa deve conter ao menos 5 caracteres.',
             'reason.required' => 'A justificativa é obrigatória.',
        ];
    }
}
