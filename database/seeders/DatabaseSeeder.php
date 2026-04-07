<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Employee;
use App\Models\Plan;
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
        $starterPlan = Plan::query()->create([
            'name' => 'Starter',
            'code' => 'starter',
            'price_cents' => 9900,
            'employee_limit' => 25,
            'user_limit' => 3,
            'monthly_export_limit' => 25,
        ]);

        Plan::query()->create([
            'name' => 'Professional',
            'code' => 'professional',
            'price_cents' => 19900,
            'employee_limit' => 120,
            'user_limit' => 10,
            'monthly_export_limit' => 150,
        ]);

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

        User::query()->create([
            'company_id' => $company->id,
            'name' => 'Operador Empresa Demo',
            'email' => 'operador@empresa-demo.com',
            'role' => 'company_operator',
            'password' => Hash::make('123456'),
        ]);

        CompanySubscription::query()->create([
            'company_id' => $company->id,
            'plan_id' => $starterPlan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7)->endOfDay(),
            'exports_period_start' => now()->startOfMonth()->toDateString(),
            'exports_used_in_period' => 0,
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
