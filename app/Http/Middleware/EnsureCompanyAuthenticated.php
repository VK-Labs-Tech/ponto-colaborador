<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAuthenticated
{
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('company.login');
        }

        if (! in_array($user->role, ['company_admin', 'company_editor', 'company_operator'], true) || ! $user->company_id) {
            abort(403);
        }

        if (! $user->company || ! $this->subscriptionService->canAccess($user->company)) {
            return response()->view('billing.blocked', [
                'companyName' => $user->company?->name ?? 'Empresa',
                'status' => $user->company?->subscription?->status ?? 'overdue',
            ], 402);
        }

        if (! $request->session()->has('company_id')) {
            $request->session()->put('company_id', $user->company_id);
            $request->session()->put('company_name', $user->company?->name ?? 'Empresa');
        }

        return $next($request);
    }
}
