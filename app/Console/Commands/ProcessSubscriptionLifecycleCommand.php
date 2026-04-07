<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ProcessSubscriptionLifecycleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-lifecycle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa expiracao de trial e vencimento de assinaturas';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService): int
    {
        $affected = $subscriptionService->processDailyLifecycle();

        $this->info("Assinaturas processadas: {$affected}");

        return self::SUCCESS;
    }
}
