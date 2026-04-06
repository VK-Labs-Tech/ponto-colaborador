<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CompanyAuthService
{
    public function __construct(private readonly CompanyRepositoryInterface $companyRepository)
    {
    }

    public function authenticate(string $login, string $password): ?Company
    {
        $company = $this->companyRepository->findByLogin($login);

        if (! $company) {
            return null;
        }

        if (! Hash::check($password, $company->password)) {
            return null;
        }

        return $company;
    }
}
