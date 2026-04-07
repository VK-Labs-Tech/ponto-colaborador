<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\SubscriptionService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn () => app(SubscriptionService::class)->processDailyLifecycle())
    ->name('subscriptions-process-lifecycle')
    ->dailyAt('00:10')
    ->withoutOverlapping();
