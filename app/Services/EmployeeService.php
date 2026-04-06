<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
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
            'pin' => $data['pin'],
            'shift_start' => $data['shift_start'],
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
            ]
        );
    }
}
