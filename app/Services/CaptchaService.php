<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptchaService
{
    private const SESSION_KEY = 'visa_status_captcha';

    public function rotate(): string
    {
        $code = strtoupper(Str::random(5));
        Session::put(self::SESSION_KEY, $code);

        return $code;
    }

    public function current(): string
    {
        if (! Session::has(self::SESSION_KEY)) {
            return $this->rotate();
        }

        return Session::get(self::SESSION_KEY);
    }

    public function validate(?string $input): bool
    {
        $expected = Session::get(self::SESSION_KEY);
        if (! $expected || $input === null) {
            return false;
        }

        return strtoupper(trim($input)) === strtoupper($expected);
    }
}