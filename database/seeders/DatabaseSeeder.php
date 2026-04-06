<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Victor Gabriel',
            'email' => 'owner@ponto.com',
            'role' => 'saas_admin',
            'password' => Hash::make('123456'),
        ]);

        $company = Company::query()->create([
            'name' => 'Empresa Demo',
            'slug' => 'empresa-demo',
            'login' => 'empresa.demo',
            'password' => Hash::make('123456'),
        ]);

        User::query()->create([
            'company_id' => $company->id,
            'name' => 'Admin Empresa Demo',
            'email' => 'admin@empresa-demo.com',
            'role' => 'company_admin',
            'password' => Hash::make('123456'),
        ]);

        User::query()->create([
            'company_id' => $company->id,
            'name' => 'Editor Empresa Demo',
            'email' => 'editor@empresa-demo.com',
            'role' => 'company_editor',
            'password' => Hash::make('123456'),
        ]);

        Employee::query()->create([
            'company_id' => $company->id,
            'name' => 'Ana Souza',
            'registration' => 'COL001',
            'pin' => '1111',
            'shift_start' => '08:00:00',
            'shift_end' => '17:00:00',
        ]);

        Employee::query()->create([
            'company_id' => $company->id,
            'name' => 'Carlos Lima',
            'registration' => 'COL002',
            'pin' => '2222',
            'shift_start' => '08:00:00',
            'shift_end' => '17:00:00',
        ]);
    }
}
