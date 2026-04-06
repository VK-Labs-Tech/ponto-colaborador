<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyUserController extends Controller
{
    public function index()
    {
        $companyId = (int) session('company_id');

        return view('company-users.index', [
            'users' => User::query()
                ->where('company_id', $companyId)
                ->whereIn('role', ['company_admin', 'company_editor'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = (int) session('company_id');

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'max:100'],
            'role' => ['required', 'in:company_editor,company_admin'],
        ]);

        User::query()->create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('company-users.index')->with('status', 'Usuario da empresa cadastrado com sucesso.');
    }
}
