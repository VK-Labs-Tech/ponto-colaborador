<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\MirrorSnapshot;
use Illuminate\Console\Command;

class EnforceRetentionPolicyCommand extends Command
{
    protected $signature = 'compliance:enforce-retention {--years=5 : Minimum years to keep legal records}';

    protected $description = 'Remove compliance data older than legal retention window';

    public function handle(): int
    {
        $years = max((int) $this->option('years'), 5);
        $cutoff = now()->subYears($years);

        $deletedAudit = AuditLog::query()->where('occurred_at', '<', $cutoff)->delete();
        $deletedSnapshots = MirrorSnapshot::query()->where('created_at', '<', $cutoff)->delete();

        $this->info("Retention policy enforced. Deleted audit logs: {$deletedAudit}. Deleted snapshots: {$deletedSnapshots}.");

        return self::SUCCESS;
    }
}
