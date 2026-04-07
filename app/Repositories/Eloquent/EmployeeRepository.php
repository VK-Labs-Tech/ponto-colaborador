<?php

namespace App\Repositories\Eloquent;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Support\PinHasher;
use Illuminate\Support\Collection;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function findById(int $id): ?Employee
    {
        return Employee::query()->find($id);
    }

    public function findByCompanyAndPin(int $companyId, string $pin): ?Employee
    {
        return Employee::query()
            ->where('company_id', $companyId)
            ->whereIn('pin', [PinHasher::hash($pin), trim($pin)])
            ->where('is_active', true)
            ->first();
    }

    public function listActiveByCompany(int $companyId): Collection
    {
        return Employee::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function listByCompany(int $companyId): Collection
    {
        return Employee::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Employee
    {
        return Employee::query()->create($data);
    }

    public function updatePin(int $employeeId, string $pin): void
    {
        Employee::query()
            ->whereKey($employeeId)
            ->update(['pin' => $pin]);
    }
}
