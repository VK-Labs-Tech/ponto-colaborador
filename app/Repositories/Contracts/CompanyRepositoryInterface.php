<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use Illuminate\Support\Collection;

interface CompanyRepositoryInterface
{
    public function findByLogin(string $login): ?Company;

    public function findById(int $id): ?Company;

    public function listAll(): Collection;

    public function create(array $data): Company;
}
