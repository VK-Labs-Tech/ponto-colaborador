<?php

namespace App\Http\Controllers;

use App\Services\MonthlyClosureService;
use Illuminate\Http\Request;

class MonthlyClosureController extends Controller
{
    public function __construct(private readonly MonthlyClosureService $monthlyClosureService)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

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
