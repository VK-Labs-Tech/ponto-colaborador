<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CompanyManagementService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyManagementController extends Controller
{
    public function __construct(private readonly CompanyManagementService $companyManagementService)
    {
    }

    public function index()
    {
        return view('admin.companies.index', [
            'companies' => $this->companyManagementService->listCompanies(),
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
        ]);

        $this->companyManagementService->createCompanyWithAdmin($validated);

        return redirect()->route('admin.companies.index')->with('status', 'Empresa e acesso EMPRESA cadastrados com sucesso.');
    }
}
