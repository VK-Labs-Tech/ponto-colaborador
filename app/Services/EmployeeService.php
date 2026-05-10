<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Support\PinHasher;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function listByCompany(int $companyId): Collection
    {
        $dados = $this->employeeRepository->listByCompany($companyId);
        // dd($dados);
        return $dados;
    }

    public function edit(array $data, string $actor, int $companyId): void{

        if (Employee::where('registration', $data['registration'])->exists()) {
             throw ValidationException::withMessages([
                'registration' => 'A matricula informada já existe.',
            ]);
        }
        $edit = $this->employeeRepository->edit($data, $companyId, $actor);

         $this->auditLogService->log(
            companyId: $companyId,
            actor: $actor,
            event: 'employee_edited',
            entityType: 'employee',
            entityId: $edit->id,
            payload: [
                'name' => $edit->name,
                'registration' => $edit->registration,
                'shift_start' => $edit->shift_start,
                'lunch_start' => $edit->lunch_start,
                'lunch_end' => $edit->lunch_end,
                'shift_end' => $edit->shift_end,
            ]
        );

    }

    public function create(int $companyId, array $data, string $actor): void
    {
        $employee = $this->employeeRepository->create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'registration' => $data['registration'] ?? null,
            'pin' => PinHasher::hash((string) $data['pin']),
            'shift_start' => $data['shift_start'],
            'lunch_start' => $data['lunch_start'],
            'lunch_end' => $data['lunch_end'],
            'shift_end' => $data['shift_end'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $this->auditLogService->log(
            companyId: $companyId,
            actor: $actor,
            event: 'employee_created',
            entityType: 'employee',
            entityId: $employee->id,
            payload: [
                'name' => $employee->name,
                'registration' => $employee->registration,
                'shift_start' => $employee->shift_start,
                'lunch_start' => $employee->lunch_start,
                'lunch_end' => $employee->lunch_end,
                'shift_end' => $employee->shift_end,
            ]
        );
    }
}
