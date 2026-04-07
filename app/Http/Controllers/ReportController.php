<?php

namespace App\Http\Controllers;

use App\Exports\MirrorReportExport;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\ReportService;
use App\Services\SubscriptionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function index(Request $request)
    {
        $companyId = (int) session('company_id');
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $employeeId = $request->filled('employee_id') ? (int) $request->input('employee_id') : null;

        $report = $this->reportService->buildMirror($companyId, $from, $to, $employeeId);

        return view('reports.index', [
            'report' => $report,
            'employees' => $this->employeeRepository->listActiveByCompany($companyId),
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'employee_id' => $employeeId,
            ],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $companyId = (int) session('company_id');

        if (! $this->subscriptionService->consumeExportQuota($companyId)) {
            throw ValidationException::withMessages([
                'export' => 'Limite de exportacoes mensais do plano atingido.',
            ]);
        }

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $employeeId = $request->filled('employee_id') ? (int) $request->input('employee_id') : null;

        $report = $this->reportService->buildMirror($companyId, $from, $to, $employeeId);

        $pdf = Pdf::loadView('reports.pdf', [
            'rows' => $report['punch_rows'],
            'totals' => $report['totals'],
            'companyName' => session('company_name'),
            'from' => $from,
            'to' => $to,
        ]);

        return $pdf->download('espelho-ponto.pdf');
    }

    public function exportExcel(Request $request)
    {
        $companyId = (int) session('company_id');

        if (! $this->subscriptionService->consumeExportQuota($companyId)) {
            throw ValidationException::withMessages([
                'export' => 'Limite de exportacoes mensais do plano atingido.',
            ]);
        }

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $employeeId = $request->filled('employee_id') ? (int) $request->input('employee_id') : null;

        $report = $this->reportService->buildMirror($companyId, $from, $to, $employeeId);

        return Excel::download(new MirrorReportExport($report['punch_rows']), 'espelho-ponto.xlsx');
    }
}
