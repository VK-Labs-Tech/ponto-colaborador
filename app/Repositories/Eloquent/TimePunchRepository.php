<?php

namespace App\Repositories\Eloquent;

use App\Models\TimePunch;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TimePunchRepository implements TimePunchRepositoryInterface
{
    public function create(array $data): TimePunch
    {
        return TimePunch::query()->create($data);
    }

    public function listByCompanyPeriod(int $companyId, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return TimePunch::query()
            ->with('employee')
            ->where('company_id', $companyId)
            ->whereBetween('punched_at', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->orderBy('punched_at')
            ->get();
    }

    public function listByEmployeePeriod(int $employeeId, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return TimePunch::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('punched_at', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->orderBy('punched_at')
            ->get();
    }

    public function firstPunchOfDay(int $employeeId, CarbonInterface $day): ?TimePunch
    {
        return TimePunch::query()
            ->where('employee_id', $employeeId)
            ->whereDate('punched_at', $day->toDateString())
            ->orderBy('punched_at')
            ->first();
    }

    public function hasPunchOnDate(int $employeeId, CarbonInterface $date): bool
    {
        return TimePunch::query()
            ->where('employee_id', $employeeId)
            ->whereDate('punched_at', $date->toDateString())
            ->exists();
    }

    public function deleteByEmployeeDate(int $employeeId, CarbonInterface $date): void
    {
        TimePunch::query()
            ->where('employee_id', $employeeId)
            ->whereDate('punched_at', $date->toDateString())
            ->delete();
    }
}
