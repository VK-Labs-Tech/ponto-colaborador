<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CompanyAuthController extends Controller
{
    private const TWO_FACTOR_MINUTES = 10;

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToHomeByRole(Auth::user()->role);
        }

        return view('auth.company-login');
    }

    public function showTwoFactor(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectToHomeByRole(Auth::user()->role);
        }

        if (! $request->session()->has('two_factor_user_id')) {
            return redirect()->route('company.login');
        }

        return view('auth.two-factor');
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
            Log::warning('security.login_failed', [
                'login' => $login,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'login' => 'Credenciais invalidas.',
            ]);
        }

        if ($user->role === 'saas_admin') {
            $request->session()->regenerate();
            $request->session()->put('two_factor_user_id', $user->id);

            $this->issueTwoFactorCode($user, $request);

            return redirect()->route('company.2fa.form')
                ->with('status', 'Codigo de verificacao enviado. Confirme para continuar.');
        }

        $request->session()->regenerate();
        Auth::login($user);

        if (! in_array($user->role, ['company_admin', 'company_editor', 'company_operator'], true) || ! $user->company_id) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Usuario sem perfil de acesso valido.',
            ]);
        }

        $request->session()->put('company_id', $user->company_id);
        $request->session()->put('company_name', $user->company?->name ?? 'Empresa');
        $request->session()->flash('show_intro_videos', true);

        if ($user->role === 'company_operator') {
            return redirect()->route('kiosk.index');
        }

        return redirect()->route('dashboard.index');
    }

    public function verifyTwoFactor(Request $request)
    {
        $pendingUserId = (int) $request->session()->get('two_factor_user_id', 0);
        if (! $pendingUserId) {
            return redirect()->route('company.login');
        }

        $validated = $request->validate([
            'code' => ['required', 'regex:/^\d{6}$/'],
        ]);

        $user = User::query()->find($pendingUserId);
        if (! $user || $user->role !== 'saas_admin') {
            $request->session()->forget('two_factor_user_id');

            return redirect()->route('company.login')->withErrors([
                'login' => 'Fluxo de verificacao invalido. Faca login novamente.',
            ]);
        }

        $hashedCode = Cache::get($this->twoFactorCacheKey($user->id));
        if (! $hashedCode) {
            return back()->withErrors([
                'code' => 'Codigo expirado. Solicite um novo codigo.',
            ]);
        }

        if (! Hash::check((string) $validated['code'], (string) $hashedCode)) {
            Log::warning('security.saas_admin_2fa_failed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'code' => 'Codigo invalido.',
            ]);
        }

        Cache::forget($this->twoFactorCacheKey($user->id));
        $request->session()->forget('two_factor_user_id');
        $request->session()->regenerate();

        Auth::login($user);
        $request->session()->flash('show_intro_videos', true);

        return redirect()->route('admin.companies.index');
    }

    public function resendTwoFactor(Request $request)
    {
        $pendingUserId = (int) $request->session()->get('two_factor_user_id', 0);
        $user = User::query()->find($pendingUserId);

        if (! $user || $user->role !== 'saas_admin') {
            return redirect()->route('company.login')->withErrors([
                'login' => 'Sessao de verificacao expirou. Faca login novamente.',
            ]);
        }

        $this->issueTwoFactorCode($user, $request);

        return back()->with('status', 'Novo codigo enviado.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['company_id', 'company_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('company.login');
    }

    private function issueTwoFactorCode(User $user, Request $request): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put(
            $this->twoFactorCacheKey($user->id),
            Hash::make($code),
            now()->addMinutes(self::TWO_FACTOR_MINUTES)
        );

        try {
            Mail::raw(
                'Seu codigo 2FA do Ponto Colaborador e: '.$code.' (expira em '.self::TWO_FACTOR_MINUTES.' minutos).',
                function ($message) use ($user): void {
                    $message->to($user->email)->subject('Codigo de verificacao 2FA');
                }
            );
        } catch (\Throwable $e) {
            Log::error('security.saas_admin_2fa_mail_error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::notice('security.saas_admin_2fa_issued', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);
    }

    private function twoFactorCacheKey(int $userId): string
    {
        return '2fa:saas_admin:'.$userId;
    }

    private function redirectToHomeByRole(string $role)
    {
        return match ($role) {
            'saas_admin' => redirect()->route('admin.companies.index'),
            'company_operator' => redirect()->route('kiosk.index'),
            default => redirect()->route('dashboard.index'),
        };
    }
}
