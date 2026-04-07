<?php

namespace App\Providers;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\MonthlyClosureRepositoryInterface;
use App\Repositories\Contracts\TimePunchRepositoryInterface;
use App\Repositories\Eloquent\AuditLogRepository;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\MonthlyClosureRepository;
use App\Repositories\Eloquent\TimePunchRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(TimePunchRepositoryInterface::class, TimePunchRepository::class);
        $this->app->bind(MonthlyClosureRepositoryInterface::class, MonthlyClosureRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || (bool) config('app.force_https', false)) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request) {
            $login = mb_strtolower((string) $request->input('login', ''));

            return [
                Limit::perMinute(6)
                    ->by($request->ip().'|'.$login)
                    ->response(function (Request $request, array $headers) use ($login) {
                        Log::warning('security.login_throttled', [
                            'ip' => $request->ip(),
                            'login' => $login,
                        ]);

                        return response('Muitas tentativas de login. Aguarde e tente novamente.', 429, $headers);
                    }),
                Limit::perMinute(20)
                    ->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        Log::warning('security.login_throttled_ip', [
                            'ip' => $request->ip(),
                        ]);

                        return response('Muitas tentativas de login. Aguarde e tente novamente.', 429, $headers);
                    }),
            ];
        });

        RateLimiter::for('kiosk-punch', function (Request $request) {
            $employeeId = (string) $request->input('employee_id', 'guest');
            $companyId = (string) session('company_id', '0');

            return [
                Limit::perMinute(20)
                    ->by($request->ip().'|'.$companyId)
                    ->response(function (Request $request, array $headers) use ($companyId) {
                        Log::warning('security.kiosk_throttled_company', [
                            'ip' => $request->ip(),
                            'company_id' => $companyId,
                        ]);

                        return response('Muitas tentativas de batida de ponto. Aguarde e tente novamente.', 429, $headers);
                    }),
                Limit::perMinute(8)
                    ->by($companyId.'|'.$employeeId)
                    ->response(function (Request $request, array $headers) use ($companyId, $employeeId) {
                        Log::warning('security.kiosk_throttled_employee', [
                            'ip' => $request->ip(),
                            'company_id' => $companyId,
                            'employee_id' => $employeeId,
                        ]);

                        return response('Muitas tentativas de batida de ponto. Aguarde e tente novamente.', 429, $headers);
                    }),
            ];
        });

        RateLimiter::for('2fa-verify', function (Request $request) {
            $key = (string) $request->session()->get('two_factor_user_id', 'guest');

            return Limit::perMinute(6)
                ->by($request->ip().'|'.$key)
                ->response(function (Request $request, array $headers) use ($key) {
                    Log::warning('security.2fa_verify_throttled', [
                        'ip' => $request->ip(),
                        'user_key' => $key,
                    ]);

                    return response('Muitas tentativas de codigo 2FA. Aguarde e tente novamente.', 429, $headers);
                });
        });

        RateLimiter::for('2fa-resend', function (Request $request) {
            $key = (string) $request->session()->get('two_factor_user_id', 'guest');

            return Limit::perMinutes(10, 3)
                ->by($request->ip().'|'.$key)
                ->response(function (Request $request, array $headers) use ($key) {
                    Log::warning('security.2fa_resend_throttled', [
                        'ip' => $request->ip(),
                        'user_key' => $key,
                    ]);

                    return response('Limite de reenvio de codigo atingido. Aguarde e tente novamente.', 429, $headers);
                });
        });
    }
}
