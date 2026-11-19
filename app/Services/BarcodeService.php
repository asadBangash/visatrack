<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    public function pngBase64(string $confirmationNumber): string
    {
        $generator = new BarcodeGeneratorPNG;
        $png = $generator->getBarcode($confirmationNumber, $generator::TYPE_CODE_128, 3, 78);

        return base64_encode($png);
    }

    public function pngBinary(string $confirmationNumber): string
    {
        $generator = new BarcodeGeneratorPNG;

        return $generator->getBarcode($confirmationNumber, $generator::TYPE_CODE_128, 2, 60);
    }
}