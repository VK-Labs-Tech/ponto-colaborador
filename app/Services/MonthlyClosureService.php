<?php

namespace App\Services;

use App\Models\MonthlyClosure;
use App\Repositories\Contracts\MonthlyClosureRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class MonthlyClosureService
{
    public function __construct(
        private readonly MonthlyClosureRepositoryInterface $monthlyClosureRepository,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function isClosed(int $companyId, Carbon $date): bool
    {
        return (bool) $this->monthlyClosureRepository->findByCompanyMonth(
            $companyId,
            (int) $date->year,
            (int) $date->month
        );
    }

    public function closeMonth(int $companyId, int $year, int $month, string $actor): MonthlyClosure
    {
        $existing = $this->monthlyClosureRepository->findByCompanyMonth($companyId, $year, $month);

        if ($existing) {
            throw ValidationException::withMessages([
                'month' => 'Este mes ja foi fechado.',
            ]);
        }

        $closure = $this->monthlyClosureRepository->create([
            'company_id' => $companyId,
            'year' => $year,
            'month' => $month,
            'closed_by' => $actor,
            'closed_at' => now(),
        ]);

        $this->auditLogService->log(
            companyId: $companyId,
            actor: $actor,
            event: 'monthly_closure_created',
            entityType: 'monthly_closure',
            entityId: $closure->id,
            payload: [
                'year' => $year,
                'month' => $month,
            ]
        );

        return $closure;
    }
}
