<?php

namespace App\Services;

use App\Repositories\Contracts\AuditLogRepositoryInterface;

class AuditLogService
{
    public function __construct(private readonly AuditLogRepositoryInterface $auditLogRepository)
    {
    }

    public function log(
        int $companyId,
        string $actor,
        string $event,
        string $entityType,
        ?int $entityId = null,
        ?array $payload = null
    ): void {
        $this->auditLogRepository->create([
            'company_id' => $companyId,
            'actor' => $actor,
            'event' => $event,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'payload' => $payload,
        ]);
    }
}
