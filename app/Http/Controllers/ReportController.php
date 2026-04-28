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

        $snapshot = $this->reportService->createSnapshot($companyId, $from, $to, $employeeId);

        return view('reports.index', [
            'report' => [
                'rows' => $snapshot->rows,
                'punch_rows' => $snapshot->punch_rows,
                'totals' => $snapshot->totals,
            ],
            'employees' => $this->employeeRepository->listActiveByCompany($companyId),
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'employee_id' => $employeeId,
            ],
            'snapshot' => $snapshot,
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

        $snapshotId = $request->filled('snapshot_id') ? (int) $request->input('snapshot_id') : null;
        $snapshot = $snapshotId
            ? $this->reportService->getSnapshot($companyId, $snapshotId)
            : null;
        $snapshot ??= $this->reportService->createSnapshot($companyId, $from, $to, $employeeId);

        $pdf = Pdf::loadView('reports.pdf', [
            'rows' => $snapshot->punch_rows,
            'totals' => $snapshot->totals,
            'companyName' => session('company_name'),
            'from' => $from,
            'to' => $to,
            'snapshot' => $snapshot,
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

        $snapshotId = $request->filled('snapshot_id') ? (int) $request->input('snapshot_id') : null;
        $snapshot = $snapshotId
            ? $this->reportService->getSnapshot($companyId, $snapshotId)
            : null;
        $snapshot ??= $this->reportService->createSnapshot($companyId, $from, $to, $employeeId);

        return Excel::download(new MirrorReportExport($snapshot->punch_rows, $snapshot), 'espelho-ponto.xlsx');
    }

    public function acknowledgeMirror(Request $request)
    {
        $companyId = (int) session('company_id');
        $snapshot = $this->reportService->getSnapshot($companyId, (int) $request->input('snapshot_id'));

        if (! $snapshot) {
            return back()->withErrors(['snapshot_id' => 'Snapshot de espelho não encontrado.']);
        }

        $snapshot->update([
            'signed_by' => $request->user()?->id,
            'signed_at' => now(),
        ]);

        return back()->with('status', 'Ciência do espelho registrada com sucesso.');
    }
}
