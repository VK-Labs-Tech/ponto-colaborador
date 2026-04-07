<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Plan;
use App\Models\SubscriptionEvent;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionService
{
    public function getByCompanyId(int $companyId): ?CompanySubscription
    {
        return CompanySubscription::query()
            ->with('plan')
            ->where('company_id', $companyId)
            ->first();
    }

    public function createTrialForCompany(int $companyId, string $planCode = 'starter'): CompanySubscription
    {
        $plan = Plan::query()->where('code', $planCode)->where('is_active', true)->firstOrFail();

        $subscription = CompanySubscription::query()->create([
            'company_id' => $companyId,
            'plan_id' => $plan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7)->endOfDay(),
            'exports_period_start' => now()->startOfMonth()->toDateString(),
            'exports_used_in_period' => 0,
        ]);

        $this->recordEvent($subscription, 'trial_started', 'system', [
            'trial_ends_at' => optional($subscription->trial_ends_at)?->toDateTimeString(),
            'plan_code' => $plan->code,
        ]);

        return $subscription;
    }

    public function activate(int $companyId, int $months = 1, string $actor = 'saas_admin'): void
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription) {
            return;
        }

        $base = $subscription->current_period_ends_at && $subscription->current_period_ends_at->isFuture()
            ? $subscription->current_period_ends_at
            : now();

        $subscription->update([
            'status' => 'active',
            'trial_ends_at' => null,
            'current_period_ends_at' => $base->copy()->addMonths($months)->endOfDay(),
        ]);

        $subscription->refresh();
        $this->createInvoice($subscription, 'pending');
        $this->recordEvent($subscription, 'subscription_activated', $actor, [
            'months' => $months,
            'period_ends_at' => optional($subscription->current_period_ends_at)?->toDateTimeString(),
        ]);
    }

    public function markOverdue(int $companyId, string $actor = 'saas_admin'): void
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription) {
            return;
        }

        $subscription->update(['status' => 'overdue']);
        $this->recordEvent($subscription, 'subscription_overdue', $actor);
    }

    public function cancel(int $companyId, string $actor = 'saas_admin'): void
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription) {
            return;
        }

        $subscription->update(['status' => 'canceled']);
        $this->recordEvent($subscription, 'subscription_canceled', $actor);
    }

    public function changePlan(int $companyId, string $planCode, string $actor = 'saas_admin'): void
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription) {
            return;
        }

        $plan = Plan::query()->where('code', $planCode)->where('is_active', true)->firstOrFail();

        $subscription->update(['plan_id' => $plan->id]);
        $this->recordEvent($subscription, 'plan_changed', $actor, [
            'plan_code' => $plan->code,
            'plan_name' => $plan->name,
        ]);
    }

    public function extendTrial(int $companyId, int $days = 7, string $actor = 'saas_admin'): void
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription) {
            return;
        }

        $base = $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()
            ? $subscription->trial_ends_at
            : now();

        $subscription->update([
            'status' => 'trial',
            'trial_ends_at' => $base->copy()->addDays($days)->endOfDay(),
            'current_period_ends_at' => null,
        ]);

        $subscription->refresh();
        $this->recordEvent($subscription, 'trial_extended', $actor, [
            'days' => $days,
            'trial_ends_at' => optional($subscription->trial_ends_at)?->toDateTimeString(),
        ]);
    }

    public function processDailyLifecycle(): int
    {
        $affected = 0;

        $trialExpiredIds = CompanySubscription::query()
            ->where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->pluck('id');

        foreach ($trialExpiredIds as $subscriptionId) {
            $subscription = CompanySubscription::query()->find($subscriptionId);
            if (! $subscription) continue;

            $subscription->update(['status' => 'overdue']);
            $this->recordEvent($subscription, 'trial_expired_auto', 'system');
            $affected++;
        }

        $activeExpiredIds = CompanySubscription::query()
            ->where('status', 'active')
            ->whereNotNull('current_period_ends_at')
            ->where('current_period_ends_at', '<', now())
            ->pluck('id');

        foreach ($activeExpiredIds as $subscriptionId) {
            $subscription = CompanySubscription::query()->find($subscriptionId);
            if (! $subscription) continue;
            $subscription->update(['status' => 'overdue']);
            $this->recordEvent($subscription, 'subscription_overdue_auto', 'system');
            $affected++;
        }

        return $affected;
    }

    public function metrics(): array
    {
        $query = CompanySubscription::query();

        $activeCount = (clone $query)->where('status', 'active')->count();
        $trialCount = (clone $query)->where('status', 'trial')->count();
        $overdueCount = (clone $query)->where('status', 'overdue')->count();
        $canceledCount = (clone $query)->where('status', 'canceled')->count();

        $mrrCents = CompanySubscription::query()
            ->where('status', 'active')
            ->join('plans', 'plans.id', '=', 'company_subscriptions.plan_id')
            ->sum('plans.price_cents');

        return [
            'active_count' => $activeCount,
            'trial_count' => $trialCount,
            'overdue_count' => $overdueCount,
            'canceled_count' => $canceledCount,
            'mrr_brl' => number_format($mrrCents / 100, 2, ',', '.'),
        ];
    }

    public function canAccess(Company $company): bool
    {
        if (! $company->is_active) {
            return false;
        }

        $subscription = $this->getByCompanyId($company->id);
        if (! $subscription) {
            return false;
        }

        $this->syncStatusByDate($subscription);

        return in_array($subscription->status, ['trial', 'active'], true);
    }

    public function ensureCanCreateEmployee(int $companyId): bool
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        $count = Company::query()->findOrFail($companyId)->employees()->count();

        return $count < $subscription->plan->employee_limit;
    }

    public function ensureCanCreateUser(int $companyId): bool
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        $count = User::query()
            ->where('company_id', $companyId)
            ->whereIn('role', ['company_admin', 'company_editor', 'company_operator'])
            ->count();

        return $count < $subscription->plan->user_limit;
    }

    public function consumeExportQuota(int $companyId): bool
    {
        $subscription = $this->getByCompanyId($companyId);
        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        $periodStart = $subscription->exports_period_start;
        $periodStartCarbon = $periodStart ? Carbon::parse((string) $periodStart) : null;

        if (! $periodStartCarbon || $periodStartCarbon->lt(now()->startOfMonth())) {
            $subscription->update([
                'exports_period_start' => now()->startOfMonth()->toDateString(),
                'exports_used_in_period' => 0,
            ]);
            $subscription->refresh();
        }

        if ($subscription->exports_used_in_period >= $subscription->plan->monthly_export_limit) {
            return false;
        }

        $subscription->increment('exports_used_in_period');

        return true;
    }

    private function syncStatusByDate(CompanySubscription $subscription): void
    {
        if ($subscription->status === 'trial' && $subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            $subscription->update(['status' => 'overdue']);
            return;
        }

        if ($subscription->status === 'active' && $subscription->current_period_ends_at && $subscription->current_period_ends_at->isPast()) {
            $subscription->update(['status' => 'overdue']);
        }
    }

    private function recordEvent(CompanySubscription $subscription, string $event, string $actor, ?array $payload = null): void
    {
        SubscriptionEvent::query()->create([
            'company_id' => $subscription->company_id,
            'company_subscription_id' => $subscription->id,
            'event' => $event,
            'actor' => $actor,
            'payload' => $payload,
        ]);
    }

    private function createInvoice(CompanySubscription $subscription, string $status = 'pending'): void
    {
        $subscription->loadMissing('plan');

        SubscriptionInvoice::query()->create([
            'company_id' => $subscription->company_id,
            'company_subscription_id' => $subscription->id,
            'plan_id' => $subscription->plan_id,
            'status' => $status,
            'amount_cents' => (int) ($subscription->plan?->price_cents ?? 0),
            'reference_month' => now()->startOfMonth()->toDateString(),
            'due_at' => now()->addDays(5),
        ]);
    }
}
