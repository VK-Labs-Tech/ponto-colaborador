<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TimePunchAdjustmentService
{
    public function __construct(
        private readonly TimePunchRepositoryInterface $timePunchRepository,
        private readonly MonthlyClosureService $monthlyClosureService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function replaceDayPunches(
        int $companyId,
        Employee $employee,
        string $date,
        array $times,
        string $reason,
        string $actor
    ): void {
        $baseDate = Carbon::parse($date);

        if ($this->monthlyClosureService->isClosed($companyId, $baseDate)) {
            throw ValidationException::withMessages([
                'date' => 'Nao e permitido ajustar ponto em mes fechado.',
            ]);
        }

        $sequence = [
            ['key' => 'entry_1', 'action' => 'in'],
            ['key' => 'exit_1', 'action' => 'out'],
            ['key' => 'entry_2', 'action' => 'in'],
            ['key' => 'exit_2', 'action' => 'out'],
        ];

        $ordered = [];
        foreach ($sequence as $item) {
            $value = $times[$item['key']] ?? null;
            if (! $value) {
                continue;
            }

            $ordered[] = [
                'action' => $item['action'],
                'punched_at' => Carbon::parse($date.' '.$value),
            ];
        }

        for ($i = 1; $i < count($ordered); $i++) {
            if (! $ordered[$i]['punched_at']->greaterThan($ordered[$i - 1]['punched_at'])) {
                throw ValidationException::withMessages([
                    'entry_1' => 'As batidas devem estar em ordem cronologica.',
                ]);
            }
        }

        DB::transaction(function () use ($companyId, $employee, $baseDate, $ordered, $reason, $actor): void {
            $this->timePunchRepository->deleteByEmployeeDate($employee->id, $baseDate);

            foreach ($ordered as $item) {
                $this->timePunchRepository->create([
                    'company_id' => $companyId,
                    'employee_id' => $employee->id,
                    'action' => $item['action'],
                    'punched_at' => $item['punched_at'],
                    'origin' => 'manual_adjustment',
                    'note' => $reason,
                ]);
            }

            $this->auditLogService->log(
                companyId: $companyId,
                actor: $actor,
                event: 'time_punch_adjusted',
                entityType: 'employee',
                entityId: $employee->id,
                payload: [
                    'date' => $baseDate->toDateString(),
                    'reason' => $reason,
                    'punches' => array_map(fn (array $item) => [
                        'action' => $item['action'],
                        'punched_at' => $item['punched_at']->toDateTimeString(),
                    ], $ordered),
                ]
            );
        });
    }
}
