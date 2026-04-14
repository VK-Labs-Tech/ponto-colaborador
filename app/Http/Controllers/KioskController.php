<?php

namespace App\Http\Controllers;

use App\Http\Requests\KioskRequest;
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

    public function store(KioskRequest $request)
    {
        $validated = $request->validated();

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
