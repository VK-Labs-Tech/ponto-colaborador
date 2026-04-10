<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Support\PinHasher;
use Illuminate\Support\Collection;

class EmployeeService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function listByCompany(int $companyId): Collection
    {
        return $this->employeeRepository->listByCompany($companyId);
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
