<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService,
        private readonly SubscriptionService $subscriptionService
    ) {
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

        if (! $this->subscriptionService->ensureCanCreateEmployee($companyId)) {
            throw ValidationException::withMessages([
                'name' => 'Limite de colaboradores do plano atingido.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'registration' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('employees', 'registration')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'pin' => ['required', 'regex:/^\d{4,10}$/'],
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
