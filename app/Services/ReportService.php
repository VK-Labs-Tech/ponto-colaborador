<?php

namespace App\Services;

use App\Models\MirrorSnapshot;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class ReportService
{
    public function __construct(private readonly TimePunchRepositoryInterface $timePunchRepository)
    {
    }

    public function buildMirror(int $companyId, CarbonInterface $from, CarbonInterface $to, ?int $employeeId = null): array
    {
        $punches = $this->timePunchRepository->listByCompanyPeriod($companyId, $from, $to);

        if ($employeeId) {
            $punches = $punches->where('employee_id', $employeeId)->values();
        }

        $punches = $this->resolveEffectivePunches($punches);

        $grouped = $punches->groupBy(function ($punch) {
            return $punch->employee_id.'|'.$punch->punched_at->toDateString();
        });

        $punchRows = $punches
            ->sortBy('punched_at')
            ->values()
            ->map(function ($punch) {
                $isLate = false;

                if ($punch->action === 'in' && $punch->employee?->shift_start) {
                    $shiftStart = Carbon::parse(
                        $punch->punched_at->toDateString().' '.$punch->employee->shift_start->format('H:i:s')
                    );
                    $isLate = $punch->punched_at->greaterThan($shiftStart);
                }

                return [
                    'employee_id' => $punch->employee_id,
                    'employee_name' => $punch->employee?->name ?? 'N/A',
                    'date' => $punch->punched_at->toDateString(),
                    'time' => $punch->punched_at->format('H:i'),
                    'action' => $punch->action,
                    'action_label' => $punch->action === 'in' ? 'Entrada' : 'Saida',
                    'is_late' => $isLate,
                    'origin' => $punch->origin,
                    'is_adjustment' => $punch->origin === 'manual_adjustment',
                    'note' => $punch->note,
                ];
            })
            ->all();

        $rows = [];
        $totalWorked = 0;
        $totalOvertime = 0;

        foreach ($grouped as $records) {
            $records = $records->sortBy('punched_at')->values();
            $employee = $records->first()->employee;
            $date = $records->first()->punched_at->toDateString();

            $inPunches = $records->where('action', 'in')->values();
            $outPunches = $records->where('action', 'out')->values();
            $firstIn = $inPunches->get(0);
            $lunchOut = $outPunches->get(0);
            $lunchIn = $inPunches->get(1);
            $lastOut = $outPunches->get(1) ?? $outPunches->last();

            $workedMinutes = $this->calculateWorkedMinutes($records->all());
            $overtimeMinutes = max($workedMinutes - (8 * 60), 0);
            $isLate = false;

            if ($firstIn && $employee) {
                $shiftStart = Carbon::parse($date.' '.$employee->shift_start->format('H:i:s'));
                $isLate = $firstIn->punched_at->greaterThan($shiftStart);
            }

            $rows[] = [
                'employee_id' => $records->first()->employee_id,
                'employee_name' => $employee?->name ?? 'N/A',
                'date' => $date,
                'entry_1' => $firstIn?->punched_at?->format('H:i') ?? '-',
                'exit_1' => $lunchOut?->punched_at?->format('H:i') ?? '-',
                'entry_2' => $lunchIn?->punched_at?->format('H:i') ?? '-',
                'exit_2' => $lastOut?->punched_at?->format('H:i') ?? '-',
                'worked_minutes' => $workedMinutes,
                'worked_hours' => $this->minutesToHuman($workedMinutes),
                'overtime_minutes' => $overtimeMinutes,
                'overtime_hours' => $this->minutesToHuman($overtimeMinutes),
                'is_late' => $isLate,
            ];

            $totalWorked += $workedMinutes;
            $totalOvertime += $overtimeMinutes;
        }

        usort($rows, fn (array $a, array $b) => strcmp($a['date'].$a['employee_name'], $b['date'].$b['employee_name']));

        return [
            'rows' => $rows,
            'punch_rows' => $punchRows,
            'totals' => [
                'worked_minutes' => $totalWorked,
                'worked_hours' => $this->minutesToHuman($totalWorked),
                'overtime_minutes' => $totalOvertime,
                'overtime_hours' => $this->minutesToHuman($totalOvertime),
            ],
        ];
    }

    public function createSnapshot(int $companyId, CarbonInterface $from, CarbonInterface $to, ?int $employeeId = null): MirrorSnapshot
    {
        $report = $this->buildMirror($companyId, $from, $to, $employeeId);
        $payload = [
            'rows' => $report['rows'],
            'punch_rows' => $report['punch_rows'],
            'totals' => $report['totals'],
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'employee_id' => $employeeId,
        ];

        $hash = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $latest = MirrorSnapshot::query()
            ->where('company_id', $companyId)
            ->whereDate('period_from', $from->toDateString())
            ->whereDate('period_to', $to->toDateString())
            ->where('employee_id', $employeeId)
            ->latest('id')
            ->first();

        $version = ($latest?->content_hash === $hash) ? $latest->version : (($latest?->version ?? 0) + 1);

        return MirrorSnapshot::query()->create([
            'company_id' => $companyId,
            'period_from' => $from->toDateString(),
            'period_to' => $to->toDateString(),
            'employee_id' => $employeeId,
            'rows' => $report['rows'],
            'punch_rows' => $report['punch_rows'],
            'totals' => $report['totals'],
            'content_hash' => $hash,
            'version' => $version,
        ]);
    }

    public function getSnapshot(int $companyId, int $snapshotId): ?MirrorSnapshot
    {
        return MirrorSnapshot::query()
            ->where('company_id', $companyId)
            ->find($snapshotId);
    }

    public function minutesToHuman(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remaining);
    }

    private function calculateWorkedMinutes(array $records): int
    {
        $minutes = 0;
        $openIn = null;

        foreach ($records as $record) {
            if ($record->action === 'in') {
                $openIn = $record;
                continue;
            }

            if ($record->action === 'out' && $openIn) {
                $diff = $openIn->punched_at->diffInMinutes($record->punched_at, false);
                if ($diff > 0) {
                    $minutes += $diff;
                }
                $openIn = null;
            }
        }

        return $minutes;
    }

    private function resolveEffectivePunches($punches)
    {
        return $punches
            ->groupBy(fn ($punch) => $punch->employee_id.'|'.$punch->punched_at->toDateString())
            ->flatMap(function ($dayPunches) {
                $adjustmentPunches = $dayPunches
                    ->where('origin', 'manual_adjustment')
                    ->sortByDesc('created_at')
                    ->values();

                if ($adjustmentPunches->isEmpty()) {
                    return $dayPunches->sortBy('punched_at')->values();
                }

                $latestBatch = $adjustmentPunches->first()->adjustment_batch;
                if ($latestBatch) {
                    return $adjustmentPunches
                        ->where('adjustment_batch', $latestBatch)
                        ->sortBy('punched_at')
                        ->values();
                }

                return $adjustmentPunches->sortBy('punched_at')->values();
            })
            ->sortBy('punched_at')
            ->values();
    }
}
