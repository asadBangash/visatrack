<?php

namespace App\Services;

use App\Models\VisaApplication;
use Barryvdh\DomPDF\Facade\Pdf;

class ConfirmationPdfService
{
    public function __construct(
        private readonly BarcodeService $barcodeService,
    ) {}

    public function download(VisaApplication $application)
    {
        $barcodeBase64 = $this->barcodeService->pngBase64($application->confirmation_number);

        return Pdf::loadView('confirmations.confirmation-pdf', [
            'application' => $application->load('location'),
            'barcodeBase64' => $barcodeBase64,
        ])->setPaper('a4');
    }
}