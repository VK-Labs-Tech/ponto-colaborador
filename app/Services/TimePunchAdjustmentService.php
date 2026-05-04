<?php

namespace App\Services;

use App\Models\AttendanceAdjustment;
use App\Models\Employee;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        string $actor,
        ?int $actorId = null,
        ?string $actorRole = null
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
            if ($value === null || $value === '') {
                continue;
            }

            $ordered[] = [
                'action' => $item['action'],
                'punched_at' => Carbon::parse($date.' '.$value),
            ];
        }

        if (count($ordered) < 2) {
            throw ValidationException::withMessages([
                'entry_1' => 'Informe pelo menos uma entrada e uma saída para o ajuste.',
            ]);
        }

        for ($i = 1; $i < count($ordered); $i++) {
            if (! $ordered[$i]['punched_at']->greaterThan($ordered[$i - 1]['punched_at'])) {
                throw ValidationException::withMessages([
                    'entry_1' => 'As batidas devem estar em ordem cronologica.',
                ]);
            }
        }

        if ($ordered[0]['action'] !== 'in' || $ordered[count($ordered) - 1]['action'] !== 'out') {
            throw ValidationException::withMessages([
                'entry_1' => 'A sequencia deve iniciar com entrada e finalizar com saída.',
            ]);
        }

        $batchId = (string) Str::uuid();

        DB::transaction(function () use ($companyId, $employee, $baseDate, $ordered, $reason, $actor, $actorId, $actorRole, $batchId): void {
            $existingPunches = $this->timePunchRepository->listByEmployeePeriod(
                employeeId: $employee->id,
                from: $baseDate->copy()->startOfDay(),
                to: $baseDate->copy()->endOfDay()
            );

            foreach ($ordered as $item) {
                $this->timePunchRepository->create([
                    'company_id' => $companyId,
                    'employee_id' => $employee->id,
                    'action' => $item['action'],
                    'punched_at' => $item['punched_at'],
                    'origin' => 'manual_adjustment',
                    'note' => $reason,
                    'adjustment_batch' => $batchId,
                ]);
            }

            AttendanceAdjustment::query()->create([
                'company_id' => $companyId,
                'employee_id' => $employee->id,
                'reference_date' => $baseDate->toDateString(),
                'worked_minutes' => 0,
                'reason' => $reason,
                'created_by' => $actor,
                'before_punches' => $existingPunches->map(fn ($punch) => [
                    'id' => $punch->id,
                    'action' => $punch->action,
                    'punched_at' => $punch->punched_at->toDateTimeString(),
                    'origin' => $punch->origin,
                    'note' => $punch->note,
                ])->values()->all(),
                'after_punches' => array_map(fn (array $item) => [
                    'action' => $item['action'],
                    'punched_at' => $item['punched_at']->toDateTimeString(),
                    'origin' => 'manual_adjustment',
                    'note' => $reason,
                ], $ordered),
                'actor_id' => $actorId,
                'actor_role' => $actorRole,
                'status' => 'approved',
                'approved_by' => $actorId,
                'approved_at' => Carbon::now(),
                'approval_reason' => 'Ajuste aplicado com justificativa obrigatória.',
                'adjustment_batch' => $batchId,
            ]);

            $this->auditLogService->log(
                companyId: $companyId,
                actor: $actor,
                event: 'time_punch_adjusted',
                entityType: 'employee',
                entityId: $employee->id,
                payload: [
                    'date' => $baseDate->toDateString(),
                    'adjustment_batch' => $batchId,
                    'punches_after' => array_map(fn (array $item) => [
                        'action' => $item['action'],
                        'punched_at' => $item['punched_at']->toDateTimeString(),
                    ], $ordered),
                ],
                before: $existingPunches->map(fn ($punch) => [
                    'id' => $punch->id,
                    'action' => $punch->action,
                    'punched_at' => $punch->punched_at->toDateTimeString(),
                    'origin' => $punch->origin,
                ])->values()->all(),
                after: array_map(fn (array $item) => [
                    'action' => $item['action'],
                    'punched_at' => $item['punched_at']->toDateTimeString(),
                    'origin' => 'manual_adjustment',
                ], $ordered),
                reason: $reason,
                actorId: $actorId,
                actorType: 'user',
                actorRole: $actorRole
            );
        });
    }
}
