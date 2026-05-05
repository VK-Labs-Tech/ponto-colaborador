<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompanyUserController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    public function edit(User $user) {
        return view('company-users.edit', compact('user'));
    }

    public function update(Request $request, User $user) {
        $companyId = (int) session('company_id');

        if ($user->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'max:100'],
            'role' => ['required', 'in:company_editor,company_admin,company_operator'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('company-users.index')->with('status', 'Usuario da empresa atualizado com sucesso.');
    }

    public function index()
    {
        $companyId = (int) session('company_id');

        return view('company-users.index', [
            'users' => User::query()
                ->where('company_id', $companyId)
                ->whereIn('role', ['company_admin', 'company_editor', 'company_operator'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = (int) session('company_id');

        if (! $this->subscriptionService->ensureCanCreateUser($companyId)) {
            throw ValidationException::withMessages([
                'name' => 'Limite de usuarios do plano atingido.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'max:100'],
            'role' => ['required', 'in:company_editor,company_admin,company_operator'],
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
