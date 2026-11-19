<?php

namespace App\Services;

use App\Models\VisaApplication;

class PublicVisaLookupService
{
    public function __construct(
        private readonly PassportLookupService $passportLookupService,
        private readonly SurnameVerificationService $surnameVerificationService,
    ) {}

    public function lookup(array $input): ?VisaApplication
    {
        $confirmation = strtoupper(trim($input['confirmation_number'] ?? ''));
        $visaType = strtoupper(trim($input['visa_type'] ?? ''));
        $locationId = (int) ($input['location_id'] ?? 0);
        $passportHash = $this->passportLookupService->hash($input['passport_number'] ?? '');
        $surnameInput = $this->surnameVerificationService->normalizeToken($input['surname'] ?? '');

        $application = VisaApplication::query()
            ->with('location')
            ->where('confirmation_number', $confirmation)
            ->where('visa_type', $visaType)
            ->where('location_id', $locationId)
            ->where('passport_lookup_hash', $passportHash)
            ->first();

        if (! $application) {
            return null;
        }

        if ($application->surname_verification_hash !== null) {
            if (! $this->surnameMatches($application, $surnameInput)) {
                return null;
            }
        }

        return $application;
    }

    private function surnameMatches(VisaApplication $application, ?string $surnameInput): bool
    {
        $inputHash = $this->surnameVerificationService->hashForToken($surnameInput);
        if ($inputHash === null) {
            return false;
        }

        if ($inputHash === $application->surname_verification_hash) {
            return true;
        }

        $tokens = preg_split('/\s+/u', trim($application->applicant_name)) ?: [];
        foreach ($tokens as $token) {
            if ($this->surnameVerificationService->hashForToken($token) === $inputHash) {
                return true;
            }
        }

        return false;
    }

    public function popupPayload(VisaApplication $application): array
    {
        $statusLabel = $application->statusLabel();

        $messages = [
            VisaApplication::STATUS_APPLICATION_RECEIVED => 'Your visa application case has been received and is awaiting further processing.',
            VisaApplication::STATUS_APPROVED => 'Your visa application is in final processing.',
            VisaApplication::STATUS_ISSUED => 'Your visa has been issued.',
        ];

        return [
            'status' => $statusLabel,
            'confirmation_number' => $application->confirmation_number,
            'created' => $application->created_at->format('d-M-Y'),
            'updated' => $application->updated_at->format('d-M-Y'),
            'message' => $messages[$application->status] ?? '',
            'location' => $application->location->display_name,
            'visa_type' => $application->visa_type,
        ];
    }
}