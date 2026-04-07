<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\TimeTrackingService;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly TimeTrackingService $timeTrackingService
    ) {
    }

    public function index()
    {
        $companyId = (int) session('company_id');

        return view('kiosk.index', [
            'employees' => $this->employeeRepository->listActiveByCompany($companyId),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer'],
            'pin' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        $companyId = (int) session('company_id');

        $punch = $this->timeTrackingService->registerPunch(
            companyId: $companyId,
            employeeId: (int) $validated['employee_id'],
            pin: (string) $validated['pin']
        );

        $actionLabel = $punch->action === 'in' ? 'Entrada' : 'Saida';

        return redirect()->route('kiosk.index')->with('status', $actionLabel . ' registrada com sucesso.');
    }
}
