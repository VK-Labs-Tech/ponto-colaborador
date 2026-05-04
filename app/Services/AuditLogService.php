<?php

namespace App\Services;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditLogService
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $auditLogRepository,
        private readonly Request $request
    ) {}

    public function log(
        int $companyId,
        string $actor,
        string $event,
        string $entityType,
        ?int $entityId = null,
        ?array $payload = null,
        ?array $before = null,
        ?array $after = null,
        ?string $reason = null,
        ?int $actorId = null,
        ?string $actorType = null,
        ?string $actorRole = null
    ): void {
        $this->auditLogRepository->create([
            'company_id' => $companyId,
            'actor' => $actor,
            'actor_id' => $actorId,
            'actor_type' => $actorType,
            'actor_role' => $actorRole,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'event' => $event,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'payload' => $payload,
            'before' => $before,
            'after' => $after,
            'reason' => $reason,
            'occurred_at' => Carbon::now(),
        ]);
    }
}
