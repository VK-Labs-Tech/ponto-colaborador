<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EditEmployeesController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'min:3', 'max:120'],
            'registration' => ['nullable', 'string', 'max:40'],
            'shift_start'  => ['required', 'date_format:H:i'],
            'lunch_start'  => ['required', 'date_format:H:i'],
            'lunch_end'    => ['required', 'date_format:H:i'],
            'shift_end'    => ['required', 'date_format:H:i'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $this->employeeService->edit(
            data: array_merge($validated, ['id' => $employee->id]),
            actor: 'auth()->user()->email',
            companyId: (int) session('company_id')
        );

        return redirect()->route('employees.index')
            ->with('status', 'Funcionário atualizado com sucesso.');
    }
}