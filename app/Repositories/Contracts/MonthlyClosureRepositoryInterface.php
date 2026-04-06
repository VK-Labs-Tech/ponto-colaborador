<?php

namespace App\Repositories\Contracts;

use App\Models\MonthlyClosure;

interface MonthlyClosureRepositoryInterface
{
    public function findByCompanyMonth(int $companyId, int $year, int $month): ?MonthlyClosure;

    public function create(array $data): MonthlyClosure;
}
