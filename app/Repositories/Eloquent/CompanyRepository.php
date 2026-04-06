<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function findByLogin(string $login): ?Company
    {
        return Company::query()
            ->where('login', $login)
            ->where('is_active', true)
            ->first();
    }

    public function findById(int $id): ?Company
    {
        return Company::query()->find($id);
    }

    public function listAll(): Collection
    {
        return Company::query()->orderBy('name')->get();
    }

    public function create(array $data): Company
    {
        return Company::query()->create($data);
    }
}
