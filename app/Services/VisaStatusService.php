<?php

namespace App\Services;

use App\Models\User;
use App\Models\VisaApplication;
use App\Models\VisaStatusHistory;
use Illuminate\Support\Facades\DB;

class VisaStatusService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    public function changeStatus(VisaApplication $application, string $newStatus, User $admin): VisaApplication
    {
        $allowed = [
            VisaApplication::STATUS_APPLICATION_RECEIVED,
            VisaApplication::STATUS_APPROVED,
            VisaApplication::STATUS_ISSUED,
        ];

        if (! in_array($newStatus, $allowed, true)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        return DB::transaction(function () use ($application, $newStatus, $admin) {
            $previous = $application->status;
            if ($previous === $newStatus) {
                return $application;
            }

            $application->update(['status' => $newStatus]);

            VisaStatusHistory::query()->create([
                'visa_application_id' => $application->id,
                'previous_status' => $previous,
                'new_status' => $newStatus,
                'changed_by' => $admin->id,
                'created_at' => now(),
            ]);

            $this->auditLogService->log($admin, 'visa.status_changed', VisaApplication::class, $application->id, [
                'confirmation_number' => $application->confirmation_number,
                'previous_status' => $previous,
                'new_status' => $newStatus,
            ]);

            return $application->fresh();
        });
    }
}