<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use App\Models\VisaApplication;
use App\Models\VisaStatusHistory;
use App\Services\PassportLookupService;
use App\Services\SurnameVerificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VisaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', config('visa.demo_admin_email'))->first();
        if (! $admin) {
            return;
        }

        $location = Location::query()->where('display_name', 'like', '%PAKISTAN, ISLAMABAD%')->first()
            ?? Location::query()->first();

        if (! $location) {
            return;
        }

        $passportService = app(PassportLookupService::class);
        $surnameService = app(SurnameVerificationService::class);
        $photoPath = $this->ensureDemoPhoto();

        $records = [
            [
                'confirmation_number' => 'AA001SING1',
                'applicant_name' => 'Demo Applicant One',
                'passport_number' => 'SAMPLE001',
                'surname_override' => 'SINGH',
                'status' => VisaApplication::STATUS_APPLICATION_RECEIVED,
            ],
            [
                'confirmation_number' => 'AA002AHME2',
                'applicant_name' => 'Demo Applicant Two',
                'passport_number' => 'SAMPLE002',
                'surname_override' => 'AHMED',
                'status' => VisaApplication::STATUS_APPROVED,
            ],
            [
                'confirmation_number' => 'AA003MALI3',
                'applicant_name' => 'Demo Applicant Three',
                'passport_number' => 'SAMPLE003',
                'surname_override' => 'MALIK',
                'status' => VisaApplication::STATUS_ISSUED,
            ],
        ];

        $ds160Defaults = [
            'date_of_birth' => '1981-02-11',
            'place_of_birth' => 'Peshawar, Pakistan',
            'gender' => 'male',
            'nationality' => 'Pakistan',
            'purpose_of_travel' => 'DRIVER OCCUPATION (A2)',
            'completed_on' => '2025-10-15',
        ];

        foreach ($records as $record) {
            $surnameToken = $surnameService->normalizeToken($record['surname_override']);

            $application = VisaApplication::query()->updateOrCreate(
                ['confirmation_number' => $record['confirmation_number']],
                [
                    'applicant_name' => $record['applicant_name'],
                    ...$ds160Defaults,
                    'passport_number' => $record['passport_number'],
                    'passport_lookup_hash' => $passportService->hash($record['passport_number']),
                    'surname_verification_hash' => $surnameService->hashForToken($surnameToken),
                    'surname_verification_override' => $surnameToken,
                    'photograph_path' => $photoPath,
                    'location_id' => $location->id,
                    'visa_type' => 'NIV',
                    'status' => $record['status'],
                    'created_by' => $admin->id,
                ]
            );

            if ($application->statusHistories()->count() === 0) {
                VisaStatusHistory::query()->create([
                    'visa_application_id' => $application->id,
                    'previous_status' => null,
                    'new_status' => $record['status'],
                    'changed_by' => $admin->id,
                    'created_at' => $application->created_at,
                ]);
            }
        }
    }

    private function ensureDemoPhoto(): string
    {
        $relative = 'photographs/demo-applicant.png';
        if (! Storage::disk('local')->exists($relative)) {
            $img = imagecreatetruecolor(200, 240);
            $bg = imagecolorallocate($img, 210, 225, 240);
            imagefill($img, 0, 0, $bg);
            ob_start();
            imagepng($img);
            $binary = ob_get_clean();
            imagedestroy($img);
            Storage::disk('local')->put($relative, $binary);
        }

        return $relative;
    }
}