<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyClosureRequest;
use App\Services\MonthlyClosureService;

class MonthlyClosureController extends Controller
{
    public function __construct(private readonly MonthlyClosureService $monthlyClosureService)
    {
    }

    public function store(MonthlyClosureRequest $request)
    {
        $validated = $request->validated();

        $companyId = (int) session('company_id');

        $this->monthlyClosureService->closeMonth(
            companyId: $companyId,
            year: (int) $validated['year'],
            month: (int) $validated['month'],
            actor: (string) session('company_name', 'empresa')
        );

        return redirect()->route('reports.index')->with('status', 'Mes fechado com sucesso.');
    }
}
