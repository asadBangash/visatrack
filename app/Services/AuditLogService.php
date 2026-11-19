<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;

class AuditLogService
{
    public function log(?Authenticatable $user, string $action, ?string $subjectType = null, ?int $subjectId = null, array $metadata = []): void
    {
        AuditLog::query()->create([
            'user_id' => $user?->getAuthIdentifier(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);
    }
}