<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use App\Services\ReportService;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly TimePunchRepositoryInterface $timePunchRepository,
        private readonly ReportService $reportService
    ) {
    }

    public function build(int $companyId): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::today()->startOfMonth();
        $monthEnd = Carbon::today()->endOfDay();
        $employees = $this->employeeRepository->listActiveByCompany($companyId);

        $mirror = $this->reportService->buildMirror($companyId, $monthStart, $monthEnd);

        $workedMinutes = array_sum(array_column($mirror['rows'], 'worked_minutes'));
        $lateCount = 0;
        $absenceCount = 0;
        $missingPunchToday = [];
        $lateToday = [];

        foreach ($employees as $employee) {
            $firstPunchToday = $this->timePunchRepository->firstPunchOfDay($employee->id, $today);
            if (! $firstPunchToday) {
                $missingPunchToday[] = $employee->name;
            }

            if ($firstPunchToday && $firstPunchToday->action === 'in') {
                $shiftStart = Carbon::parse($today->toDateString().' '.$employee->shift_start->format('H:i:s'));
                if ($firstPunchToday->punched_at->greaterThan($shiftStart)) {
                    $lateToday[] = $employee->name;
                }
            }

            $cursor = $monthStart->copy();
            while ($cursor->lte($today)) {
                if ($cursor->isWeekday()) {
                    $hasPunch = $this->timePunchRepository->hasPunchOnDate($employee->id, $cursor);
                    if (! $hasPunch) {
                        $absenceCount++;
                    }
                }

                $cursor->addDay();
            }
        }

        foreach ($mirror['rows'] as $row) {
            if ($row['is_late']) {
                $lateCount++;
            }
        }

        return [
            'worked_hours' => $this->reportService->minutesToHuman($workedMinutes),
            'late_count' => $lateCount,
            'absence_count' => $absenceCount,
            'alerts_missing_punch' => $missingPunchToday,
            'alerts_late' => $lateToday,
        ];
    }
}
