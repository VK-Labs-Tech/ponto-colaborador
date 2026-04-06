<?php

namespace App\Repositories\Eloquent;

use App\Models\MonthlyClosure;
use App\Repositories\Contracts\MonthlyClosureRepositoryInterface;

class MonthlyClosureRepository implements MonthlyClosureRepositoryInterface
{
    public function findByCompanyMonth(int $companyId, int $year, int $month): ?MonthlyClosure
    {
        return MonthlyClosure::query()
            ->where('company_id', $companyId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    public function create(array $data): MonthlyClosure
    {
        return MonthlyClosure::query()->create($data);
    }
}
