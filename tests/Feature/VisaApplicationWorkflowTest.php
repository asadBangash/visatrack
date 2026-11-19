<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use App\Models\VisaApplication;
use App\Services\BarcodeService;
use App\Services\ConfirmationNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisaApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_confirmation_numbers_are_unique(): void
    {
        $service = app(ConfirmationNumberService::class);
        $numbers = [];
        for ($i = 0; $i < 5; $i++) {
            $numbers[] = $service->generateUnique();
        }
        $this->assertCount(5, array_unique($numbers));
    }

    public function test_admin_can_create_application_with_photo(): void
    {
        Storage::fake('local');
        $admin = $this->admin();
        $location = Location::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.applications.store'), [
            'applicant_name' => 'Jane Demo',
            'date_of_birth' => '1990-05-20',
            'place_of_birth' => 'Lahore, Pakistan',
            'gender' => 'female',
            'nationality' => 'Pakistan',
            'purpose_of_travel' => 'BUSINESS (B1)',
            'passport_number' => 'P1234567',
            'location_id' => $location->id,
            'photograph' => UploadedFile::fake()->image('applicant.jpg'),
        ]);

        $application = VisaApplication::query()->first();
        $this->assertNotNull($application);
        $response->assertRedirect(route('admin.applications.show', $application));
        $this->assertMatchesRegularExpression('/^AA\d{3}[A-Z0-9]{5}$/', $application->confirmation_number);
    }

    public function test_barcode_generation(): void
    {
        $barcode = app(BarcodeService::class)->pngBinary('AA005P4IWT');
        $this->assertNotEmpty($barcode);
        $this->assertStringStartsWith("\x89PNG", $barcode);
    }
}