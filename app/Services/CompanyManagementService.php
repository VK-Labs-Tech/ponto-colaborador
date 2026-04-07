<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyManagementService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function listCompanies(): Collection
    {
        return $this->companyRepository->listAll();
    }

    public function createCompanyWithAdmin(array $data): void
    {
        DB::transaction(function () use ($data): void {
            $company = $this->companyRepository->create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
                'login' => $data['company_login'],
                'password' => $data['company_password'],
                'is_active' => true,
            ]);

            User::query()->create([
                'company_id' => $company->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'role' => 'company_admin',
                'password' => $data['admin_password'],
            ]);

            $this->subscriptionService->createTrialForCompany(
                companyId: $company->id,
                planCode: (string) $data['plan_code']
            );
        });
    }

    public function detailByCompanyId(int $companyId): array
    {
        $company = Company::query()
            ->with([
                'subscription.plan',
                'subscription.events' => fn ($query) => $query->latest()->limit(30),
                'subscription.invoices' => fn ($query) => $query->with('plan')->latest('reference_month')->limit(12),
            ])
            ->findOrFail($companyId);

        $employeeCount = $company->employees()->count();
        $userCount = User::query()
            ->where('company_id', $company->id)
            ->whereIn('role', ['company_admin', 'company_editor', 'company_operator'])
            ->count();

        $subscription = $company->subscription;
        $plan = $subscription?->plan;

        return [
            'company' => $company,
            'subscription' => $subscription,
            'plan' => $plan,
            'events' => $subscription?->events ?? collect(),
            'invoices' => $subscription?->invoices ?? collect(),
            'limits' => [
                'employees' => [
                    'used' => $employeeCount,
                    'limit' => $plan?->employee_limit,
                ],
                'users' => [
                    'used' => $userCount,
                    'limit' => $plan?->user_limit,
                ],
                'exports' => [
                    'used' => (int) ($subscription?->exports_used_in_period ?? 0),
                    'limit' => $plan?->monthly_export_limit,
                ],
            ],
        ];
    }
}
