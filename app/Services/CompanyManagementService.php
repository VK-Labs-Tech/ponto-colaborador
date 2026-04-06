<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyManagementService
{
    public function __construct(private readonly CompanyRepositoryInterface $companyRepository)
    {
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
        });
    }
}
