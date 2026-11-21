<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use App\Models\VisaApplication;
use App\Services\CaptchaService;
use App\Services\PassportLookupService;
use App\Services\SurnameVerificationService;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicVisaStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_lookup_returns_generic_error(): void
    {
        $this->seed(LocationSeeder::class);
        $location = Location::query()->first();
        $code = app(CaptchaService::class)->current();

        $response = $this->postJson(route('visa-status.check'), [
            'visa_type' => 'NIV',
            'location_id' => $location->id,
            'confirmation_number' => 'DEMO-NONE',
            'passport_number' => 'WRONG',
            'surname' => 'XXXXX',
            'captcha' => $code,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'The information you entered does not match our records. Please verify your details and try again.']);
    }

    public function test_valid_demo_record_returns_popup_payload(): void
    {
        $this->seed(LocationSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'password' => Hash::make('x')]);
        $location = Location::query()->where('display_name', 'PAKISTAN, ISLAMABAD')->first()
            ?? Location::query()->first();

        $passportService = app(PassportLookupService::class);
        $surnameService = app(SurnameVerificationService::class);

        VisaApplication::query()->create([
            'confirmation_number' => 'AA002AHME2',
            'applicant_name' => 'Demo Applicant Two',
            'date_of_birth' => '1981-02-11',
            'place_of_birth' => 'Peshawar, Pakistan',
            'gender' => 'male',
            'nationality' => 'Pakistan',
            'purpose_of_travel' => 'DRIVER OCCUPATION (A2)',
            'completed_on' => '2025-10-15',
            'passport_number' => 'SAMPLE002',
            'passport_lookup_hash' => $passportService->hash('SAMPLE002'),
            'surname_verification_hash' => $surnameService->hashForToken('AHMED'),
            'surname_verification_override' => 'AHMED',
            'photograph_path' => 'photographs/demo.png',
            'location_id' => $location->id,
            'visa_type' => 'NIV',
            'status' => VisaApplication::STATUS_APPROVED,
            'created_by' => $admin->id,
        ]);

        $code = app(CaptchaService::class)->current();

        $response = $this->postJson(route('visa-status.check'), [
            'visa_type' => 'NIV',
            'location_id' => $location->id,
            'confirmation_number' => 'AA002AHME2',
            'passport_number' => 'SAMPLE002',
            'surname' => 'AHMED',
            'captcha' => $code,
        ]);

        $response->assertOk();
        $response->assertJsonPath('popup.status', 'Approved');
    }

    public function test_popup_uses_custom_case_dates_when_set(): void
    {
        $this->seed(LocationSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'password' => Hash::make('x')]);
        $location = Location::query()->first();

        $passportService = app(PassportLookupService::class);
        $surnameService = app(SurnameVerificationService::class);

        VisaApplication::query()->create([
            'confirmation_number' => 'AA099DATE1',
            'applicant_name' => 'Date Demo',
            'date_of_birth' => '1981-02-11',
            'place_of_birth' => 'Karachi, Pakistan',
            'gender' => 'male',
            'nationality' => 'Pakistan',
            'purpose_of_travel' => 'TOUR',
            'completed_on' => '2025-11-19',
            'case_created_on' => '2026-08-19',
            'case_updated_on' => '2026-08-21',
            'passport_number' => 'DATED001',
            'passport_lookup_hash' => $passportService->hash('DATED001'),
            'surname_verification_hash' => $surnameService->hashForToken('DDEMO'),
            'surname_verification_override' => 'DDEMO',
            'photograph_path' => 'photographs/demo.png',
            'location_id' => $location->id,
            'visa_type' => 'NIV',
            'status' => VisaApplication::STATUS_ISSUED,
            'created_by' => $admin->id,
        ]);

        $code = app(CaptchaService::class)->current();

        $response = $this->postJson(route('visa-status.check'), [
            'visa_type' => 'NIV',
            'location_id' => $location->id,
            'confirmation_number' => 'AA099DATE1',
            'passport_number' => 'DATED001',
            'surname' => 'DDEMO',
            'captcha' => $code,
        ]);

        $response->assertOk();
        $response->assertJsonPath('popup.created', '19-Aug-2026');
        $response->assertJsonPath('popup.updated', '21-Aug-2026');
    }

    public function test_lookup_accepts_matching_name_token_for_surname_field(): void
    {
        $this->seed(LocationSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'password' => Hash::make('x')]);
        $location = Location::query()->where('display_name', 'PAKISTAN, LAHORE')->first()
            ?? Location::query()->first();

        $passportService = app(PassportLookupService::class);
        $surnameService = app(SurnameVerificationService::class);

        VisaApplication::query()->create([
            'confirmation_number' => 'AA148XC2MG',
            'applicant_name' => 'Assad Rehman',
            'date_of_birth' => '1981-02-11',
            'place_of_birth' => 'Peshawar, Pakistan',
            'gender' => 'male',
            'nationality' => 'Pakistan',
            'purpose_of_travel' => 'TOUR',
            'completed_on' => '2025-11-19',
            'passport_number' => 'RE9152302',
            'passport_lookup_hash' => $passportService->hash('RE9152302'),
            'surname_verification_hash' => $surnameService->hashForToken('REHMAN'),
            'photograph_path' => 'photographs/demo.png',
            'location_id' => $location->id,
            'visa_type' => 'NIV',
            'status' => VisaApplication::STATUS_APPLICATION_RECEIVED,
            'created_by' => $admin->id,
        ]);

        $code = app(CaptchaService::class)->current();

        $response = $this->postJson(route('visa-status.check'), [
            'visa_type' => 'NIV',
            'location_id' => $location->id,
            'confirmation_number' => 'AA148XC2MG',
            'passport_number' => 'RE9152302',
            'surname' => 'ASSAD',
            'captcha' => $code,
        ]);

        $response->assertOk();
        $response->assertJsonPath('popup.confirmation_number', 'AA148XC2MG');
    }
}