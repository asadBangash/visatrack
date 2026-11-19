<?php

namespace App\Services;

class PassportLookupService
{
    public function normalize(string $passport): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($passport)));
    }

    public function hash(string $passport): string
    {
        return hash_hmac('sha256', $this->normalize($passport), config('app.key'));
    }
}