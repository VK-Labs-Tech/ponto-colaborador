<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\CompanyManagementService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyManagementController extends Controller
{
    public function __construct(
        private readonly CompanyManagementService $companyManagementService,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function index()
    {
        return view('admin.companies.index', [
            'companies' => $this->companyManagementService->listCompanies(),
            'plans' => Plan::query()->where('is_active', true)->orderBy('name')->get(),
            'metrics' => $this->subscriptionService->metrics(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'company_login' => ['required', 'string', 'min:4', 'max:60', Rule::unique('companies', 'login')],
            'company_password' => ['required', 'string', 'min:6', 'max:100'],
            'admin_name' => ['required', 'string', 'min:3', 'max:150'],
            'admin_email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'admin_password' => ['required', 'string', 'min:6', 'max:100'],
            'plan_code' => ['required', Rule::exists('plans', 'code')],
        ]);

        $this->companyManagementService->createCompanyWithAdmin($validated);

        return redirect()->route('admin.companies.index')->with('status', 'Empresa e acesso EMPRESA cadastrados com sucesso.');
    }

    public function show(int $companyId)
    {
        return view('admin.companies.show', $this->companyManagementService->detailByCompanyId($companyId));
    }

    public function updateSubscription(Request $request, int $companyId)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,overdue,cancel,change_plan,extend_trial'],
            'plan_code' => ['nullable', Rule::exists('plans', 'code')],
            'months' => ['nullable', 'integer', 'min:1', 'max:24'],
            'days' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        switch ($validated['action']) {
            case 'activate':
                $this->subscriptionService->activate($companyId, (int) ($validated['months'] ?? 1), (string) ($request->user()?->email ?? 'saas_admin'));
                $message = 'Assinatura ativada.';
                break;
            case 'overdue':
                $this->subscriptionService->markOverdue($companyId, (string) ($request->user()?->email ?? 'saas_admin'));
                $message = 'Assinatura marcada como inadimplente.';
                break;
            case 'cancel':
                $this->subscriptionService->cancel($companyId, (string) ($request->user()?->email ?? 'saas_admin'));
                $message = 'Assinatura cancelada.';
                break;
            case 'change_plan':
                $this->subscriptionService->changePlan($companyId, (string) $validated['plan_code'], (string) ($request->user()?->email ?? 'saas_admin'));
                $message = 'Plano alterado com sucesso.';
                break;
            case 'extend_trial':
                $this->subscriptionService->extendTrial($companyId, (int) ($validated['days'] ?? 7), (string) ($request->user()?->email ?? 'saas_admin'));
                $message = 'Trial estendido.';
                break;
            default:
                $message = 'Acao nao reconhecida.';
                break;
        }

        return redirect()->route('admin.companies.index')->with('status', $message);
    }
}
