<?php

namespace App\Services;

use App\Models\User;
use App\Models\VisaApplication;
use App\Models\VisaStatusHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VisaApplicationService
{
    public function __construct(
        private readonly ConfirmationNumberService $confirmationNumberService,
        private readonly PassportLookupService $passportLookupService,
        private readonly SurnameVerificationService $surnameVerificationService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function create(array $data, UploadedFile $photo, User $admin): VisaApplication
    {
        return DB::transaction(function () use ($data, $photo, $admin) {
            $path = $photo->store('photographs', 'local');

            $override = $data['surname_verification_override'] ?? null;
            $surnameToken = $override
                ? $this->surnameVerificationService->normalizeToken($override)
                : $this->surnameVerificationService->tokenFromFullName($data['applicant_name']);

            $surnameHash = $this->surnameVerificationService->hashForToken($surnameToken);
            $passportHash = $this->passportLookupService->hash($data['passport_number']);

            $application = VisaApplication::query()->create([
                'confirmation_number' => $this->confirmationNumberService->generateUnique(),
                'applicant_name' => $data['applicant_name'],
                'date_of_birth' => $data['date_of_birth'],
                'place_of_birth' => $data['place_of_birth'],
                'gender' => $data['gender'],
                'nationality' => $data['nationality'],
                'purpose_of_travel' => $data['purpose_of_travel'],
                'completed_on' => $data['completed_on'] ?? now()->toDateString(),
                'required_documents' => $data['required_documents'] ?? null,
                'confirmation_address' => $data['confirmation_address'] ?? null,
                'passport_number' => $data['passport_number'],
                'passport_lookup_hash' => $passportHash,
                'surname_verification_hash' => $surnameHash,
                'surname_verification_override' => $override ? $this->surnameVerificationService->normalizeToken($override) : null,
                'photograph_path' => $path,
                'location_id' => $data['location_id'],
                'visa_type' => $data['visa_type'] ?? config('visa.default_visa_type', 'NIV'),
                'status' => VisaApplication::STATUS_APPLICATION_RECEIVED,
                'created_by' => $admin->id,
            ]);

            VisaStatusHistory::query()->create([
                'visa_application_id' => $application->id,
                'previous_status' => null,
                'new_status' => VisaApplication::STATUS_APPLICATION_RECEIVED,
                'changed_by' => $admin->id,
                'created_at' => now(),
            ]);

            $this->auditLogService->log($admin, 'visa.created', VisaApplication::class, $application->id, [
                'confirmation_number' => $application->confirmation_number,
            ]);

            return $application->load('location');
        });
    }

    public function updateApplicant(VisaApplication $application, array $data, ?UploadedFile $photo, User $admin): VisaApplication
    {
        return DB::transaction(function () use ($application, $data, $photo, $admin) {
            $updates = [
                'applicant_name' => $data['applicant_name'],
                'date_of_birth' => $data['date_of_birth'],
                'place_of_birth' => $data['place_of_birth'],
                'gender' => $data['gender'],
                'nationality' => $data['nationality'],
                'purpose_of_travel' => $data['purpose_of_travel'],
                'completed_on' => $data['completed_on'] ?? $application->completed_on,
                'required_documents' => $data['required_documents'] ?? null,
                'confirmation_address' => $data['confirmation_address'] ?? null,
                'passport_number' => $data['passport_number'],
                'passport_lookup_hash' => $this->passportLookupService->hash($data['passport_number']),
                'location_id' => $data['location_id'],
            ];

            if (array_key_exists('surname_verification_override', $data)) {
                $override = $data['surname_verification_override'];
                $token = $override ? $this->surnameVerificationService->normalizeToken($override) : $this->surnameVerificationService->tokenFromFullName($data['applicant_name']);
                $updates['surname_verification_override'] = $override ? $this->surnameVerificationService->normalizeToken($override) : null;
                $updates['surname_verification_hash'] = $this->surnameVerificationService->hashForToken($token);
            }

            if ($photo) {
                if ($application->photograph_path && Storage::disk('local')->exists($application->photograph_path)) {
                    Storage::disk('local')->delete($application->photograph_path);
                }
                $updates['photograph_path'] = $photo->store('photographs', 'local');
            }

            $application->update($updates);

            $this->auditLogService->log($admin, 'visa.updated', VisaApplication::class, $application->id, [
                'confirmation_number' => $application->confirmation_number,
            ]);

            return $application->fresh(['location']);
        });
    }
}