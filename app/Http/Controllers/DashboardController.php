<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $companyId = (int) session('company_id');
        $metrics = $this->dashboardService->build($companyId);

        return view('dashboard.index', [
            'metrics' => $metrics,
        ]);
    }
}
