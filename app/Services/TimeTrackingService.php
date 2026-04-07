<?php

namespace App\Services;

use App\Models\TimePunch;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use App\Services\AuditLogService;
use App\Services\MonthlyClosureService;
use App\Support\PinHasher;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TimeTrackingService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly TimePunchRepositoryInterface $timePunchRepository,
        private readonly MonthlyClosureService $monthlyClosureService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function registerPunch(int $companyId, int $employeeId, string $pin): TimePunch
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (! $employee || $employee->company_id !== $companyId || ! $employee->is_active) {
            throw ValidationException::withMessages([
                'employee_id' => 'Colaborador invalido para esta empresa.',
            ]);
        }

        if (! PinHasher::verify($pin, (string) $employee->pin)) {
            throw ValidationException::withMessages([
                'pin' => 'PIN invalido.',
            ]);
        }

        if (PinHasher::needsUpgrade((string) $employee->pin)) {
            $this->employeeRepository->updatePin($employee->id, PinHasher::hash($pin));
        }

        $now = Carbon::now();
        if ($this->monthlyClosureService->isClosed($companyId, $now)) {
            throw ValidationException::withMessages([
                'date' => 'Nao e permitido lancar ponto em mes fechado.',
            ]);
        }

        $todayPunches = $this->timePunchRepository->listByEmployeePeriod(
            employeeId: $employee->id,
            from: $now->copy()->startOfDay(),
            to: $now->copy()->endOfDay()
        );

        $lastPunch = $todayPunches->last();
        $action = $lastPunch?->action === 'in' ? 'out' : 'in';

        $punch = $this->timePunchRepository->create([
            'company_id' => $companyId,
            'employee_id' => $employee->id,
            'action' => $action,
            'punched_at' => $now,
            'origin' => 'kiosk',
        ]);

        $this->auditLogService->log(
            companyId: $companyId,
            actor: session('company_name', 'empresa'),
            event: 'time_punch_created',
            entityType: 'time_punch',
            entityId: $punch->id,
            payload: [
                'employee_id' => $employee->id,
                'action' => $action,
                'punched_at' => $now->toDateTimeString(),
            ]
        );

        return $punch;
    }
}
