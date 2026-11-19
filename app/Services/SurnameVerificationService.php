<?php

namespace App\Services;

use App\Models\VisaApplication;

class SurnameVerificationService
{
    public function tokenFromFullName(string $fullName): ?string
    {
        $strategy = config('visa.surname_strategy', 'last_token');
        $normalized = trim(preg_replace('/\s+/u', ' ', $fullName));

        if ($normalized === '') {
            return null;
        }

        if ($strategy === 'last_token') {
            $parts = explode(' ', $normalized);
            $token = end($parts);

            return $this->normalizeToken($token);
        }

        return null;
    }

    public function normalizeToken(?string $token): ?string
    {
        if ($token === null || $token === '') {
            return null;
        }

        $alpha = preg_replace('/[^A-Za-z]/', '', $token);
        if ($alpha === null || $alpha === '') {
            return null;
        }

        return strtoupper(substr($alpha, 0, 5));
    }

    public function hashForToken(?string $token): ?string
    {
        $normalized = $this->normalizeToken($token);
        if ($normalized === null) {
            return null;
        }

        return hash_hmac('sha256', $normalized, config('app.key'));
    }

    public function resolveStoredToken(VisaApplication $application): ?string
    {
        if ($application->surname_verification_override) {
            return $this->normalizeToken($application->surname_verification_override);
        }

        if ($application->surname_verification_hash) {
            return null; // verify via hash only
        }

        return null;
    }
}