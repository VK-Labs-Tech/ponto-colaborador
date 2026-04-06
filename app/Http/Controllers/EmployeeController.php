<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService)
    {
    }

    public function index()
    {
        $companyId = (int) session('company_id');

        return view('employees.index', [
            'employees' => $this->employeeService->listByCompany($companyId),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = (int) session('company_id');

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'registration' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('employees', 'registration')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'pin' => ['required', 'string', 'min:4', 'max:10'],
            'shift_start' => ['required', 'date_format:H:i'],
            'shift_end' => ['required', 'date_format:H:i'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->employeeService->create(
            companyId: $companyId,
            data: $validated,
            actor: (string) session('company_name', 'empresa')
        );

        return redirect()->route('employees.index')->with('status', 'Funcionario cadastrado com sucesso.');
    }
}
