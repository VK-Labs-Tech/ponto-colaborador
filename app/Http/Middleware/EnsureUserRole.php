<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    private const ROLE_ALIASES = [
        'admin' => ['company_admin'],
        'gestor' => ['company_editor', 'company_admin'],
        'colaborador' => ['company_operator', 'company_editor', 'company_admin'],
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('company.login');
        }

        $normalized = [];
        foreach ($roles as $role) {
            if (array_key_exists($role, self::ROLE_ALIASES)) {
                $normalized = [...$normalized, ...self::ROLE_ALIASES[$role]];
                continue;
            }
            $normalized[] = $role;
        }

        if (! in_array($user->role, array_values(array_unique($normalized)), true)) {
            abort(403);
        }

        return $next($request);
    }
}
