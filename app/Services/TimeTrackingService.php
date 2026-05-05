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

    public function registerPunch(int $companyId, int $employeeId, string $pin, array $context = []): TimePunch
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
        if (PinHasher::needsUpgrade((string) $employee->pin)) $this->employeeRepository->updatePin($employee->id, PinHasher::hash($pin));
    
        $timezone = 'America/Cuiaba';
        $now = Carbon::now($timezone);
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

        if ($lastPunch && $lastPunch->punched_at->diffInSeconds($now) < 60) {
            throw ValidationException::withMessages([
                'employee_id' => 'Nova marcacao bloqueada por janela de segurança de 60 segundos.',
            ]);
        }

        $todayPunches = $this->timePunchRepository->listByEmployeePeriod(
            employeeId: $employee->id,
            from: $now->copy()->startOfDay()->utc(),  
            to: $now->copy()->endOfDay()->utc()       
        );

        $punch = $this->timePunchRepository->create([
            'company_id' => $companyId,
            'employee_id' => $employee->id,
            'action' => $action,
            'punched_at' => $now,
            'origin' => 'kiosk',
            'ip_address' => $context['ip_address'] ?? null,
            'latitude' => $context['latitude'] ?? null,
            'longitude' => $context['longitude'] ?? null,
            'device_fingerprint' => $context['device_fingerprint'] ?? null,
        ]);

            $this->auditLogService->log(
                companyId: $companyId,
                actor: 'kiosk',
                event: 'time_punch_created',
                entityType: 'time_punch',
                entityId: $punch->id,
                payload: [
                    'employee_id' => $employee->id,
                    'action'      => $action,
                    'punched_at'  => $now->toDateTimeString(),
                    'ip_address'  => $context['ip_address'] ?? null,
                    'latitude'    => $context['latitude'] ?? null,
                    'longitude'   => $context['longitude'] ?? null,
                ],
                actorId:   $employee->id,
                actorType: 'employee',
                actorRole: null
            );

        return $punch;
    }
}
