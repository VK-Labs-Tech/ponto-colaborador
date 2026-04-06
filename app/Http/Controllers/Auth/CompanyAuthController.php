<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CompanyAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.company-login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'min:3', 'max:190'],
            'password' => ['required', 'string'],
        ]);

        $login = trim((string) $validated['login']);

        $user = User::query()
            ->where('email', $login)
            ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($login)])
            ->first();

        if (! $user || ! Hash::check((string) $validated['password'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Credenciais invalidas.',
            ]);
        }

        $request->session()->regenerate();
        Auth::login($user);

        if ($user->role === 'saas_admin') {
            return redirect()->route('admin.companies.index');
        }

        if (! in_array($user->role, ['company_admin', 'company_editor'], true) || ! $user->company_id) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Usuario sem perfil de acesso valido.',
            ]);
        }

        $request->session()->put('company_id', $user->company_id);
        $request->session()->put('company_name', $user->company?->name ?? 'Empresa');

        return redirect()->route('dashboard.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['company_id', 'company_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('company.login');
    }
}
