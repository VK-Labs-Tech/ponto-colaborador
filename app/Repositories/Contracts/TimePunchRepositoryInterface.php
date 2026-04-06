<?php

namespace App\Repositories\Contracts;

use App\Models\TimePunch;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

interface TimePunchRepositoryInterface
{
    public function create(array $data): TimePunch;

    public function listByCompanyPeriod(int $companyId, CarbonInterface $from, CarbonInterface $to): Collection;

    public function listByEmployeePeriod(int $employeeId, CarbonInterface $from, CarbonInterface $to): Collection;

    public function firstPunchOfDay(int $employeeId, CarbonInterface $day): ?TimePunch;

    public function hasPunchOnDate(int $employeeId, CarbonInterface $date): bool;

    public function deleteByEmployeeDate(int $employeeId, CarbonInterface $date): void;
}
