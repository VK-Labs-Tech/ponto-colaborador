<?php

namespace App\Repositories\Contracts;

use App\Models\Employee;
use Illuminate\Support\Collection;

interface EmployeeRepositoryInterface
{
    public function findById(int $id): ?Employee;
    public function findByCompanyAndPin(int $companyId, string $pin): ?Employee;
    public function listActiveByCompany(int $companyId): Collection;
    public function listByCompany(int $companyId): Collection;
    public function create(array $data): Employee;
    public function edit(array $data, int $companyId, string $actor): Employee;
    public function updatePin(int $employeeId, string $pin): void;
}
