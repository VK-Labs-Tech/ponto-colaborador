<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTimePunchRequest;
use App\Models\Employee;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use App\Services\TimePunchAdjustmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class TimePunchAdjustmentController extends Controller
{
    public function __construct(
        private readonly TimePunchRepositoryInterface $timePunchRepository,
        private readonly TimePunchAdjustmentService $timePunchAdjustmentService
    ) {
    }

    public function edit(Request $request)
    {
        $companyId = (int) session('company_id');
        $employeeId = (int) $request->query('employee_id');
        $date = (string) $request->query('date');

        $employee = Employee::query()
            ->where('company_id', $companyId)
            ->findOrFail($employeeId);

        $day = Carbon::parse($date);
        $punches = $this->timePunchRepository->listByEmployeePeriod($employee->id, $day->copy()->startOfDay(), $day->copy()->endOfDay());

        $inPunches = $punches->where('action', 'in')->values();
        $outPunches = $punches->where('action', 'out')->values();

        return view('time-punch-adjustments.edit', [
            'employee' => $employee,
            'date' => $day->toDateString(),
            'values' => [
                'entry_1' => $inPunches->get(0)?->punched_at?->format('H:i'),
                'exit_1' => $outPunches->get(0)?->punched_at?->format('H:i'),
                'entry_2' => $inPunches->get(1)?->punched_at?->format('H:i'),
                'exit_2' => $outPunches->get(1)?->punched_at?->format('H:i'),
            ],
        ]);
    }

    public function update(UpdateTimePunchRequest $request)
    {
        $companyId = (int) session('company_id');

        $validated = $request->validated();

        $employee = Employee::query()
            ->where('company_id', $companyId)
            ->findOrFail($validated['employee_id']);

        $this->timePunchAdjustmentService->replaceDayPunches(
            companyId: $companyId,
            employee: $employee,
            date: (string) $validated['date'],
            times: [
                'entry_1' => $validated['entry_1'] ?? null,
                'exit_1' => $validated['exit_1'] ?? null,
                'entry_2' => $validated['entry_2'] ?? null,
                'exit_2' => $validated['exit_2'] ?? null,
            ],
            reason: (string) $validated['reason'],
            actor: $request->user()?->id ?? 'system'
        );

        return redirect()->route('reports.index', [
            'from' => $validated['date'],
            'to' => $validated['date'],
            'employee_id' => $employee->id,
        ])->with('status', 'Batidas ajustadas com sucesso.');
    }
}
