<?php

namespace App\Services;

use App\Models\VisaApplication;
class ConfirmationNumberService
{
    public function generateUnique(): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $digits = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $suffix = '';
            for ($i = 0; $i < 5; $i++) {
                $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $number = 'AA'.$digits.$suffix;
        } while (VisaApplication::query()->where('confirmation_number', $number)->exists());

        return $number;
    }
}